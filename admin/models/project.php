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
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

/**
 * It is a project model
 * 
 * @author Todor Iliev
 * @todo gets the destination dir from parameters
 */
class VipPortfolioModelProject extends JModelAdmin {
    
	/**
     * 
     * A folder where the images will be saved
     * @var string
     */
    public $imagesFolder  = "";
    
    /**
     * 
     * Mime types allowed for uploading
     * @var string
     */
    public $uploadMime = array();
    
    /**
     * 
     * Maximum allowed file size
     * @var string
     */
    public $uploadMaxSize = 0;
    
    /**
     * 
     * A list of image extensions allowed for upload
     * @var string
     */
    public $imageExtensions = array();
    
    /**
     * Thumbnail width
     * @var integer
     */
    public $thumbWidth;
    
    /**
     * Thumbnail height
     * @var integer
     */
    public $thumbHeight;
    
    /**
     * Thumbnail width of the additional image
     * @var integer
     */
    public $extraThumbWidth;
    
    /**
     * Thumbnail height of the additional image
     * @var integer
     */
    public $extraThumbHeight;
    
    /**
     * Image width
     * @var integer
     */
    public $imageWidth;
    
    /**
     * Image height
     * @var integer
     */
    public $imageHeight;
    
    /**
     * 
     * Options that flags resizing ability
     * If it is set to 1, the system will resize original image when they are uploaded.
     * @var unknown_type
     */
    public $resizeImages;
    
    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
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
        
        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        
        // Joomla! media extension parameters
        $mediaParams = JComponentHelper::getParams("com_media");
        
        // Extension parameters
        $this->imagesURI           = $params->get("images_directory");
        $this->imagesFolder        = JPATH_SITE . DIRECTORY_SEPARATOR. $params->get("images_directory");
        $this->imageWidth          = $params->get("resize_image_width", 800);
        $this->imageHeight         = $params->get("resize_image_height", 600);
        $this->thumbWidth          = $params->get("resize_thumb_width", 200);
        $this->thumbHeight         = $params->get("resize_thumb_height", 150);
        $this->extraThumbWidth     = $params->get("extra_image_thumb_width", 50);
        $this->extraThumbHeight    = $params->get("extra_image_thumb_height", 50);
        $this->resizeImages        = $params->get("resize_image", 0);
        
        // Media Manager parameters
        $this->uploadMime      = explode(",", $mediaParams->get("upload_mime"));
        $this->imageExtensions = explode(",", $mediaParams->get("image_extensions") );
        $this->uploadMaxSize   = $mediaParams->get("upload_maxsize");
        
        
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
        $form = $this->loadForm($this->option.'.project', 'project', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.project.data', array());
        
        if(empty($data)){
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    /**
     * Save project data into the DB
     * 
     * @param array $data   The data about project
     * 
     * @return     ID of the project
     */
    public function save($data){
        
        $title          = JArrayHelper::getValue($data,"title");
        $id             = JArrayHelper::getValue($data,"id");
        $catid          = JArrayHelper::getValue($data,"catid");
        $url            = JArrayHelper::getValue($data,"url");
        $published      = JArrayHelper::getValue($data,"published");
        $description    = JArrayHelper::getValue($data,"description");
        
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
                    $file = $this->imagesFolder .DIRECTORY_SEPARATOR. $row->thumb;
                    if(is_file($file)) {
                        JFile::delete($file);
                    }
            }
            
            $row->set("thumb", $images['thumb']);
        
        }
        
        // Sets the images
        if(!empty($images['image'])){
            
            // Delete old image if I upload the new one
            if(!empty($row->image)){
                // Remove an image from the filesystem
                $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$row->image;
                if(is_file($file)) {
                    JFile::delete($file);
                }
            }
            
            $row->set("image", $images['image']);
        
        }
        
        $row->set("published", $published);
        $row->store();
        
        return $row->id;
    
    }
    
