<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipPorfolio
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipPorfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Category model.
 *
 * @package		ITPrism Components
 * @subpackage	VipPorfolio
 * @since		1.5
 */
class VipPortfolioModelCategory extends JModelAdmin {
    
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
    public $resizeImage;
    
    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_VIPPORTFOLIO';
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Category', $prefix = 'VipPortfolioTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get the record form.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Get the form.
        $form = $this->loadForm($this->option.'.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
            return false;
        }
        
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option.'edit.category.data', array());
        
        if(empty($data)){
            $data = $this->getItem();
        }
        
        return $data;
    }

    /**
     * Save a category
     * 
     * @param $data        All data for the category in an array
     * 
     */
    public function save($data){
        
        $id     = JArrayHelper::getValue($data, "id", null);
        $name   = JArrayHelper::getValue($data, "name", "");
        
        // Load item data
        $row = $this->getTable();
        $row->load($id);
        
        // Save image
        $newImage = $this->saveImages();

        $row->set("name", $name);
        $alias = JString::strtolower(JArrayHelper::getValue($data, "alias", ""));
        
        $row->set("alias",          JApplication::stringURLSafe($alias));
        $row->set("desc",           JArrayHelper::getValue($data, "desc", ""));
        
        $row->set("meta_title",     JArrayHelper::getValue($data, "meta_title", ""));
        $row->set("meta_keywords",  JArrayHelper::getValue($data, "meta_keywords", ""));
        $row->set("meta_desc",      JArrayHelper::getValue($data, "meta_desc", ""));
        $row->set("meta_canonical", JArrayHelper::getValue($data, "meta_canonical", ""));
        
        $published = JArrayHelper::getValue($data, "published", 0);
        
        if(!empty($newImage)){
            
            // Delete old image if I upload the new one
            if(!empty($row->image)){
                jimport('joomla.filesystem.file');
                // Remove an image from the filesystem
                $file = $this->imagesFolder .DIRECTORY_SEPARATOR. $row->image;
                
                if(is_file($file)) {
                    JFile::delete($file);
                }
            
            }
            $row->set("image", $newImage);
        }
        
        $row->set("published", $published);
        $row->store();
        
        return $row->id;
    
    }
    
    /**
     * Delete records from the DB
     *
     * @param array $cids
     */
    public function delete($cids){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Get all images
        $query = $db->getQuery(true);
        $query
            ->select("image")
            ->from("#__vp_categories")
            ->where("id IN (" . implode(",", $cids) . ")");
            
        $db->setQuery($query);
        $images = $db->loadColumn();
        
        // Delete old image if I upload the new one
        if(!empty($images)){
            jimport('joomla.filesystem.file');
            
            foreach( $images as $image ) {
                // Remove the images from the filesystem
                $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$image;
                if(is_file($file)) {
                    JFile::delete($file);
                }
            }
        }
        
        // Delete categories 
        $query = $db->getQuery(true);
        $query
            ->delete("#__vp_categories")
            ->where("id IN (" . implode(",", $cids) . ")");
        
        $db->setQuery($query);
        $db->query();
    
    }
    
    /**
     * 
     * Check for existing projects in categories
     * @param array $cids
     */
    public function isProjectsExists($cids) {
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Checks for existing projects into that directory
        $query = $db->getQuery("new");
        $query
            ->select("COUNT(*) as num")
            ->from("#__vp_projects")
            ->where("catid IN (" . implode(",", $cids) . ")");
        
        $db->setQuery($query, 0, 1);
        $num = $db->loadResult();
        
        return (bool)$num;
    }
    
    /**
     * Delete image only
     *
     * @param integer Item id
     */
    public function removeImage($id){
        
        // Load category data
        $row = $this->getTable();
        $row->set("id", $id);
        $row->load();
        
        // Delete old image if I upload the new one
        if(!empty($row->image)){
            jimport('joomla.filesystem.file');
            // Remove an image from the filesystem
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$row->image;
            
            if(is_file($file)) {
                JFile::delete($file);
            }
        }
        
        $row->set("image", "");
        $row->store();
    
    }
    
    
    /**
     * Saves the image and the thumb
     * 
     */
    public function saveImages(){
        
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $names         = array("image");
        $uploadedFile  = $app->input->files->get('jform');
        $uploadedFile  = JArrayHelper::getValue($uploadedFile, "image");
        
        // Check for errors
        $this->checkUploadErrors($uploadedFile);
        
        // Save Image
        if(!empty($uploadedFile['name'])){
            
            $options = array();
            if($this->resizeImage) {
                $options = array("width" => $this->imageWidth, "height"=>$this->imageHeight);
            }
            
            $names['image'] = $this->uploadImage($uploadedFile['tmp_name'],$uploadedFile['name'], $this->imagesFolder, "image_", $options);
        }

        return $names['image'];
    
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
     * 
     */
    protected function uploadImage($uploadedFile, $uploadedName, $destFolder, $prefix = "", $options = array()) {
        
        jimport('joomla.image.image');
        $imageProperties = JImage::getImageFileProperties($uploadedFile);
        
        // Check mime type of the file
        if(false === array_search($imageProperties->mime, $this->uploadMime)){
            throw new Exception(JText::_('ITP_ERROR_IMAGE_TYPE'));
        }
        
        // Check file extension
        $ext     = JFile::getExt($uploadedName);
        $ext     = JFile::makeSafe($ext);
        
        if(false === array_search($ext, $this->imageExtensions)){
            throw new Exception(JText::sprintf('ITP_ERROR_IMAGE_EXTENSIONS', $ext));
        }
        
        // Generate the name
        $name    = $prefix . substr(JUtility::getHash(time()), 0, 6) . "." . $ext;
        $newFile = $destFolder . DIRECTORY_SEPARATOR. $name;
        
        if(!JFile::upload($uploadedFile, $newFile)){
            throw new Exception(JText::_('ITP_ERROR_FILE_CANT_BE_UPLOADED'));
        }
        
        if(!is_file($newFile)){
            throw new Exception('ITP_ERROR_FILE_CANT_BE_UPLOADED');
        }
        
        // Resize image
        if(!empty($options)) {
            
            $image = new JImage();
            $image->loadFile($newFile);
            if (!$image->isLoaded()) {
                throw new Exception(JText::sprintf('ITP_ERROR_FILE_NOT_FOUND', $newFile));
            }
            
            $width   = JArrayHelper::getValue($options, "width", $this->imageWidth);
            $height  = JArrayHelper::getValue($options, "height", $this->imageWidth);
            
            // Resize the file
            $image->resize($width, $height, false);
            $image->toFile($newFile, IMAGETYPE_PNG);
            
        }
        
        return $name;
    }
    
	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function populateState() {
		
	    parent::populateState();
	    
	    $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
		
		$resizeImage = $app->getUserStateFromRequest($this->option.'.category.resize_image', 'resize_image', 0, 'uint');
		$this->setState('resize_image', $resizeImage);
		
		$imageWidth = $app->getUserStateFromRequest($this->option.'.category.image_width', 'image_width');
		$this->setState('image_width', $imageWidth);
		
		$imageHeight = $app->getUserStateFromRequest($this->option.'.category.image_height', 'image_height');
		$this->setState('image_height', $imageHeight);
	}
    
    
}