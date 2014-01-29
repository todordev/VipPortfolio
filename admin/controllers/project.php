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
jimport("joomla.filesystem.path");

/**
 * Project controller class.
 *
 * @package		VipPortfolio
 * @subpackage	Components
 * @since		1.6
 */
class VipPortfolioControllerProject extends ITPrismControllerFormBackend {
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Project', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        
        $model = parent::getModel($name, $prefix, $config);
        
        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        
        // Set images folder
        $model->setImagesFolder(JPATH_ROOT .DIRECTORY_SEPARATOR. $params->get("images_directory", "images/vipportfolio"));
        
        return $model;
    }
    
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Initialize variables
        $msg     = "";
        
        // Gets the data from the form
        $data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id", 0, "int");
        
        // Redirect options
        $redirectOptions = array (
            "task"	 => $this->getTask(),
            "id"     => $itemId
        );
        
        $model   = $this->getModel();
        /** @var $model VipPortfolioModelProject */
        
        $form = $model->getForm($data, false);
        /** @var $form JForm */
        
        if (!$form) {
            throw new Exception($model->getError());
        }
        
        // Test for valid data.
        $validData = $model->validate($form, $data);
        
        // Check for validation errors.
        if($validData === false){
            $this->displayWarning($form->getErrors(), $redirectOptions);
            return;
        }
        
        try {
            
            // Get image
            $thumb   = $app->input->files->get('jform', array(), 'array');
            $thumb   = JArrayHelper::getValue($thumb, "thumb");
            
            $image   = $app->input->files->get('jform', array(), 'array');
            $image   = JArrayHelper::getValue($image, "image");
            
            // Upload image
            if(!empty($image['name']) OR !empty($thumb['name'])) {
            
                // Upload image
                $imageNames = array();
                if(!empty($image['name'])) {
                
                    // Image options
                    $options       = JArrayHelper::getValue($validData, "resize");
                
                    $imageNames    = $model->uploadImage($image, $options);
                    if(!empty($imageNames["image"])) {
                        $validData["image"] = $imageNames["image"];
                    }
                
                    if(!empty($imageNames["thumb"])) {
                        $validData["thumb"] = $imageNames["thumb"];
                    }
                
                }
                
                // Upload thumbnail
                if(!empty($thumb['name']) AND empty($validData["thumb"])) {
                
                    $thumbName    = $model->uploadThumb($thumb);
                    if(!empty($thumbName)) {
                        $validData["thumb"] = $thumbName;
                    }
                
                }
            
            }
            
            $redirectOptions["id"] = $model->save($validData);
            
        } catch (RuntimeException $e) {
            $this->displayWarning($e->getMessage(), $redirectOptions);
        } catch (Exception $e) {
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPPORTFOLIO_ERROR_SYSTEM'));
            
        }
        
        $this->displayMessage(JText::_('COM_VIPPORTFOLIO_PROJECT_SAVED'), $redirectOptions);
    
    }
    
    /**
     * Delete an image
     */
    public function removeImage(){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $itemId   = $app->input->get->getInt('id', 0);
        $type     = $app->input->get->getCmd('type');
        if(!$itemId){
            throw new Exception(JText::_('COM_VIPPORTFOLIO_ERROR_IMAGE_DOES_NOT_EXIST'));
        }
        
        // Redirect options
        $redirectOptions = array (
            "view"	 => "project",
            "id"     => $itemId
        );
        
        try {
            
            // Get the model
            $model = $this->getModel();
            /** @var $model VipPortfolioModelProject */
            
            $model->removeImage($itemId, $type);
            
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPPORTFOLIO_ERROR_SYSTEM'));
        }
        
        // Display message
        if(strcmp("thumb", $type) == 0) {
            $msg = JText::_('COM_VIPPORTFOLIO_THUMB_DELETED');
        } else {
            $msg = JText::_('COM_VIPPORTFOLIO_IMAGE_DELETED');
        }
        
        $this->displayMessage($msg, $redirectOptions);
    
    }
    
}