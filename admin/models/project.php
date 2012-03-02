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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

/**
 * It is a project model
 * 
 * @author Todor Iliev
 * @todo gets the destination dir from parameters
 */
class VipPortfolioModelProject extends JModelAdmin {
    
    private $imagesDir = "";
    
    public $imageTypes = array();
    
    /**
     * Thumbnail width
     * @var integer
     */
    public $thumbWidth = 200;
    
    /**
     * Thumbnail height
     * @var integer
     */
    public $thumbHeight = 150;
    
    /**
     * Image width
     * @var integer
     */
    public $imageWidth = 800;
    
    /**
     * Image height
     * @var integer
     */
    public $imageHeight = 600;
    
    /**
     * @var     string  The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_VIPPORTFOLIO';
    
    /**
     * Constructor.
     *
     * @param   array   $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array()){
        parent::__construct($config);
        
        $this->imagesDir = JPATH_SITE . DS . "media" . DS . "vipportfolio";
        
        $this->imageTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
    }
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Project', $prefix = 'VipPortfolioTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array   $data       An optional array of data for the form to interogate.
     * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true){
        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Get the form.
        $form = $this->loadForm('com_vipportfolio.project', 'project', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
            return false;
        }
        
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_vipportfolio.edit.project.data', array());
        
        if(empty($data)){
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    /**
     * Save project data into the DB
     * 
     * @param $data   The data about project
     * 
     * @return     ID of the project
     */
    public function save($data){
        
        $title = JArrayHelper::getValue($data,"title");
        if(!$title){
            throw new ItpUserException(JText::_('ITP_ERROR_INVALID_TITLE'), 404);
        }
        
        $id = JArrayHelper::getValue($data,"id");
        $catid = JArrayHelper::getValue($data,"catid");
        $url = JArrayHelper::getValue($data,"url");
        $published = JArrayHelper::getValue($data,"published");
        $description = JArrayHelper::getValue($data,"description");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("title", $title);
        $row->set("description", $description);
        $row->set("url", $url);
        $row->set("catid", $catid);
        
        // Save image
        $images = $this->saveImages();
        
        // Set the thumbnail
        if(!empty($images['thumb'])){
            
            // Delete old image if I upload the new one
            if(!empty($row->thumb)){
                    // Remove an image from the filesystem
                    $file = $this->imagesDir.DS.$row->thumb;
                    
                    JFile::delete($file);
            }
            
            $row->set("thumb", $images['thumb']);
        
        }
        
        // Sets the images
        if(!empty($images['image'])){
            
            // Delete old image if I upload the new one
            if(!empty($row->image)){
                // Remove an image from the filesystem
                $file = $this->imagesDir.DS.$row->image;
                JFile::delete($file);
            }
            
            $row->set("image", $images['image']);
        
        }
        
        $row->set("published", $published);
        
        $row->store();
        
        // Adding extra images
        $extraImages = $this->uploadExtraImage();
        
        if(!empty($extraImages)){
            $this->storeExtraImage($extraImages, $row->id);
        }
        
        return $row->id;
    
    }
    
    /**
     * Delete records
     *
     * @param array $cids Rows Ids
     */
    public function delete($cids){
        
        if(!$cids){
            throw new ItpUserException(JText::_('ITP_ERROR_INVALID_ITEMS_SELECTED'), 404);
        }
        
        $tableProjects = $this->_db->nameQuote('#__vp_projects');
        $columnThumb = $this->_db->nameQuote('thumb');
        $columnImage = $this->_db->nameQuote('image');
        $columnId = $this->_db->nameQuote('id');
        
        /** Loads images **/
        $query = "
             SELECT
                $columnThumb,
                $columnImage 
             FROM
                 $tableProjects
             WHERE
                 $columnId IN (" . implode(",", $cids) . ")";
        
        $this->_db->setQuery($query);
        $rows = $this->_db->loadObjectList();
        
        $this->removeExtraImages($cids);
        
        /** Delete images **/
        foreach($rows as $images){
            if(!empty($images->thumb)){
                $file = $this->imagesDir.DS.$images->thumb; 
                if(JFile::exists($file)) {
                    JFile::delete($file);
                }
            }
            if(!empty($images->image)){
                $file = $this->imagesDir.DS.$images->image; 
                if(JFile::exists($file)) {
                    JFile::delete($file);
                }    
            }
            $file = "";
        }
        
        // Delete records 
        $query = "
			DELETE  
			FROM 
			     $tableProjects 
			WHERE   
			     $columnId IN ( " . implode(",", $cids) . " )";
        
        $this->_db->setQuery($query);
        
        if(!$this->_db->query()){
            throw new ItpException($this->_db->getErrorMsg(), 500);
        }
    
    }
    
