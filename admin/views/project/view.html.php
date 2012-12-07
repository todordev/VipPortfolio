<?php
/**
 * @package      ITPrism Components
 * @subpackage   Vip Portfolio
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Portfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class VipPortfolioViewProject extends JView {
    
    protected $state;
    protected $item;
    protected $form;
    
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
    protected function addToolbar(){
        
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        $this->documentTitle= $isNew ? JText::_('COM_VIPPORTFOLIO_PROJECT_ADD')
                                      : JText::_('COM_VIPPORTFOLIO_PROJECT_EDIT');
                                      
        if(!$isNew) {
            JToolBarHelper::title($this->documentTitle, 'vip-projects-edit');
        } else {
            JToolBarHelper::title($this->documentTitle, 'vip-projects-new');
        }
		                             
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
	    
	    // Add behaviors
        JHtml::_('behavior.formvalidation');
        JHtml::_('behavior.tooltip');
        
		$this->document->setTitle($this->documentTitle);
        
        // Add styles
        $this->document->addStyleSheet('../media/'.$this->option.'/js/messageclass/message.css');
        
        // Add JS libraries
        $this->document->addScript('../media/'.$this->option.'/js/messageclass/message.js');
        $this->document->addScript('../media/'.$this->option.'/js/formupload/Request.File.js');
        $this->document->addScript('../media/'.$this->option.'/js/formupload/Form.MultipleFileInput.js');
        $this->document->addScript('../media/'.$this->option.'/js/formupload/Form.Upload.js');
        
		// Add scripts
		$this->document->addScript('../media/'.$this->option.'/js/admin/'.strtolower($this->getName()).'.js');
		$this->document->addScript('../media/'.$this->option.'/js/admin/helper.js');
		
	}

}