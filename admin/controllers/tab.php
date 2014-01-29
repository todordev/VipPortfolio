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
 * Facebook Tab controller
 *
 * @package		VipPortfolio
 * @subpackage	Components
 * @since		1.6
 */
class VipPortfolioControllerTab extends ITPrismControllerFormBackend {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Tab', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function save() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Gets the data from the form
        $data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id", 0, "int");
        
        // Redirect options
        $redirectOptions = array (
            "task"	 => $this->getTask(),
            "id"     => $itemId
        );
        
        $model = $this->getModel();
        /** @var $model VipPortfolioModelTab **/
        
        $form = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if (!$form) {
            throw new Exception($model->getError());
        }
        
        // Test for valid data.
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            $this->displayWarning($form->getErrors(), $redirectOptions);
            return;
        }
        
        // Check for installed tab in the system
        $pageId  = JArrayHelper::getValue($validData, "page_id", 0);
        $appId   = JArrayHelper::getValue($validData, "app_id");
        
        // If I want to add a new tab, but appID is used,
        // display error message.
        if(!$itemId AND $model->isInstalled($pageId, $appId)) {
            $this->displayWarning(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_APP_INSTALLED"), $redirectOptions);
            return;
        }
        
        try {
            
            // Get component parameters
            $params = JComponentHelper::getParams($this->option);
            
            $itemId = $model->save($validData);
            $item   = $model->getItem($itemId);
            
            if($validData["published"]) {
            
                // Install
                if(!$model->isInstalledFacebookTab($item, $params)) {
                    $model->installFacebookTab($item, $params);
                } else { // Update
                    $model->updateFacebookTab($item, $params);
                }
            
            } else {
                $model->uninstallFacebookTab($item, $params);
            }
            
            // Set item ID to redirect options
            $redirectOptions["id"] = $itemId;
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            $this->displayError(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK"), array("view" => "pages"));
            return;
        }
        
        $this->displayMessage(JText::_('COM_VIPPORTFOLIO_TAB_SAVED'), $redirectOptions);
    }
}