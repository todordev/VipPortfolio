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
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_VIPPORTFOLIO';
    
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
            
            // Get values that was used by the user
            $app = JFactory::getApplication();
            $data->resize = array(
                "thumb_width"  => $app->getUserState($this->option.".project.thumb_width", 200),
                "thumb_height" => $app->getUserState($this->option.".project.thumb_height", 300),
                "thumb_scale"  => $app->getUserState($this->option.".project.thumb_scale", JImage::SCALE_INSIDE),
                "image_width"  => $app->getUserState($this->option.".project.image_width", 500),
                "image_height" => $app->getUserState($this->option.".project.image_height", 600),
                "image_scale"  => $app->getUserState($this->option.".project.image_scale", JImage::SCALE_INSIDE)
            );
            
            // Prime some default values.
			if ($this->getState($this->getName().'.id') == 0) {
				$data->set('catid', $app->input->getInt('catid', $app->getUserState($this->option.'.projects.filter.category_id')));
			}
			
        }
        
        return $data;
    }
    
    /**
     * Save project data into the DB
     * 
     * @param array $data   The data about project
     * @return     ID of the project
     */
    public function save($data){
        
        $title          = JArrayHelper::getValue($data,"title");
        $alias          = JArrayHelper::getValue($data,"alias");
        $id             = JArrayHelper::getValue($data,"id");
        $catid          = JArrayHelper::getValue($data,"catid");
        $url            = JArrayHelper::getValue($data,"url");
        $published      = JArrayHelper::getValue($data,"published");
        $description    = JArrayHelper::getValue($data,"description");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("title",         $title);
        $row->set("alias",         $alias);
        $row->set("description",   $description);
        $row->set("url",           $url);
        $row->set("catid",         $catid);
        $row->set("published",     $published);
        
        // Prepare the row for saving
		$this->prepareTable($row, $data);
		
        $row->store();
        
        return $row->id;
    
    }
    
	/**
	 * Prepare and sanitise the table prior to saving.
	 * @since	1.6
	 */
	protected function prepareTable(&$table, $data) {
	    
        // Set the thumbnail
        if(!empty($data['thumb'])){
            
            // Delete old image if I upload the new one
            if(!empty($table->thumb)){
                    // Remove an image from the filesystem
                    $file = $this->imagesFolder .DIRECTORY_SEPARATOR. $table->thumb;
                    if(is_file($file)) {
                        JFile::delete($file);
                    }
            }
            
            $table->set("thumb", $data['thumb']);
        
        }
        
        // Sets the images
        if(!empty($data['image'])){
            
            // Delete old image if I upload the new one
            if(!empty($table->image)){
                // Remove an image from the filesystem
                $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$table->image;
                if(is_file($file)) {
                    JFile::delete($file);
                }
            }
            
            $table->set("image", $data['image']);
        
        }
        
        // get maximum order number
		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db     = JFactory::getDbo();
				$query  = $db->getQuery(true);
				$query
				    ->select("MAX(ordering)")
				    ->from("#__vp_projects");
				
			    $db->setQuery($query, 0, 1);
				$max   = $db->loadResult();

				$table->ordering = $max+1;
			}
		}
		
	    // Fix magic qutoes
	    if( get_magic_quotes_gpc() ) {
            $table->title       = stripcslashes($table->title);
            $table->description = stripcslashes($table->description);
            $table->url         = stripcslashes($table->url);
        }
        
		// If does not exist alias, I will generate the new one from the title
        if(!$table->alias) {
            $table->alias = $table->title;
        }
        $table->alias = JApplication::stringURLSafe($table->alias);
        
	}
	
    /**
     * Delete records
     *
     * @param array $cids Rows Ids
     */
    public function delete(&$cids){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        JArrayHelper::toInteger($cids);
        
        // Get all images
        $query = $db->getQuery(true);
        $query
            ->select("a.thumb, a.image")
            ->from($db->quoteName("#__vp_projects") . " AS a")
            ->where("a.id IN (" . implode(",", $cids) . ")");
            
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
            ->delete($db->quoteName("#__vp_projects"))
            ->where($db->quoteName("id") ." IN (" . implode(",", $cids) . ")");
        
        $db->setQuery($query);
        $db->query();
    }
    
    /**
     * Delete an image
     *
     * @param integer Project id
     * @param string  Shows the type of image - the thumbnail or the original image
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
     */
    protected function removeExtraImages($projectsIds){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Get all images
        $query = $db->getQuery(true);
        $query
            ->select("a.image, a.thumb")
            ->from($db->quoteName("#__vp_images") . ' AS a')
            ->where("a.project_id IN (" . implode(",", $projectsIds) . ")");
            
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
            ->delete($db->quoteName("#__vp_images"))
            ->where($db->quoteName("project_id") ." IN (" . implode(",", $projectsIds) . ")");
        
        $db->setQuery($query);
        $db->query();
    
    }
    
    /**
     * Only delete an additionl image
     *
     * @param integer Image ID
     */
    public function removeExtraImage($id){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Get the image
        $query = $db->getQuery(true);
        $query
            ->select("a.image, a.thumb")
            ->from($db->quoteName("#__vp_images") .' AS a')
            ->where("a.id = " . (int)$id );
            
        
        $db->setQuery($query);
        $row = $db->loadObject();
         
        if(!empty($row)){
            
            // Remove the image from the filesystem
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$row->image;
            if(is_file($file)) {
                JFile::delete($file);
            }
            
            // Remove the thumbnail from the filesystem
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR. $row->thumb;
            if(is_file($file)) {
                JFile::delete($file);
            }
            
            // Delete the record
            $query = $db->getQuery(true);
            $query
                ->delete($db->quoteName("#__vp_images"))
                ->where($db->quoteName("id") ." = ". (int)$id );
            
            $db->setQuery($query);
            $db->query();
        }
    
    }
    
    public function uploadImage($file, $options = array()) {
        
        $app = JFactory::getApplication();
    	/** @var $app JAdministrator **/
        
        $upload          = new ITPrismFileUploadImage($file);
        
        // Media manager parameters
        $upload->setMimeTypes($this->uploadMime);
        
        $upload->setImageExtensions($this->imageExtensions);
        
        $KB              = 1024 * 1024;
        $upload->setMaxFileSize( round($this->uploadMaxSize * $KB, 0) );
        
        // Validate
        $upload->validate();
    
        $ext = JFile::getExt( JFile::makeSafe($file["name"]) );
        
        // Generate name of the image
        $code      = substr(JApplication::getHash(time()), 0, 6);
        $imageName = "image_".$code.".".$ext;
        $dest      = $this->imagesFolder . DIRECTORY_SEPARATOR . $imageName;
        
        $upload->upload($dest);
        
        // Resize image
        $resizeImage = JArrayHelper::getValue($options, "resize_image", false);
        $width       = JArrayHelper::getValue($options, "image_width", 500);
        $height      = JArrayHelper::getValue($options, "image_height", 600);
        $scale       = JArrayHelper::getValue($options, "image_scale", JImage::SCALE_INSIDE);
        
        if(!empty($resizeImage)) {
            $app->setUserState($this->option.".project.image_width", $width);
            $app->setUserState($this->option.".project.image_height", $height);
            $app->setUserState($this->option.".project.image_scale", $scale);
            $this->resizeImage($imageName, $width, $height, $scale);
        }
        
        // Create thumbnail
        $createThumb = JArrayHelper::getValue($options, "create_thumb", false);
        $width       = JArrayHelper::getValue($options, "thumb_width", 200);
        $height      = JArrayHelper::getValue($options, "thumb_height", 300);
        $scale       = JArrayHelper::getValue($options, "thumb_scale", JImage::SCALE_INSIDE);
        
        $thumbName   = null;
        if(!empty($createThumb)) {
            $app->setUserState($this->option.".project.thumb_width", $width);
            $app->setUserState($this->option.".project.thumb_height", $height);
            $app->setUserState($this->option.".project.thumb_scale", $scale);
            $thumbName = $this->createThumb($imageName, $width, $height, "thumb_", $scale);
        }
        
        return $names = array(
            "image" => $imageName,
            "thumb" => $thumbName
        );
        
    }
    
	/**
     * This method upload the thumnail
     * @param array $file
     */
    public function uploadThumb($file) {
        
        $upload          = new ITPrismFileUploadImage($file);
        
        // Media manager parameters
        $upload->setMimeTypes($this->uploadMime);
        
        $upload->setImageExtensions($this->imageExtensions);
        
        $KB              = 1024 * 1024;
        $upload->setMaxFileSize( round($this->uploadMaxSize * $KB, 0) );
        
        // Validate
        $upload->validate();
    
        $ext = JFile::getExt( JFile::makeSafe($file["name"]) );
        
        // Generate name of the image
        $code      = substr(JApplication::getHash(time()), 0, 6);
        $thumbName = "thumb_".$code.".".$ext;
        $dest      = $this->imagesFolder . DIRECTORY_SEPARATOR . $thumbName;
        
        $upload->upload($dest);
        
        return $thumbName;
        
    }
    
    protected function resizeImage($fileName, $width, $height, $scale = JImage::SCALE_INSIDE) {
        
        // Make thumbnail
        $newFile = $this->imagesFolder.DIRECTORY_SEPARATOR.$fileName;
        
        $ext     = (string)JFile::getExt(JFile::makeSafe($fileName));
        
        $image   = new JImage();
        
        $image->loadFile($newFile);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('COM_VIPPORTFOLIO_ERROR_FILE_NOT_FOUND', $newFile));
        }
        
        // Resize the file
        $image->resize($width, $height, false, $scale);
        
        switch ($ext) {
			case "gif":
				$type = IMAGETYPE_GIF;
				break;

			case "png":
				$type = IMAGETYPE_PNG;
				break;

			case IMAGETYPE_JPEG:
			default:
				$type = IMAGETYPE_JPEG;
		}
		
        $image->toFile($newFile, $type);
        
    }
    
    protected function createThumb($fileName, $width, $heigh, $prefix = "thumb_", $scale = JImage::SCALE_INSIDE) {
        
        // Make thumbnail
        $newFile = $this->imagesFolder.DIRECTORY_SEPARATOR.$fileName;
        
        $ext     = JFile::getExt(JFile::makeSafe($fileName));
        
        $image   = new JImage();
        $image->loadFile($newFile);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('COM_VIPPORTFOLIO_ERROR_FILE_NOT_FOUND', $newFile));
        }
        
        // Resize the file as a new object
        $thumb     = $image->resize($width, $heigh, true, $scale);
        
        $code      = uniqid(rand(0, 10000));
        $thumbName = $prefix . substr(JApplication::getHash($code), 0, 6) . ".".$ext;
        $thumbFile = $this->imagesFolder.DIRECTORY_SEPARATOR.$thumbName;
        
        switch ($ext) {
			case "gif":
				$type = IMAGETYPE_GIF;
				break;
				
			case "png":
				$type = IMAGETYPE_PNG;
				break;

			case IMAGETYPE_JPEG:
			default:
				$type = IMAGETYPE_JPEG;
		}
		
        $thumb->toFile($thumbFile, $type);
        
        return $thumbName;
    }
    
    public function uploadExtraImages($files, $thumbWidth = 50, $thumbHeight = 50, $scale = JImage::SCALE_INSIDE){
        
        $images = array();
        
        // check for error
        foreach($files as $file){
            
            // Upload image
            if(!empty($file['name'])){
                
                $upload          = new ITPrismFileUploadImage($file);
            
                // Media manager parameters
                $upload->setMimeTypes($this->uploadMime);
                
                $upload->setImageExtensions($this->imageExtensions);
                
                $KB              = 1024 * 1024;
                $upload->setMaxFileSize( round($this->uploadMaxSize * $KB, 0) );
                
                // Validate
                $upload->validate();
            
                $ext = JFile::getExt( JFile::makeSafe($file["name"]) );
                
                // Generate name of the image
                $code      = substr(JApplication::getHash(time() + mt_rand()), 0, 6);
                $imageName = "extra_".$code.".".$ext;
                $dest      = $this->imagesFolder . DIRECTORY_SEPARATOR . $imageName;
                
                $upload->upload($dest);
                
                $names = array("thumb" =>"", "image" =>"");
                $names['image'] = $imageName;
                $names["thumb"] = $this->createThumb($imageName, $thumbWidth, $thumbHeight, "extra_thumb_", $scale);
                
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
    public function storeExtraImage($images, $projectId){
        
        settype($images,    "array");
        settype($projectId, "integer");
        $result = array();
        
        if(!empty($images) AND !empty($projectId)){
            
            $image = array_shift($images);
            
            $db = JFactory::getDbo();
        	/** @var $db JDatabaseMySQLi **/
            
            $query = $db->getQuery(true);
            $query
                ->insert($db->quoteName("#__vp_images"))
                ->set( $db->quoteName("image")      ."=". $db->quote($image["image"]))
                ->set( $db->quoteName("thumb")      ."=". $db->quote($image["thumb"]))
                ->set( $db->quoteName("project_id") ."=". (int)$projectId);
                
            JLog::add((string)$query, JLog::DEBUG);
            $db->setQuery($query);
            $db->query();
            
            $lastId = $db->insertid();
            
            // Add URI path to images
            $result = array(
                "id"     => $lastId, 
                "image"  => "../".$this->imagesURI."/".$image["image"],
                "thumb"  => "../".$this->imagesURI."/".$image["thumb"]
            );
            
        }
        
        return $result;
    
    }
    
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table) {
		$condition   = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}
	
}