    /**
     * Delete records
     *
     * @param array $cids Rows Ids
     */
    public function delete($cids){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Get all images
        $query = $db->getQuery(true);
        $query
            ->select("thumb, image")
            ->from("#__vp_projects")
            ->where("id IN (" . implode(",", $cids) . ")");
            
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        // Delete images
        foreach($rows as $image){
            if(!empty($image->thumb)){
                $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$image->thumb; 
                if(is_file($file)) {
                    JFile::delete($file);
                }
            }
            if(!empty($image->image)){
                $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$image->image; 
                if(is_file($file)) {
                    JFile::delete($file);
                }    
            }
            $file = "";
        }
        
        // Remove additional images
        $this->removeExtraImages($cids);
        
        // Delete records 
        $query = $db->getQuery(true);
        $query
            ->delete("#__vp_projects")
            ->where("id IN (" . implode(",", $cids) . ")");
        
        $db->setQuery($query);
        $db->query();
    }
    
    /**
     * Delete an image
     *
     * @param integer Project id
     * @param string  Shows the type of image - the thumbnail or the original image
     * @exception ItpException
     */
    public function removeImage($id, $type){
        
        $row = $this->getTable();
        $row->set("id", $id);
        $row->load();
        
        if(strcmp("thumb", $type) == 0){
            
            // Remove an image from the filesystem
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$row->thumb;
            if(is_file($file)) {
                JFile::delete($file);
            }
            
            // Remove the image from the DB
            $row->set("thumb", "");
        }
        
        if(strcmp("image", $type) == 0){
            
            // Remove an image from the filesystem
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$row->image;
            if(is_file($file)) {
                JFile::delete($file);
            }
            
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
    protected function removeExtraImages($projectsIds){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Get all images
        $query = $db->getQuery(true);
        $query
            ->select("image, thumb")
            ->from("#__vp_images")
            ->where("project_id IN (" . implode(",", $projectsIds) . ")");
            
        $db->setQuery($query);
        $images = $db->loadObjectList();
        
        
        if(!$images){
            return;
        }
        
        foreach($images as $image) {
            
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$image->image;
            if(is_file($file)) {
                JFile::delete($file);
            }
            
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$image->thumb;
            if(is_file($file)) {
                JFile::delete($file);
            }
            $file = "";
            
        }
        
        // Delete records 
        $query = $db->getQuery(true);
        $query
            ->delete("#__vp_images")
            ->where("project_id IN (" . implode(",", $projectsIds) . ")");
        
        $db->setQuery($query);
        $db->query();
    
    }
    
    /**
     * Only delete an additionl image
     *
     * @param integer Image ID
     * @exception ItpException
     */
    public function removeExtraImage($id){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Get the image
        $query = $db->getQuery(true);
        $query
            ->select("image, thumb")
            ->from("#__vp_images")
            ->where("id = " . (int)$id );
            
        
        $db->setQuery($query);
        $row = $db->loadObject();
         
        if(!empty($row)){
            
            // Remove the image from the filesystem
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$row->image;
            if(is_file($file)) {
                JFile::delete($file);
            }
            
            // Remove the thumbneil from the filesystem
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR. $row->thumb;
            if(is_file($file)) {
                JFile::delete($file);
            }
            
            // Delete the record
            $query = $db->getQuery(true);
            $query
                ->delete("#__vp_images")
                ->where("id = " . (int)$id );
            
            $db->setQuery($query);
            $db->query();
        }
    
    }
    
    /**
     * Saves the images
     * 
     * @throws ItpException 
     */
    public function saveImages(){
        
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.image.image');
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $names         = array("thumb" => "", "image" => "");
        $uploadedFile  = $app->input->files->get('jform');
        $uploadedFile  = JArrayHelper::getValue($uploadedFile, "image");
        
        // Save image
        $this->checkUploadErrors($uploadedFile);
        
        // Upload image
        if(!empty($uploadedFile['name'])){
            
            $names['image'] = $this->uploadImage($uploadedFile, $this->imagesFolder, "image_");
            if($this->resizeImages) {
                $this->resizeImage($names['image']);
            }
            
            $names["thumb"] = $this->createThumb($names['image'], $this->thumbWidth, $this->thumbHeight, "thumb_");
            
        }

        return $names;
    
    }
    
    private function resizeImage($fileName) {
        
        // Make thumbnail
        $newFile = $this->imagesFolder.DIRECTORY_SEPARATOR.$fileName;
        
        $ext     = (string)JFile::getExt($fileName);
        $ext     = strtolower(JFile::makeSafe($ext));
        
        $image = new JImage();
        $image->loadFile($newFile);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('ITP_ERROR_FILE_NOT_FOUND', $newFile));
        }
        
        // Resize the file
        $image->resize($this->imageWidth, $this->imageHeight, false);
        
        switch ($ext) {
			case "gif":
				$type = IMAGETYPE_GIF;
				break;

			case "gif":
				$type = IMAGETYPE_PNG;
				break;

			case IMAGETYPE_JPEG:
			default:
				$type = IMAGETYPE_JPEG;
		}
		
        $image->toFile($newFile, $type);
        
    }
    
