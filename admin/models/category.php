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
defined('_JEXEC') or die();

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
     * Images directory
     * 
     * @var string
     */
    public $imagesDir = "";
    
    public $imageTypes  = array();
    
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
        
        $this->imagesDir = JPATH_SITE . DS . "media" . DS. "vipportfolio";
        
        $this->imageTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
    }
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
        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Get the form.
        $form = $this->loadForm('com_vipportfolio.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_vipportfolio.edit.category.data', array());
        
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
     * @exception ItpUserException
     */
    public function save($data){
        
        $id     = JArrayHelper::getValue($data, "id", null);
        $name   = JArrayHelper::getValue($data, "name", "");
        
        if(!$name){
            throw new ItpUserException(JText::_('ITP_ERROR_INVALID_NAME'), 404);
        }
        
        // Load item data
        $row = $this->getTable();
        $row->load($id);
        
        // Save image
        $newImage = $this->saveImages();

        $row->set("name", $name);
        $alias = JString::strtolower(JArrayHelper::getValue($data, "alias", ""));
        $row->set("alias", JApplication::stringURLSafe($alias));
        $row->set("desc", JArrayHelper::getValue($data, "desc", ""));
        
        $row->set("meta_title", JArrayHelper::getValue($data, "meta_title", ""));
        $row->set("meta_keywords", JArrayHelper::getValue($data, "meta_keywords", ""));
        $row->set("meta_desc", JArrayHelper::getValue($data, "meta_desc", ""));
        $row->set("meta_canonical", JArrayHelper::getValue($data, "meta_canonical", ""));
        
        $published = JArrayHelper::getValue($data, "published", 0);
        
        if(!empty($newImage)){
            
            // Delete old image if I upload the new one
            if(!empty($row->image)){
                jimport('joomla.filesystem.file');
                // Remove an image from the filesystem
                $file = $this->imagesDir.DS. $row->image;
                
                JFile::delete($file);
            
            }
            $row->set("image", $newImage);
        }
        
        $row->set("published", $published);
        
        if(!$row->store()){
            throw new ItpException($row->getError(), 500);
        }
        
        return $row->id;
    
    }
    
    /**
     * Delete records from the DB
     *
     * @param array $cids
     * @exception ItpUserException
     * @exception ItpException
     */
    public function delete($cids){
        
        if(!$cids){
            throw new ItpUserException(JText::_('ITP_ERROR_INVALID_ITEMS_SELECTED'), 404);
        }
        
        $db = JFactory::getDbo();
        /* @var $db JDatabaseMySQLi */
        
        // Checks for existing projects into that directory
        foreach($cids as $id){
            $query = "
               SELECT
                   COUNT(*)
               FROM
                   `#__vp_projects` 
               WHERE
                   `catid` =" . (int)$id;
            
            $db->setQuery($query,0,1);
            $num = $db->loadResult();
            
            if($num){
                throw new ItpUserException(JText::_('ITP_ERROR_PROJECT_EXISTS'), 500);
            }
        }
        
        /** Load images **/
        $query = "
             SELECT
                 `image` 
             FROM
                 `#__vp_categories`
             WHERE
                 `id` IN (" . implode(",", $cids) . ")";
        
        $db->setQuery($query,0,1);
        $image = $db->loadResult();
        
        // Delete old image if I upload the new one
        if(!empty($image)){
            jimport('joomla.filesystem.file');
            // Remove an image from the filesystem
            $file = $this->imagesDir.DS.$image;
            JFile::delete($file);
        }
        
        // Delete categories 
        $query = "
            DELETE  
            FROM 
                 `#__vp_categories` 
            WHERE   
                 `id` IN ( " . implode(',', $cids) . " )";
        
        $db->setQuery($query);
        
        if(!$db->query()){
            throw new ItpException($db->getErrorMsg(), 500);
        }
    
    }
    
    /**
     * Delete image only
     *
     * @param integer Item id
     * @exception ItpException
     */
    public function removeImage($id){
        
        if(!$id){
            throw new ItpException(JText::_('ITP_ERROR_RECORDS_DO_NOT_EXIST'), 500);
        }
        
        // Load category data
        $row = $this->getTable();
        $row->set("id", $id);
        $row->load();
        
        // Delete old image if I upload the new one
        if(!empty($row->image)){
            jimport('joomla.filesystem.file');
            // Remove an image from the filesystem
            $file = $this->imagesDir.DS.$row->image;
            JFile::delete($file);
        }
        
        $row->set("image", "");
        $row->store();
    
    }
    
    
    /**
     * Saves the image and the thumb
     * 
     * @throws ItpException 
     */
    public function saveImages(){
        
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        
        if(!JFolder::exists($this->imagesDir)){
            if(!VpHelper::createFolder($this->imagesDir)) {
                throw new ItpException(JText::sprintf("ITP_ERROR_CANNOT_CREATE_FOLDER",$this->imagesDir), 500);
            }
        }
        
        jimport('joomla.filesystem.path');
        
        $names = array("image");
        
        /************* Save Image ************/
        $uploadedFile = JRequest::getVar('jform', '', 'files', 'array');
        
        $this->checkUploadErrors($uploadedFile);
        if(!empty($uploadedFile['name']['image'])){
//            $options = array("width" => $this->imageWidth, "height"=>$this->imageHeight, "type" => "crop", "startX"=>0, "startY"=>0);
            $names['image'] = $this->uploadImage($uploadedFile['tmp_name']['image'],$uploadedFile['name']['image'], $this->imagesDir, "image_");
        }

        return $names['image'];
    
    }
    
    protected function checkUploadErrors($uploadedFile){
        
        if(!empty($uploadedFile['error']['image'])){
                
            switch($uploadedFile['error']['image']){
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
        $name    = $prefix . substr(JUtility::getHash(time()), 0, 6) . "." . JFile::makeSafe($ext);
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
    
}