    /**
     * Delete an image
     *
     * @param integer Project id
     * @param string  Shows the type of image - the thumbnail or the original image
     * @exception ItpException
     */
    public function removeImage($id, $type){
        
        if(!$id OR !$type){
            throw new ItpException(JText::_('ITP_ERROR_RECORDS_DO_NOT_EXIST'), 500);
        }
        
        $row = $this->getTable();
        $row->set("id", $id);
        $row->load();
        
        if(strcmp("thumb", $type) == 0){
            
            // Remove an image from the filesystem
            $file = $this->imagesDir.DS.$row->thumb;
            JFile::delete($file);
            
            // Remove the image from the DB
            $row->set("thumb", "");
        }
        
        if(strcmp("image", $type) == 0){
            
            // Remove an image from the filesystem
            $file = $this->imagesDir.DS.$row->image;
            JFile::delete($file);
            
            // Remove the image from the DB
            $row->set("image", "");
        }
        
        $row->store();
    
    }
    
	/**
     * Delete all additionl images
     *
     * @param array Projects IDs
     * @exception ItpException
     */
    public function removeExtraImages($projectsId){
        
        $db = JFactory::getDbo();
        
        $tableImages      = $this->_db->nameQuote('#__vp_images');
        $columnName       = $this->_db->nameQuote('name');
        $columnProjectsId = $this->_db->nameQuote('projects_id');
        
        $query = "
            SELECT
                $columnName
            FROM
                $tableImages
            WHERE
                $columnProjectsId IN (" . implode(",",$projectsId) . ")";
        
        $db->setQuery($query);
        $fileNames = $db->loadColumn();
        
        if(!$fileNames){
            return;
        }
        
        foreach($fileNames as $name) {
            
            $file = $this->imagesDir.DS.$name;
            if(JFile::exists($file)) {
                JFile::delete($file);
            }
            
            $file = $this->imagesDir.DS. "ethumb_".$name;
            if(JFile::exists($file)) {
                JFile::delete($file);
            }
            
        }
        
        $query = "
            DELETE
            FROM
                `#__vp_images`
            WHERE
                `projects_id` IN (" . implode(",",$projectsId) . ")";
        $db->setQuery($query);
        
        if(!$db->query()){
            throw new ItpException(JText::_('ITP_ERROR_SYSTEM'), 500);
        }
    
    }
    
    /**
     * Only delete an additionl image
     *
     * @param integer Image ID
     * @exception ItpException
     */
    public function removeExtraImage($id){
        
        if(!$id){
            throw new ItpException(JText::_('ITP_ERROR_RECORDS_DO_NOT_EXIST'), 500);
        }
        
        $db = JFactory::getDbo();
        
        $query = "
            SELECT
                `name`
            FROM
                `#__vp_images`
            WHERE
                `id`=" . (int)$id;
        
        $db->setQuery($query,0,1);
        $name = $db->loadResult();
        
        if(!empty($name)){
            
            // Remove an image from the filesystem
            $file = $this->imagesDir.DS.$name;
            JFile::delete($file);
            
            $file = $this->imagesDir.DS. "ethumb_".$name;
            JFile::delete($file);
            
            $query = "
	            DELETE
	            FROM
	                `#__vp_images`
	            WHERE
	                `id`=" . (int)$id . "
	            LIMIT 1";
            
            $db->setQuery($query);
            
            if(!$db->query()){
                throw new ItpException(JText::_('ITP_ERROR_SYSTEM'), 500);
            }
        }
    
    }
    
