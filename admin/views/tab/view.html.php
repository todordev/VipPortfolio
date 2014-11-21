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

class VipPortfolioViewTab extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $state;
    protected $item;
    protected $form;

    protected $documentTitle;
    protected $option;

    protected $pageName;

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
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        $this->params = $this->state->get("params");

        $pageId         = $this->state->get("page_id");
        $this->pageName = VipPortfolioHelper::getFacebookPageName($pageId);

        // Prepare actions, behaviors, scritps and document
        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew               = ($this->item->id == 0);
        $this->documentTitle = $isNew ? JText::_('COM_VIPPORTFOLIO_TAB_ADD')
            : JText::_('COM_VIPPORTFOLIO_TAB_EDIT');

        JToolBarHelper::title($this->documentTitle);

        JToolBarHelper::apply('tab.apply');
        JToolBarHelper::save2new('tab.save2new');
        JToolBarHelper::save('tab.save');

        if (!$isNew) {
            JToolBarHelper::cancel('tab.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('tab.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        // Add behaviors
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

        JHtml::_('formbehavior.chosen', 'select');

        $isNew = ($this->item->id == 0);

        if (!$isNew) {
            $this->document->setTitle(JText::sprintf("COM_VIPPORTFOLIO_TAB_EDIT", $this->pageName));
        } else {
            $this->document->setTitle(JText::sprintf("COM_VIPPORTFOLIO_TAB_ADD", $this->pageName));
        }

        // Add scripts
        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}
