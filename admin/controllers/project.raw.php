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

jimport('joomla.application.component.controllers');

/**
 * Project controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Vip Portfolio
 * @since		1.6
 */
class VipPortfolioControllerProject extends JController {
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Project', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        
        $option = JFactory::getApplication()->input->get("option");
        
        $model  = parent::getModel($name, $prefix, $config);
        
        // Load the component parameters.
        $params                 = JComponentHelper::getParams($option);
        
        // Extension parameters
        $model->imagesURI       = $params->get("images_directory", "images/vipportfolio");
        $model->imagesFolder    = JPATH_SITE . DIRECTORY_SEPARATOR. $params->get("images_directory", "images/vipportfolio");
        
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
     * Deletes Extra Image
     *
     */
    public function removeExtraImage() {
       
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Initialize variables
        $itemId  = $app->input->post->get("id");
        
        try {
            
            // Get the model
            $model = $this->getModel();
            $model->removeExtraImage($itemId);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }
        
        $response = array(
        	"success" => true,
            "title"=> JText::_( 'COM_VIPPORTFOLIO_SUCCESS' ),
            "text" => JText::_( 'COM_VIPPORTFOLIO_IMAGE_DELETED' ),
            "data" => array("item_id"=>$itemId)
        );
        
        echo json_encode($response);
    }
    
    public function addExtraImage() {
       
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Initialize variables
        $itemId  = $app->input->get->get("id");
        $files   = $app->input->files->get("file");
        
        if(!$files) {
            $response = array(
            	"success" => false,
                "title"=> JText::_( 'COM_VIPPORTFOLIO_FAIL' ),
                "text" => JText::_( 'COM_VIPPORTFOLIO_ERROR_FILE_UPLOAD' ),
            );
                
            echo json_encode($response);
            return;
        }
        
        try {
           
            // Get the model
            $model  = $this->getModel();
            $images = $model->uploadExtraImages($files);
            $images = $model->storeExtraImages($images, $itemId);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            
            // Problem with file uploading
            if($e->getCode() == 1001) {
                $response = array(
                	"success" => false,
                    "title"=> JText::_( 'COM_VIPPORTFOLIO_FAIL' ),
                    "text" => $e->getMessage(),
                );
                    
                echo json_encode($response);
                return;
            }
            
            throw new Exception($e->getMessage());
        }
        
        $response = array(
        	"success" => true,
            "title"=> JText::_( 'COM_VIPPORTFOLIO_SUCCESS' ),
            "text" => JText::_( 'COM_VIPPORTFOLIO_IMAGE_SAVED' ),
            "data" => $images
        );
        
        echo json_encode($response);
    }
    

}