    /**
     * Saves the images
     * 
     * @throws ItpException 
     */
    public function saveImages(){
        
        jimport('joomla.filesystem.folder');
        
        if(!JFolder::exists($this->imagesDir)){
            if(!VpHelper::createFolder($this->imagesDir)) {
                throw new ItpException(JText::sprintf("ITP_ERROR_CANNOT_CREATE_FOLDER",$this->imagesDir), 500);
            }
        }
        
        jimport('joomla.filesystem.path');
        
        $names = array("thumb" => "", "image" => "");
        
        $uploadedFile = JRequest::getVar('jform', '', 'files', 'array');
        
        /************* Save Thumb ************/
        // check for error
        $error = JArrayHelper::getValue($uploadedFile['error'], 'thumb');
        $this->checkUploadErrors($error);
        
        // Upload thumb
        if(!empty($uploadedFile['name']['thumb'])){
//            $options = array("width" => $this->imageWidth, "height"=>$this->imageHeight, "type" => "crop", "startX"=>0, "startY"=>0);
            $names['thumb'] = $this->uploadImage($uploadedFile['tmp_name']['thumb'],$uploadedFile['name']['thumb'], $this->imagesDir, "thumb_");
        }
        
        /************* Save Image ************/
        // check for error
        $error = JArrayHelper::getValue($uploadedFile['error'], 'image');
        $this->checkUploadErrors($error);
        
        // Upload image
        if(!empty($uploadedFile['name']['image'])){
//            $options = array("width" => $this->imageWidth, "height"=>$this->imageHeight, "type" => "crop", "startX"=>0, "startY"=>0);
            $names['image'] = $this->uploadImage($uploadedFile['tmp_name']['image'],$uploadedFile['name']['image'], $this->imagesDir, "image_");
        }

        return $names;
    
    }
    
    protected function checkUploadErrors($error){
        
        if(!empty($error)){
            switch($error){
                case UPLOAD_ERR_INI_SIZE:
                     throw new ItpUserException(JText::_('ITP_ERROR_UPLOAD_ERR_INI_SIZE'), 500);
                case UPLOAD_ERR_FORM_SIZE:
                    throw new ItpUserException(JText::_('ITP_ERROR_UPLOAD_ERR_FORM_SIZE'), 500);
                case UPLOAD_ERR_PARTIAL:
                    throw new ItpUserException(JText::_('ITP_ERROR_UPLOAD_ERR_PARTIAL'), 500);
                case UPLOAD_ERR_NO_FILE:
//                    throw new ItpUserException( JText::_( 'ITP_ERROR_UPLOAD_ERR_NO_FILE' ), 500 );
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new ItpUserException(JText::_('ITP_ERROR_UPLOAD_ERR_NO_TMP_DIR'), 500);
                case UPLOAD_ERR_CANT_WRITE:
                    throw new ItpUserException(JText::_('ITP_ERROR_UPLOAD_ERR_CANT_WRITE'), 500);
                case UPLOAD_ERR_EXTENSION:
                    throw new ItpUserException(JText::_('ITP_ERROR_UPLOAD_ERR_EXTENSION'), 500);
                default:
                    throw new ItpUserException(JText::_('ITP_ERROR_UPLOAD_ERR_UNKNOWN'), 500);
            }
        
        }
            
    }
    
