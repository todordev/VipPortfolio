<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class VipPortfolioViewTabs extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

    protected $pageName;
    protected $pageId;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        $pageId = $this->state->get("page_id");
        if (!$pageId) {
            $msg = JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_INVALID_PAGE");
            $app->redirect(JRoute::_("index.php?option=com_vipportfolio&view=pages", false), $msg, "notice");

            return;
        }

        // Check for Facebook connect
        $facebook = new Facebook(array(
            'appId'      => $this->params->get("fbpp_app_id"),
            'secret'     => $this->params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));

        $facebookUserId = $facebook->getUser();
        if (!$facebookUserId) {
            $msg = JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_NOT_CONNECT");
            $app->redirect(JRoute::_("index.php?option=com_vipportfolio&view=pages", false), $msg, "notice");

            return;
        }

        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->pageId   = $pageId;
        $this->pageName = VipPortfolioHelper::getFacebookPageName($pageId);

        // Prepare sorting data
        $this->prepareSorting();

        // Add submenu
        VipPortfolioHelper::addSubmenu("pages");

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0) ? false : true;

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.published' => JText::_('JSTATUS'),
            'a.title'     => JText::_('COM_VIPPORTFOLIO_TITLE'),
            'a.app_id'    => JText::_('COM_VIPPORTFOLIO_APP_ID'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("archived" => false, "trash" => false)), 'value', 'text', $this->state->get('filter.state'), true)
        );

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolBarHelper::title(JText::sprintf('COM_VIPPORTFOLIO_FACEBOOK_TABS_MANAGER', $this->pageName));
        JToolBarHelper::addNew('tab.add');
        JToolBarHelper::editList('tab.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList("tabs.publish");
        JToolBarHelper::unpublishList("tabs.unpublish");
        JToolBarHelper::divider();
        JToolBarHelper::deleteList(JText::_("COM_VIPPORTFOLIO_DELETE_ITEMS_QUESTION"), "tabs.delete");

        // Back buttons
        JToolBarHelper::divider();

        // Add custom buttons
        $bar = JToolbar::getInstance('toolbar');

        // Back to projects
        $link = JRoute::_('index.php?option=com_vipportfolio&view=pages');
        $bar->appendButton('Link', 'arrow-left-3', JText::_("COM_VIPPORTFOLIO_BACK_TO_PAGES"), $link);

        JToolBarHelper::custom('pages.backToDashboard', "dashboard", "", JText::_("COM_VIPPORTFOLIO_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::sprintf('COM_VIPPORTFOLIO_FACEBOOK_TABS_MANAGER', $this->pageName));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('itprism.ui.joomla_list');
    }
}
