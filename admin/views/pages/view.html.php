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

class VipPortfolioViewPages extends JViewLegacy
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

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params = $this->state->get('params');

        // Include HTML helper
        JHtml::addIncludePath(JPATH_COMPONENT_SITE . '/helpers/html');

        // Prepare sorting data
        $this->prepareSorting();

        // Add submenu
        VipPortfolioHelper::addSubmenu($this->getName());

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

        $this->sortFields = array(
            'a.published' => JText::_('JSTATUS'),
            'a.title'     => JText::_('COM_VIPPORTFOLIO_TITLE'),
            'a.fans'      => JText::_('COM_VIPPORTFOLIO_FANS'),
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
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_VIPPORTFOLIO_FACEBOOK_PAGES'));

        // Facebook buttons
        if (!$this->params->get("fbpp_app_id") or !$this->params->get("fbpp_app_secret")) {

            $app = JFactory::getApplication();
            /** @var $app JApplicationAdministrator */

            // Add a message to the message queue
            $app->enqueueMessage(JText::_('COM_VIPPORTFOLIO_ERROR_FACEBOOK_MISSING_SETTINGS'), 'Notice');

        } else {

            $facebook = new Facebook(array(
                'appId'      => $this->params->get("fbpp_app_id"),
                'secret'     => $this->params->get("fbpp_app_secret"),
                'fileUpload' => false
            ));

            $facebookUserId = $facebook->getUser();

            JToolBarHelper::divider();
            if (!$facebookUserId) {
                JToolBarHelper::custom('pages.connect', "globe", "", JText::_("COM_VIPPORTFOLIO_CONNECT"), false);
            } else {
                JToolBarHelper::custom('pages.update', "refresh", "", JText::_("COM_VIPPORTFOLIO_UPDATE_ALL"), false);
            }


        }

        // Back button
        JToolBarHelper::divider();
        JToolBarHelper::custom('pages.backToDashboard', "dashboard", "", JText::_("COM_VIPPORTFOLIO_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIPPORTFOLIO_FACEBOOK_PAGES'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('bootstrap.tooltip');

        $this->document->addScript('../media/' . $this->option . '/js/admin/list.js');
    }
}
