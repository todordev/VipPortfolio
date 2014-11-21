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

class VipPortfolioViewLineal extends JViewLegacy
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

        $this->params     = $this->state->get("params");
        /** @var  $params Joomla\Registry\Registry */

        $this->item = JArrayHelper::getValue($this->items, 0);

        if (!$this->item) {
            throw new RuntimeException(JText::_("COM_VIPPORTFOLIO_ERROR_INVALID_PROJECT"));
        }

        // Extra Images
        $extraImages = array();
        if ($this->params->get("lineal_extra_image") and isset($this->item)) {

            $extraImages[$this->item->id] = VipPortfolioHelper::getExtraImages($this->item->id);

            $this->extraImages = $extraImages;
            $this->extraMax    = $this->params->get("lineal_extra_max");

        }

        // Open link target
        $this->openLink = 'target="' . $this->params->get("lineal_open_link", "_self") . '"';

        $this->imagesUri = JURI::root() . $this->params->get("images_directory", "images/vipportfolio") . "/";

        $this->prepareLightBox();
        $this->prepareDocument();

        // Events
        $offset = $this->state->get("list.start", null);

        $item              = new stdClass();
        $item->title       = $this->document->getTitle();
        $item->link        = VipPortfolioHelperRoute::getLinealViewRoute($this->categoryId, $offset);
        $item->image_intro = $this->imagesUri . $this->item->thumb;

        JPluginHelper::importPlugin('content');
        $dispatcher  = JEventDispatcher::getInstance();
        $this->event = new stdClass();

        $results                             = $dispatcher->trigger('onContentBeforeDisplay', array('com_vipportfolio.details', &$item, &$this->params, $offset));
        $this->event->onContentBeforeDisplay = trim(implode("\n", $results));

        $results                            = $dispatcher->trigger('onContentAfterDisplay', array('com_vipportfolio.details', &$item, &$this->params, $offset));
        $this->event->onContentAfterDisplay = trim(implode("\n", $results));

        parent::display($tpl);
    }

    protected function prepareLightBox()
    {
        $this->modal      = $this->params->get("lModal");
        $this->modalClass = VipPortfolioHelper::getModalClass($this->modal);

        switch ($this->modal) {

            case "duncan":

                JHTML::_('jquery.framework');
                JHtml::_('vipportfolio.lightbox_duncan');

                // Initialize lightbox
                $js = 'jQuery(document).ready(function(){
                      jQuery(".' . $this->modalClass . '").lightbox();
                });';
                $this->document->addScriptDeclaration($js);

                break;

            case "nivo": // Joomla! native

                JHTML::_('jquery.framework');
                JHtml::_('vipportfolio.lightbox_nivo');

                // Initialize lightbox
                $js = '
                jQuery(document).ready(function(){
                    jQuery(".' . $this->modalClass . '").nivoLightbox();
                });';
                $this->document->addScriptDeclaration($js);

                break;
        }

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

        //Escape strings for HTML output
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

        // Add the category name into breadcrumbs
        if ($this->params->get('cat_breadcrumbs')) {
            if (!empty($this->category->name)) {
                $pathway = $app->getPathway();
                $pathway->addItem($this->category->name);
            }
        }

        // JavaScript and Styles

        $view = JString::strtolower($this->getName());

        // Add template style
        $this->document->addStyleSheet('media/' . $this->option . '/projects/' . $view . '/style.css');

        // If tmpl is set that mean the user loads the page from Facebook
        // So we should Auto Grow the tab.
        $tmpl = $app->input->get("tmpl");
        if (!empty($tmpl)) {
            if ($this->params->get("fbpp_auto_grow")) {
                VipPortfolioHelper::facebookAutoGrow($this->document, $this->params);
            }
        }

    }
}
