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

jimport('joomla.application.component.controller');

/**
 * Main controller
 *
 * @package		VipPortfolio
 * @subpackage	Components
 */
class VipPortfolioController extends JControllerLegacy {
    
    protected $option;
    
	public function __construct($config = array())	{
		parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
	}

	public function display($cachable = false, $urlparams = false) {

		$document = JFactory::getDocument();
		/** @var $document JDocumentHtml **/
		
		// Add component style
        $document->addStyleSheet('../media/'.$this->option.'/css/style.css');
        
        $viewName      = JFactory::getApplication()->input->getCmd('view', 'dashboard');
        JFactory::getApplication()->input->set("view", $viewName);

        parent::display();
        return $this;
	}

}