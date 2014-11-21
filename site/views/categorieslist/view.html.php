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

class VipPortfolioViewCategoriesList extends JViewLegacy
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

    protected $category;
    protected $categoryId;
    protected $portfolio;
    protected $projectsView;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params     = $this->state->params;

        $this->projectsView = $app->input->get("projecs_view", "categorieslist", "string");

        // Parse parameters
        if (!empty($this->items)) {
            foreach ($this->items as $item) {
                $item->params = json_decode($item->params);
                if (!empty($item->params->image)) {
                    $item->image = $item->params->image;
                }
            }
        }

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
        $item->link        = VipPortfolioHelperRoute::getCategoryListViewRoute();
        $item->image_intro = VipPortfolioHelper::getCategoryImage($this->items);

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
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_VIPPORTFOLIO_CATEGORIES_DEFAULT_PAGE_TITLE'));
        }

        // Set page title
        $title = $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0)) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        $this->document->setTitle($title);

        // Meta Description
        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        // Meta keywords
        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        // JavaScript and Styles

        $view = JString::strtolower($this->getName());

        // Add template style
        $this->document->addStyleSheet('media/' . $this->option . '/categories/' . $view . '/style.css', 'text/css', null);

    }
}