    private function createThumb($fileName, $width, $heigh, $prefix) {
        
        // Make thumbnail
        $newFile = $this->imagesFolder.DIRECTORY_SEPARATOR.$fileName;
        
        $ext     = JFile::getExt($fileName);
        $ext     = JFile::makeSafe($ext);
        
        $image   = new JImage();
        $image->loadFile($newFile);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('ITP_ERROR_FILE_NOT_FOUND', $newFile));
        }
        
        // Resize the file as a new object
        $thumb     = $image->resize($width, $heigh, true);
        
        $code      = uniqid(rand(0, 10000));
        $thumbName = $prefix . substr(JUtility::getHash($code), 0, 6) . ".".$ext;
        $thumbFile = $this->imagesFolder.DIRECTORY_SEPARATOR.$thumbName;
        
        switch ($ext) {
			case "gif":
				$type = IMAGETYPE_GIF;
				break;

			case "gif":
				$type = IMAGETYPE_PNG;
				break;

			case IMAGETYPE_JPEG:
			default:
				$type = IMAGETYPE_JPEG;
		}
		
        $thumb->toFile($thumbFile, $type);
        
        return $thumbName;
    }
    
    
    protected function checkUploadErrors($uploadedFile){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $serverContentLength = (int)$app->input->server->get('CONTENT_LENGTH');
        
        // Verify file size
        if(
            $serverContentLength > ($this->uploadMaxSize * 1024 * 1024) OR
			$serverContentLength > (int)(ini_get('upload_max_filesize'))* 1024 * 1024 OR
			$serverContentLength > (int)(ini_get('post_max_size'))* 1024 * 1024 OR
			$serverContentLength > (int)(ini_get('memory_limit'))* 1024 * 1024
		) {
		    throw new Exception(JText::_("ITP_ERROR_WARNFILETOOLARGE"));
		}
		
        if(!empty($uploadedFile['error'])){
                
            switch($uploadedFile['error']){
                case UPLOAD_ERR_INI_SIZE:
                     throw new Exception(JText::_('ITP_ERROR_UPLOAD_ERR_INI_SIZE'), 500);
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception(JText::_('ITP_ERROR_UPLOAD_ERR_FORM_SIZE'), 500);
                case UPLOAD_ERR_PARTIAL:
                    throw new Exception(JText::_('ITP_ERROR_UPLOAD_ERR_PARTIAL'), 500);
                case UPLOAD_ERR_NO_FILE:
//                    throw new Exception( JText::_( 'ITP_ERROR_UPLOAD_ERR_NO_FILE' ), 500 );
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new Exception(JText::_('ITP_ERROR_UPLOAD_ERR_NO_TMP_DIR'), 500);
                case UPLOAD_ERR_CANT_WRITE:
                    throw new Exception(JText::_('ITP_ERROR_UPLOAD_ERR_CANT_WRITE'), 500);
                case UPLOAD_ERR_EXTENSION:
                    throw new Exception(JText::_('ITP_ERROR_UPLOAD_ERR_EXTENSION'), 500);
                default:
                    throw new Exception(JText::_('ITP_ERROR_UPLOAD_ERR_UNKNOWN'), 500);
            }
        
        }
    }
    
    /**
     * 
     * Upload an image
     * @param string $uploadedFile Path and filename
     * @param string $uploadedName Filename of the uploaded file
     * @param string $destFolder   Destination directory where the file will be saved
     * @param string $prefix	   File prefix
     * @param string $options	   Options for resizing
     * @throws Exception
     * 
     * @return string 
     * 
     */
    protected function uploadImage($uploadedFile, $destFolder, $prefix = "") {
        
        $imageProperties = JImage::getImageFileProperties($uploadedFile["tmp_name"]);
        
        // Check mime type of the file
        if(false === array_search($imageProperties->mime, $this->uploadMime)){
            throw new Exception(JText::_('ITP_ERROR_IMAGE_TYPE'));
        }
        
        // Check file extension
        $ext     = (string)JFile::getExt($uploadedFile["name"]);
        $ext     = strtolower(JFile::makeSafe($ext));
        
        if(false === array_search($ext, $this->imageExtensions)){
            throw new Exception(JText::sprintf('ITP_ERROR_IMAGE_EXTENSIONS', $ext));
        }
        
        $code    = uniqid(rand(0, 10000));
        $name    = $prefix . substr(JUtility::getHash($code), 0, 6) . "." . $ext;
        $newFile = $destFolder .DIRECTORY_SEPARATOR. $name;
        
        if(!JFile::upload($uploadedFile["tmp_name"], $newFile)){
            throw new Exception(JText::_('ITP_ERROR_FILE_CANT_BE_UPLOADED'));
        }
        
        if(!is_file($newFile)){
            throw new Exception('ITP_ERROR_FILE_CANT_BE_UPLOADED');
        }
        
        return $name;
    }
    
    public function uploadExtraImages($files){
        
        $images = array();
        
        // check for error
        foreach($files as $file){
            $this->checkUploadErrors($file);
        }
        
        foreach($files as $file){
            // Upload image
            if(!empty($file['name'])){
                
                $names = array("thumb" =>"", "image" =>"");
                $names['image'] = $this->uploadImage($file, $this->imagesFolder, "extra_");
                $names["thumb"] = $this->createThumb($names['image'], $this->extraThumbWidth, $this->extraThumbWidth, "extra_thumb_");
                
                $images[] = $names;
            }
        }
        
        return $images;
    
    }
    
    /**
     * 
     * Save additional images names to the project
     * @param array $images
     * 
     * * @throws Exception
     */
    public function storeExtraImages($images, $itemId){
        
        settype($images, "array");
        settype($itemId, "integer");
        $results = array();
        
        if(!empty($images) AND !empty($itemId)){
            
            $queries = array();
            $names   = array();
            
            $db = JFactory::getDbo();
        	/** @var $db JDatabaseMySQLi **/
            
            $query = "
	    	   INSERT INTO
	    	       `#__vp_images`
	    	       (`image`, `thumb`, `project_id`)
	    	   VALUES 
	    	   ";
            
            foreach($images as $image){
                $queries[] = "(" . $db->quote($image["image"]) . ",".$db->quote($image["thumb"]).",".$itemId.")";
                $names[]   = $db->quote($image["image"]);
            }
            
            $query .= implode(",", $queries);
            $db->setQuery($query);
            $db->query();
            
            // Get ids of the images
            $query = $db->getQuery(true);
            $query
                ->select("id, image, thumb")
                ->from("#__vp_images")
                ->where("image IN (".implode(",",$names).")");
                
            $db->setQuery($query);
            $results = $db->loadAssocList();
            
            if(!empty($results)) {
                foreach($results as &$result) {
                    $result["image"] = JURI::root()."/".$this->imagesURI."/".$result["image"];
                    $result["thumb"] = JURI::root()."/".$this->imagesURI."/".$result["thumb"];
                }
            }
            
        }
        
        return $results;
    
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