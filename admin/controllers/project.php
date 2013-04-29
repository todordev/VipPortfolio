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

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.backend');

/**
 * Project controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Vip Portfolio
 * @since		1.6
 */
class VipPortfolioControllerProject extends ITPrismControllerFormBackend {
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Project', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        
        $model = parent::getModel($name, $prefix, $config);
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Load the component parameters.
        $params       = JComponentHelper::getParams($this->option);
        
        // Extension parameters
        $model->imagesURI       = $params->get("images_directory", "images/vipportfolio");
        $model->imagesFolder    = JPATH_SITE . DIRECTORY_SEPARATOR. $params->get("images_directory", "images/vipportfolio");
        
        // Joomla! media extension parameters
        $mediaParams = JComponentHelper::getParams("com_media");
        
        // Media Manager parameters
        $model->uploadMime      = explode(",", $mediaParams->get("upload_mime"));
        $model->imageExtensions = explode(",", $mediaParams->get("image_extensions") );
        $model->uploadMaxSize   = $mediaParams->get("upload_maxsize");
        
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
        
        $thumb   = $app->input->files->get('jform', array(), 'array');
        $thumb   = JArrayHelper::getValue($thumb, "thumb");
        
        $image   = $app->input->files->get('jform', array(), 'array');
        $image   = JArrayHelper::getValue($image, "image");
        
        // Redirect options
        $redirectOptions = array (
            "task"	  => $this->getTask(),
            "item_id" => $itemId
        );
        
        $model = $this->getModel();
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
            $messages = $form->getErrors();
            $this->displayWarning($messages, $redirectOptions);
            return;
        }
        
        try{
            
            if(!empty($image['name']) OR !empty($thumb['name'])) {
                jimport('joomla.filesystem.folder');
                jimport('joomla.filesystem.file');
                jimport('joomla.filesystem.path');
                jimport('joomla.image.image');
                jimport('itprism.file.upload.image');                
            }
            
            // Upload image
            $imageNames = array();
            if(!empty($image['name'])) {
                
                // Image options
                $options = JArrayHelper::getValue($validData, "resize");
                
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
            
            $redirectOptions["item_id"] = $model->save($validData);
        
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            
            // Problem with uploading, so set a message and redirect to pages
            if($e->getCode() == 1001) {
                $this->displayWarning($e->getMessage(), $redirectOptions);
                return;
            } else { // System error
                throw new Exception(JText::_('COM_VIPPORTFOLIO_ERROR_SYSTEM'), 500);
            }
            
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
            "view"	  => "project",
            "item_id" => $itemId
        );
        
        try{
            
            // Get the model
            $model = $this->getModel();
            /** @var $model VipPortfolioModelProject */
            
            $model->removeImage($itemId, $type);
            
        } catch ( Exception $e ) {
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