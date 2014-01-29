<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * CSS Editor controller class.
 *
 * @package		VipPortfolio
 * @subpackage	Components
 * @since		1.6
 */
class VipPortfolioControllerCssEditor extends JControllerLegacy {
    
    protected $option;
    
    public function __construct($config = array()) {
		parent::__construct($config);
		$this->option = JFactory::getApplication()->input->get("option");
	}
	
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'CssEditor', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    
    /**
     * Load a file
     */
    public function getfile(){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Initialize variables
        $msg         = "";
        $link        = "";
        
        // Get file ID
        $styleFile   = $app->getUserStateFromRequest($this->option.".csseditor.style_file", "style_file", 0, "int");
        $fileName    = VipPortfolioHelper::getStyleFile($styleFile);
        
         // Verify the file
        if(!$fileName OR !is_file($fileName)) {
            $response = array(
            	"success"  => false,
                "title"    => JText::_( 'COM_VIPPORTFOLIO_FAIL' ),
                "text"     => JText::sprintf('COM_VIPPORTFOLIO_ERROR_FILE_NOT_FOUND', $fileName)
            );
            
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
        
        try {
            $code = file_get_contents($fileName);
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPPORTFOLIO_ERROR_SYSTEM'));
        }
        
        echo $code;
        JFactory::getApplication()->close();
    }
    
    
}