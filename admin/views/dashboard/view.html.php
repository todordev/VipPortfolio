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

class VipPortfolioViewDashboard extends JViewLegacy {
    
    protected $option;
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        jimport("vipportfolio.version");
        $this->version = new VipPortfolioVersion();
        
        // Load ITPrism library version
        jimport("itprism.version");
        if(!class_exists("ITPrismVersion")) {
            $this->itprismVersion = JText::_("COM_VIPPORTFOLIO_ITPRISM_LIBRARY_DOWNLOAD");
        } else {
            $itprismVersion = new ITPrismVersion();
            $this->itprismVersion = $itprismVersion->getShortVersion();
        }
        
        // Add submenu
        VipPortfolioHelper::addSubmenu($this->getName());
        
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        JToolBarHelper::title(JText::_("COM_VIPPORTFOLIO_DASHBOARD"));
        
        JToolBarHelper::preferences('com_vipportfolio');
        JToolBarHelper::divider();
        
        // Help button
        $bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_VIPPORTFOLIO_HELP_URL'));
		
    }

	/**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar() {
		$this->sidebar = JHtmlSidebar::render();
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle(JText::_('COM_VIPPORTFOLIO_DASHBOARD'));
		
	}
	
}