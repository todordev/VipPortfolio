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
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class VipPortfolioViewProject extends JView {
    
    protected $state;
    protected $item;
    protected $form;
    
    /**
     * Display the view
     */
    public function display($tpl = null){
        
        $this->state= $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
        // Check for errors.
        if(count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        
        $extraImages = array();
        if($this->item->id) {
            $extraImages = VpHelper::getExtraImages($this->item->id);
        }
        $this->assignRef("extraImages", $extraImages);
        
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
        
        JRequest::setVar('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        
        JToolBarHelper::title($isNew ? JText::_('COM_VIPPORTFOLIO_PROJECT_ADD')
		                             : JText::_('COM_VIPPORTFOLIO_PROJECT_EDIT'), 'vip-projects-new');
		                             
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
	    
	    $option = JRequest::getCmd("option");
	    
	    // Add behaviors
		//JHtml::_('behavior.modal', 'a.vip-modal');
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
        
		$this->document->setTitle(JText::_('COM_VIPPORTFOLIO_PROJECT_ADMINISTRATION'));
        
        // Add styles
        $this->document->addStyleSheet(JURI::root() . 'media/'.$option.'/slimbox/css/slimbox.css');
        $this->document->addStyleSheet(JURI::root() . 'media/'.$option.'/js/messageclass/message.css');
        
        // Add JS libraries
        $this->document->addScript(JURI::root() . 'media/'.$option.'/js/trashable.js');
        $this->document->addScript(JURI::root() . 'media/'.$option.'/js/messageclass/message.js');
        $this->document->addScript(JURI::root() . 'media/'.$option.'/slimbox/slimbox.js');
        
		// Add scripts
		$this->document->addScript(JURI::root() . 'administrator/components/'.$option.'/models/forms/'.$this->getName().'.js');
		$this->document->addScript(JURI::root() . 'administrator/components/'.$option.'/views/'.$this->getName().'/submitbutton.js');
        
	}

}