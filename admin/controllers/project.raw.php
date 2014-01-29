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

jimport('joomla.application.component.controllers');

/**
 * Project controller class.
 *
 * @package		VipPortfolio
 * @subpackage	Components
 * @since		1.6
 */
class VipPortfolioControllerProject extends JControllerLegacy {
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Project', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        
        $model  = parent::getModel($name, $prefix, $config);
        
        // Load the component parameters.
        $params = JComponentHelper::getParams("com_vipportfolio");
        
        // Set images folder
        $model->setImagesFolder(JPATH_ROOT .DIRECTORY_SEPARATOR. $params->get("images_directory", "images/vipportfolio"));
        $model->setImagesUri("../".$params->get("images_directory", "images/vipportfolio")."/");
        
        return $model;
    }
    
    /** 
     * Deletes Extra Image
     *
     */
    public function removeExtraImage() {
       
        // Initialize variables
        $itemId  = $this->input->post->get("id");
        
        jimport("itprism.response.json");
        $response    = new ITPrismResponseJson();
        
        try {
            
            // Get the model
            $model = $this->getModel();
            $model->removeExtraImage($itemId);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }
        
        $response
            ->setTitle(JText::_('COM_VIPPORTFOLIO_SUCCESS'))
            ->setText(JText::_('COM_VIPPORTFOLIO_IMAGE_DELETED'))
            ->setData(array("item_id"=>$itemId))
            ->success();
        
        echo $response;
        JFactory::getApplication()->close();
    }
    
    public function addExtraImage() {
       
        jimport("itprism.response.json");
        $response    = new ITPrismResponseJson();
        
        // Initialize variables
        $itemId      = $this->input->post->get("id");
        
        // Prepare the size of additional thumbnails
        $thumbWidth  = $this->input->post->get("thumb_width", 50);
        $thumbHeight = $this->input->post->get("thumb_height", 50);
        if($thumbWidth < 25 OR $thumbHeight < 25 ) {
            $thumbWidth = 50;
            $thumbHeight = 50;
        }
        
        $scale     = $this->input->post->get("thumb_scale", JImage::SCALE_INSIDE);
        
        $files     = $this->input->files->get("files");
        if(!$files) {
            
            $response
                ->setTitle(JText::_('COM_VIPPORTFOLIO_FAIL'))
                ->setText(JText::_('COM_VIPPORTFOLIO_ERROR_FILE_UPLOAD'))
                ->failure();
            
            echo $response;
            JFactory::getApplication()->close();
        }
        
        try {
           
            // Get the model
            $model  = $this->getModel();
            $images = $model->uploadExtraImages($files, $thumbWidth, $thumbHeight, $scale);
            $images = $model->storeExtraImage($images, $itemId);
            
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }
        
        $response
            ->setTitle(JText::_('COM_VIPPORTFOLIO_SUCCESS'))
            ->setText(JText::_('COM_VIPPORTFOLIO_IMAGE_SAVED'))
            ->setData($images)
            ->success();
        
        echo $response;
        JFactory::getApplication()->close();
    }
    

}