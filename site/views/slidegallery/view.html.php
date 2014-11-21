<?php
/**
 * @package      VipPortfolio
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class VipPortfolioViewSlideGallery extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state = null;

    protected $items = null;
    protected $pagination = null;

    protected $event = null;
    protected $option;
    protected $pageclass_sfx;

    protected $category;
    protected $categoryId;
    protected $portfolio;
    protected $item;
    protected $extraImages;
    protected $extraMax;
    protected $imagesUri;
    protected $openLink;
    protected $modal;
    protected $modalClass;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Check for valid category
        $this->categoryId = $app->input->getInt("id");
        $this->category   = null;

        if (!empty($this->categoryId)) {
            $this->category = VipPortfolioHelper::getCategory($this->categoryId);
            // Checking for published category
            if (!$this->category or !$this->category->published) {
                throw new Exception(JText::_("COM_VIPPORTFOLIO_ERROR_CATEGORY_DOES_NOT_EXIST"));
            }
        }

        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params = $this->state->get("params");

        $this->prepareDocument();

        // If tmpl is set that mean the user loads the page from Facebook
        // So we should Auto Grow the tab.
        $tmpl = $app->input->get("tmpl");
        if (!empty($tmpl)) {
            if ($this->params->get("fbpp_auto_grow")) {
                VipPortfolioHelper::facebookAutoGrow($this->document, $this->params);
            }
        }

        // Events
        JPluginHelper::importPlugin('content');
        $dispatcher = JEventDispatcher::getInstance();
        $offset     = 0;

        $item              = new stdClass();
        $item->title       = $this->document->getTitle();
        $item->link        = VipPortfolioHelperRoute::getSlideGalleryViewRoute($this->categoryId);
        $item->image_intro = VipPortfolioHelper::getIntroImage($this->category, $this->items);

        $this->event                         = new stdClass();
        $results                             = $dispatcher->trigger('onContentBeforeDisplay', array('com_vipportfolio.details', &$item, &$this->params, $offset));
        $this->event->onContentBeforeDisplay = trim(implode("\n", $results));

        $results                            = $dispatcher->trigger('onContentAfterDisplay', array('com_vipportfolio.details', &$item, &$this->params, $offset));
        $this->event->onContentAfterDisplay = trim(implode("\n", $results));

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function prepareDocument()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $menus = $app->getMenu();
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Set page heading
        if (!$this->params->get("page_heading")) {
            if (!empty($this->category) and !empty($this->category->name)) {
                $this->params->def('page_heading', $this->category->name);
            } else {
                if ($menu) {
                    $this->params->def('page_heading', $menu->title);
                } else {
                    $this->params->def('page_heading', JText::_('COM_VIPPORTFOLIO_DEFAULT_PAGE_TITLE'));
                }
            }
        }

        // Set page title
        if (!$this->category) { // Uncategorised
            // Get title from the page title option
            $title = $this->params->get("page_title");

            if (!$title) {
                $title = $app->get('sitename');
            }

        } else {

            $title = $this->category->title;

            if (!$title) {
                // Get title from the page title option
                $title = $this->params->get("page_title");

                if (!$title) {
                    $title = $app->get('sitename');
                }

            } elseif ($app->get('sitename_pagetitles', 0)) { // Set site name if it is necessary ( the option 'sitename' = 1 )
                $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);

            }

        }

        $this->document->setTitle($title);

        // Meta Description
        if (!$this->category) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($this->category->metadesc);
        }

        // Meta keywords
        if (!$this->category) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $this->category->metakey);
        }

        // Canonical URL
        if (!empty($this->category) and !empty($this->category->meta_canonical)) {
            $cLink = '<link href="' . $this->category->meta_canonical . '" rel="canonical"  />';
            $this->document->addCustomTag($cLink);
        }

        // Add the category name into breadcrumbs
        if ($this->params->get('cat_breadcrumbs')) {
            if (!empty($this->category->name)) {
                $pathway = $app->getPathway();
                $pathway->addItem($this->category->name);
            }
        }

        // Add script

        JHTML::_('jquery.framework');

        jimport("vipportfolio.slidegallery");
        VipPortfolioSlideGallery::load();
        $this->portfolio = new VipPortfolioSlideGallery($this->items, $this->params);

        // Set image path
        $imagesPath = JURI::root() . $this->params->get("images_directory", "images/vipportfolio") . "/";
        $this->portfolio->setImagesPath($imagesPath);

        $this->portfolio
            ->setSelector("vp-com-slidegallery")
            ->addScriptDeclaration($this->document);
    }
}
