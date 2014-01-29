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

jimport('itprism.controller.form.backend');

/**
 * CSS Editor controller class.
 *
 * @package		VipPortfolio
 * @subpackage	Components
 * @since		1.6
 */
class VipPortfolioControllerCssEditor extends ITPrismControllerFormBackend {
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'CssEditor', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    /**
     * Save an item
     *
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Initialize variables
        $msg     = "";
        
        // Redirect options
        $redirectOptions = array(
        	"view" => "csseditor"
        );
        
        // Gets the data from the form
        $styleCode   = $app->input->post->get('style_code', "string", "raw");
        $styleCode   = JString::trim($styleCode);

        // Get the path to file
        $styleFile   = $app->getUserStateFromRequest($this->option.".csseditor.style_file", "style_file", 0, "int");
        $fileName    = VipPortfolioHelper::getStyleFile($styleFile);
        
        $model       = $this->getModel();
        /** @var $model VipPortfolioModelCssEditor */
        
        // Check for errors.
        if(empty($fileName) OR !is_file($fileName)){
            $msg = JText::sprintf("COM_VIPPORTFOLIO_ERROR_ERROR_FILE_CANT_BE_SAVED", $fileName);
            $this->displayWarning($msg, $redirectOptions);
            return;
        }
        
        try {
            // Verify for enabled magic quotes
            if( get_magic_quotes_gpc() ) {
                $styleCode = stripcslashes($styleCode);
            }
            
            JFile::write($fileName, $styleCode);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPPORTFOLIO_ERROR_SYSTEM'));
        }
        
        // Prepare redirect options
        $task = $this->getTask();
        if(strcmp($task, "save") == 0) {
            $redirectOptions["view"] = "dashboard";
        }
        
        $this->displayMessage(JText::_('COM_VIPPORTFOLIO_STYLE_FILE_SAVED'), $redirectOptions);
    
    }
    
	/**
     * This method does cancel action 
     * and redirects to dashboard.
     */
    public function cancel(){
        
        $redirectOptions = array(
        	"view" => "dashboard"
        );
        
        $link = $this->prepareRedirectLink($redirectOptions);
        $this->setRedirect(JRoute::_($link, false));
    }
    
}