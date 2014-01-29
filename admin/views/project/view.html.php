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

class VipPortfolioViewProject extends JViewLegacy {
    
    protected $state;
    protected $item;
    protected $form;
    protected $params;
    
    protected $documentTitle;
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    /**
     * Display the view
     */
    public function display($tpl = null){
        
        $this->state  = $this->get('State');
        $this->item   = $this->get('Item');
        $this->form   = $this->get('Form');

        $this->params = $this->state->get("params");
        
        $extraImages = array();
        if($this->item->id) {
            $extraImages = VipPortfolioHelper::getExtraImages($this->item->id);
        }
        $this->extraImages = $extraImages;
        
        $this->addToolbar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        $this->documentTitle = $isNew ? JText::_('COM_VIPPORTFOLIO_PROJECT_ADD')
                                      : JText::_('COM_VIPPORTFOLIO_PROJECT_EDIT');

        JToolBarHelper::title($this->documentTitle);
        
        JToolBarHelper::apply('project.apply');
        JToolBarHelper::save2new('project.save2new');
        JToolBarHelper::save('project.save');
    
        if(!$isNew){
            JToolBarHelper::cancel('project.cancel', 'JTOOLBAR_CANCEL');
        }else{
            JToolBarHelper::cancel('project.cancel', 'JTOOLBAR_CLOSE');
        }
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
	    $this->document->setTitle($this->documentTitle);
        
	    // Load language string in JavaScript 
        JText::script('COM_VIPPORTFOLIO_CHOOSE_FILE');
        JText::script('COM_VIPPORTFOLIO_REMOVE');
        
        // Script
        JHtml::_('behavior.framework');
        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');
        JHtml::_('behavior.tooltip');
        
        JHtml::_('bootstrap.framework');
        JHtml::_('formbehavior.chosen', 'select');
        
        JHtml::_('itprism.ui.pnotify');
        JHtml::_('itprism.ui.bootstrap_filestyle');
        JHtml::_('itprism.ui.fileupload');
        
		$this->document->addScript('../media/'.$this->option.'/js/admin/'.strtolower($this->getName()).'.js');
		$this->document->addScript('../media/'.$this->option.'/js/admin/helper.js');
		
	}

}