    protected function uploadImage($uploadedFile, $uploadedName, $dir, $prefix = "", $options = array()) {
        
        list($width, $height, $type, $attr) = getimagesize($uploadedFile);
        
        // Checks file extension
        if(false === array_search($type, $this->imageTypes)){
            JError::raiseWarning(500,JText::_('ITP_ERROR_IMAGE_TYPE'));
        }
        
        // Generate the name
        $ext     = JFile::getExt($uploadedName);
        $random  = uniqid(mt_rand(0,1000), true) + time();
        $name    = $prefix . substr(JUtility::getHash($random), 0, 6) . "." . JFile::makeSafe($ext);
        $newFile = $dir . DS . $name;
        
        if(!JFile::upload($uploadedFile, $newFile)){
            JError::raiseError(500,JText::_('ITP_ERROR_FILE_CANT_BE_UPLOADED'));
        }
        
        if(!JFile::exists($newFile)){
            JError::raiseError(500,JText::_('ITP_ERROR_FILE_CANT_BE_UPLOADED'));
        }
        
        if(!empty($options)) {
            
            /** Make a thumbnail **/
            require_once JPATH_COMPONENT_ADMINISTRATOR . DS . "libraries" . DS . "phpthumb" . DS . "ThumbLib.inc.php";
        
            $width   = JArrayHelper::getValue($options,"width",0);
            $height  = JArrayHelper::getValue($options,"height",0);
            
            $image   = PhpThumbFactory::create($newFile);
            switch($options['type']){
                case "adaptive":
                    $image->adaptiveResize($width, $height);
                    break;
                default: // crop
                    $image->crop($options['startX'], $options['startY'], $this->imageWidth, $this->imageHeight);
                    break;
            }
            
            $image->save($newFile);
        }
        
        return $name;
    }
    
    private function uploadExtraImage(){
        
        require_once JPATH_COMPONENT_ADMINISTRATOR . DS . "libraries" . DS . "phpthumb" . DS . "ThumbLib.inc.php";
        
        $names = array();
        
        /************* Save Extra Images ************/
        $uploadedExtraImages = JRequest::getVar('extra', '', 'files', 'array');
        
        foreach($uploadedExtraImages['error'] as $key => $image){
            
            /************* Save Image ************/
            // check for error
            $error = JArrayHelper::getValue($uploadedExtraImages['error'], $key);
            $this->checkUploadErrors($error);
            
            // Upload image
            if(!empty($uploadedExtraImages['name'][$key])){
    //            $options = array("width" => $this->imageWidth, "height"=>$this->imageHeight, "type" => "crop", "startX"=>0, "startY"=>0);
                $imageName = $this->uploadImage($uploadedExtraImages['tmp_name'][$key],$uploadedExtraImages['name'][$key], $this->imagesDir);
                
                /** Make a thumbnail **/
                $file = $this->imagesDir . DS . $imageName;
                $fileThumb = $this->imagesDir . DS . "ethumb_" . $imageName;
                
                $thumb = PhpThumbFactory::create($file);
                $thumb->adaptiveResize(48, 48);
                $thumb->save($fileThumb);
                
                $names[] = $imageName;
                
            }
        }
        
        return $names;
    
    }
    
    private function storeExtraImage($extraImages, $projectId){
        
        settype($extraImages, "array");
        settype($projectId, "integer");
        
        if(!$projectId){
            throw new ItpException(JText::_('ITP_ERROR_INVALID_ITEMS'), 500);
        }
        
        if(!empty($extraImages) and !empty($projectId)){
            
            $query = "
	    	   INSERT INTO
	    	       `#__vp_images`
	    	       (`name`,`projects_id`)
	    	   VALUES 
	    	   ";
            
            foreach($extraImages as $image){
                $images[] = "(" . $this->_db->Quote($image) . ",$projectId)";
            }
            
            $query .= implode(",", $images);
            
            $this->_db->setQuery($query);
            
            if(!$this->_db->query()){
                throw new ItpException(JText::_('ITP_ERROR_SYSTEM'), 500);
            }
        }
    
    }
    
/**
     * A protected method to get a set of ordering conditions.
     *
     * @param   object  A record object.
     * @return  array   An array of conditions to add to add to ordering queries.
     * @since   1.6
     */
    protected function getReorderConditions($table){
        $condition = array();
        $condition[] = 'catid = '.(int) $table->catid;
        return $condition;
    }
    
}