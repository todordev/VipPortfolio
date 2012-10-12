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

jimport('joomla.application.component.controllerform');

/**
 * Project controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Vip Portfolio
 * @since		1.6
 */
class VipPortfolioControllerProject extends JControllerForm {
    
    // Check the table in so it can be edited.... we are done with it anyway
    private $defaultLink = 'index.php?option=com_vipportfolio';
    
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
        
        // Get values from the user state
        $resizeImage = $app->input->getInt('resize_image', 0);
        $app->setUserState($this->option.'.project.resize_image', $resizeImage);
       
        $imageWidth = $app->input->getInt('image_width');
        $app->setUserState($this->option.'.project.image_width', $imageWidth);
        
        $imageHeight = $app->input->getInt('image_height');
        $app->setUserState($this->option.'.project.image_height', $imageHeight);
        
        // Thumbnail size
        $thumbWidth = $app->input->getInt('thumb_width', 200);
        $app->setUserState($this->option.'.project.thumb_width', $thumbWidth);
        
        $thumbHeight = $app->input->getInt('thumb_height', 300);
        $app->setUserState($this->option.'.project.thumb_height', $thumbHeight);
        
        $model->resizeImage         = $resizeImage;
        $model->imageWidth          = $imageWidth;
        $model->imageHeight         = $imageHeight;
        $model->thumbWidth          = $thumbWidth;
        $model->thumbHeight         = $thumbHeight;
        
        // Extra image thumbnail size
        $model->extraThumbWidth     = $params->get("extra_image_thumb_width", 50);
        $model->extraThumbHeight    = $params->get("extra_image_thumb_height", 50);
        
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
     *
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Initialize variables
        $msg     = "";
        $link    = "";
        
        // Gets the data from the form
        $data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id", 0, "int");
        $model   = $this->getModel();
        /** @var $model VipPortfolioModelProject */
        
        $form = $model->getForm($data, false);
        /** @var $form JForm */
        
        if (!$form) {
            throw new Exception($model->getError());
        }
        
        try{
            
            // Test for valid data.
            $validData = $model->validate($form, $data);
            
            // Check for validation errors.
            if($validData === false){
                $this->defaultLink .= "&view=".$this->view_item."&layout=edit";
            
                if($itemId) {
                    $this->defaultLink .= "&id=" . $itemId;
                } 
                
                $this->setMessage($model->getError(), "notice");
                $this->setRedirect(JRoute::_($this->defaultLink, false));
                return;
            }
            
            $itemId = $model->save($validData);
        
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('ITP_ERROR_SYSTEM'));
        }
        
        $msg  = JText::_('COM_VIPPORTFOLIO_PROJECT_SAVED');
        $link = $this->prepareRedirectLink($itemId);
        
        $this->setRedirect(JRoute::_($link, false), $msg);
    
    }
    
	/**
     * 
     * Prepare redirect link. 
     * If has clicked apply, will be redirected to edit form and will be loaded the item data
     * If has clicked save2new, will be redirected to edit form, and you will be able to add a new record
     * If has clicked save, will be redirected to the list of items
     *
     * @param integer $itemId 
     */
    protected function prepareRedirectLink($itemId = 0) {
        
        $task = $this->getTask();
        $link = $this->defaultLink;
        
        // Prepare redirection
        switch($task) {
            case "apply":
                $link .= "&view=".$this->view_item."&layout=edit";
                if(!empty($itemId)) {
                    $link .= "&id=" . (int)$itemId; 
                }
                break;
                
            case "save2new":
                $link .= "&view=".$this->view_item."&layout=edit";
                break;
                
            default:
                $link .= "&view=".$this->view_list;
                break;
        }
        
        return $link;
    }
    
    /**
     * Delete an image
     *
     */
    public function removeImage(){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $id   = $app->input->get->getInt('id', 0);
        $type = $app->input->get->get('type', "string");
        if(!$id){
            throw new Exception(JText::_('ITP_ERROR_IMAGE_DOES_NOT_EXIST'));
        }
        
        try{
            
            // Get the model
            $model = $this->getModel();
            /** @var $model VipPortfolioModelProject */
            
            $model->removeImage($id, $type);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('ITP_ERROR_SYSTEM'));
        }
        
        $msg = JText::_('COM_VIPPORTFOLIO_IMAGE_DELETED');
        $this->setRedirect(JRoute::_($this->defaultLink."&view=".$this->view_item."&layout=edit&id=" . (int)$id, false), $msg);
    
    }
    
    /**
     * Cancel operations
     *
     */
    public function cancel(){
        $this->setRedirect(JRoute::_($this->defaultLink . "&view=".$this->view_list, false));
    }
    
}