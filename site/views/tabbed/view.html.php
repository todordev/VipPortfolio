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

jimport('joomla.html.html.bootstrap');

class VipPortfolioViewTabbed extends JViewLegacy
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
    protected $projects;
    protected $imagesUrl;
    protected $activeTab;
    protected $displayCaption;
    protected $openLink;
    protected $modal;
    protected $modalClass;
    protected $projectsView;

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
        $this->state  = $this->get('State');
        $this->items  = $this->get('Items');
        $this->params = $this->state->params;

        $this->projectsView = $app->input->get("projecs_view", "tabbed", "string");

        // Parse parameters and collect categories ids in array
        $categories = array();
        if (!empty($this->items)) {
            foreach ($this->items as &$item) {
                $item->params = json_decode($item->params);
                if (!empty($item->params->image)) {
                    $item->image = $item->params->image;
                }

                $categories[] = $item->id;
            }
        }

        // Get projects for these categories.
        // We need only published projects.
        $published = 1;
        $projects_ = VipPortfolioHelper::getProjects($categories, $published);

        $projects = array();
        foreach ($projects_ as &$item) {
            $projects[$item['catid']][] = $item;
        }

        $this->projects = $projects;

        $this->imagesUrl      = JURI::root() . $this->params->get("images_directory", "images/vipportfolio") . "/";
        $this->activeTab      = $this->params->get("tabbed_active_tab");
        $this->displayCaption = false;

        // Open link target
        $this->openLink = 'target="' . $this->params->get("tabbed_open_link", "_self") . '"';

        $this->prepareLightBox();
        $this->prepareDocument();

        // Events
        $offset = 0;

        $item              = new stdClass();
        $item->title       = $this->document->getTitle();
        $item->link        = VipPortfolioHelperRoute::getTabbedViewRoute();
        $item->image_intro = VipPortfolioHelper::getCategoryImage($this->items);

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
        $this->modal      = $this->params->get("tabbed_modal");
        $this->modalClass = VipPortfolioHelper::getModalClass($this->modal);

        $this->setLayout($this->modal);

        switch ($this->modal) {

            case "duncan":

                JHTML::_('jquery.framework');
                JHtml::_('vipportfolio.lightbox_duncan');

                // Initialize lightbox
                $js = "jQuery(document).ready(function(){\n";
                $js .= "jQuery('." . $this->modalClass . "').lightbox();\n";
                $js .= "});";

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
     * Prepare the document
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


        // Scripts
        JHTML::_('jquery.framework');

        if ($this->params->get("tabbed_display_tip", 0)) {
            JHTML::_('bootstrap.tooltip');
        }

        if ($this->params->get("tabbed_caption_title", 0) or $this->params->get("tabbed_caption_desc", 0) or $this->params->get("tabbed_caption_url", 0)) {
            $this->displayCaption = true;
        }
        // Load captionjs script.
        if ($this->displayCaption) {
            JHTML::_('vipportfolio.jsquares');

            $js = '';

            $this->document->addScriptDeclaration($js);
        }

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
