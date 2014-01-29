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

class VipPortfolioViewCssEditor extends JViewLegacy {
    
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
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        if( get_magic_quotes_gpc() ) {
            $app->enqueueMessage(JText::_('COM_VIPPORTFOLIO_WARNING_MAGIC_QUOTES'), 'notice');
        }
        
        // Get selected file
        $this->styleFile = $app->getUserStateFromRequest($this->option.".csseditor.style_file", "style_file", 0, "int");
        
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

        $this->documentTitle = JText::_('COM_VIPPORTFOLIO_CSS_EDITOR');
                                      
        JToolBarHelper::title($this->documentTitle);
		                             
        JToolBarHelper::apply('csseditor.apply');
        JToolBarHelper::save('csseditor.save');
    
        JToolBarHelper::cancel('csseditor.cancel', 'JTOOLBAR_CLOSE');
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
	    $this->document->setTitle($this->documentTitle);
	    
	    // Styles
        $this->document->addStyleSheet('../media/'.$this->option.'/js/codemirror/lib/codemirror.css');
        
	    // Scripts
	    JHtml::_('behavior.keepalive');
	    JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
        
        JHtml::_('bootstrap.framework');
        JHtml::_('formbehavior.chosen', 'select');
        
        $this->document->addScript('../media/'.$this->option.'/js/codemirror/lib/codemirror.js');
        $this->document->addScript('../media/'.$this->option.'/js/codemirror/mode/css/css.js');
        JHtml::_('itprism.ui.pnotify');
        
        $this->document->addScript('../media/'.$this->option.'/js/admin/helper.js');
		$this->document->addScript('../media/'.$this->option.'/js/admin/'.strtolower($this->getName()).'.js');
        
	}

}