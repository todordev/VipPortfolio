<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

/**
 * It is a project model.
 */
class VipPortfolioModelProject extends JModelAdmin {
    
    protected $imagesFolder = "";
    protected $imagesURI    = "";
    
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
            ->from($db->quoteName("#__vp_images") .' AS a')
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
    
    public function uploadImage($image, $options = array()) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $names           = array("image", "thumb");
        
        $uploadedFile    = JArrayHelper::getValue($image, 'tmp_name');
        $uploadedName    = JArrayHelper::getValue($image, 'name');
        
        // Joomla! media extension parameters
        $mediaParams     = JComponentHelper::getParams("com_media");
        
        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.image");
        
        $file           = new ITPrismFile();
        
        // Prepare size validator.
        $KB             = 1024 * 1024;
        $fileSize       = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize  = $mediaParams->get("upload_maxsize") * $KB;
        
        $sizeValidator  = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);
        
        
        // Prepare image validator.
        $imageValidator = new ITPrismFileValidatorImage($uploadedFile, $uploadedName);
        
        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $imageValidator->setMimeTypes($mimeTypes);
        
        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $imageValidator->setImageExtensions($imageExtensions);
        
        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator);
        
        // Validate the file
        $file->validate();
        
        // Generate temporary file name
        $ext   = JString::strtolower(JFile::makeSafe(JFile::getExt($image['name'])));
        
        jimport("itprism.string");
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString(6);
        
        $destFile   = $this->imagesFolder .DIRECTORY_SEPARATOR. "image_".$generatedName.".".$ext;
        
        // Prepare uploader object.
        $uploader    = new ITPrismFileUploaderLocal($image);
        $uploader->setDestination($destFile);
        
        // Upload temporary file
        $file->setUploader($uploader);
        
        $file->upload();
        
        // Get file
        $sourceFile = $file->getFile();
        
        if(!is_file($sourceFile)){
            throw new Exception('COM_VIPPORTFOLIO_ERROR_FILE_CANT_BE_UPLOADED');
        }
        
        // Resize image
        $resizeImage = JArrayHelper::getValue($options, "resize_image", false);
        $width       = JArrayHelper::getValue($options, "image_width", 500);
        $height      = JArrayHelper::getValue($options, "image_height", 600);
        $scale       = JArrayHelper::getValue($options, "image_scale", JImage::SCALE_INSIDE);
        
        if(!empty($resizeImage)) {
            $app->setUserState($this->option.".project.image_width", $width);
            $app->setUserState($this->option.".project.image_height", $height);
            $app->setUserState($this->option.".project.image_scale", $scale);
            $this->resizeImage($sourceFile, $width, $height, $scale);
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
            $thumbName = $this->createThumb($sourceFile, $width, $height, "thumb_", $scale);
        }
        
        return $names = array(
            "image" => basename($sourceFile),
            "thumb" => $thumbName
        );
        
    }
    
    /**
     * This method upload the thumnail.
     * 
     * @param array $image
     */
    public function uploadThumb($image) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $uploadedFile    = JArrayHelper::getValue($image, 'tmp_name');
        $uploadedName    = JArrayHelper::getValue($image, 'name');
        
        // Joomla! media extension parameters
        $mediaParams     = JComponentHelper::getParams("com_media");
        
        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.image");
        
        $file           = new ITPrismFile();
        
        // Prepare size validator.
        $KB             = 1024 * 1024;
        $fileSize       = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize  = $mediaParams->get("upload_maxsize") * $KB;
        
        $sizeValidator  = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);
        
        
        // Prepare image validator.
        $imageValidator = new ITPrismFileValidatorImage($uploadedFile, $uploadedName);
        
        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $imageValidator->setMimeTypes($mimeTypes);
        
        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $imageValidator->setImageExtensions($imageExtensions);
        
        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator);
        
        // Validate the file
        $file->validate();
        
        // Generate temporary file name
        $ext   = JString::strtolower(JFile::makeSafe(JFile::getExt($image['name'])));
        
        jimport("itprism.string");
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString(6);
        
        $thumbName  = "thumb_".$generatedName.".".$ext;
        $destFile   = $this->imagesFolder .DIRECTORY_SEPARATOR. $thumbName;
        
        // Prepare uploader object.
        $uploader    = new ITPrismFileUploaderLocal($image);
        $uploader->setDestination($destFile);
        
        // Upload temporary file
        $file->setUploader($uploader);
        
        $file->upload();
        
        return $thumbName;
        
    }
    
    protected function resizeImage($file, $width, $height, $scale = JImage::SCALE_INSIDE) {
        
        // Make thumbnail
        
        $ext     = JString::strtolower(JFile::getExt(JFile::makeSafe($file)));
        
        $image   = new JImage();
        
        $image->loadFile($file);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('COM_VIPPORTFOLIO_ERROR_FILE_NOT_FOUND', $file));
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
		
        $image->toFile($file, $type);
        
    }
    
    protected function createThumb($file, $width, $heigh, $prefix = "thumb_", $scale = JImage::SCALE_INSIDE) {
        
        $destFolder = JPath::clean(dirname($file));
        
        $ext        = JString::strtolower(JFile::makeSafe(JFile::getExt($file)));
        
        $image   = new JImage();
        $image->loadFile($file);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('COM_VIPPORTFOLIO_ERROR_FILE_NOT_FOUND', $file));
        }
        
        // Resize the file as a new object
        $thumb     = $image->resize($width, $heigh, true, $scale);
        
        jimport("itprism.string");
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString(6);
        
        $thumbName = $prefix.$generatedName. "." .$ext;
        $thumbFile = $destFolder.DIRECTORY_SEPARATOR.$thumbName;
        
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
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $KB             = 1024 * 1024;
        
        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.image");
        
        // Joomla! media extension parameters
        $mediaParams     = JComponentHelper::getParams("com_media");
        
        // check for error
        foreach($files as $image){
            
            // Upload image
            if(!empty($image['name'])){
                
                $names           = array("image", "thumb");
                
                $uploadedFile    = JArrayHelper::getValue($image, 'tmp_name');
                $uploadedName    = JArrayHelper::getValue($image, 'name');
                
                $file           = new ITPrismFile();
                
                // Prepare size validator.
                $fileSize       = (int)JArrayHelper::getValue($image, 'size');
                $uploadMaxSize  = $mediaParams->get("upload_maxsize") * $KB;
                
                $sizeValidator  = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);
                
                
                // Prepare image validator.
                $imageValidator = new ITPrismFileValidatorImage($uploadedFile, $uploadedName);
                
                // Get allowed mime types from media manager options
                $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
                $imageValidator->setMimeTypes($mimeTypes);
                
                // Get allowed image extensions from media manager options
                $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
                $imageValidator->setImageExtensions($imageExtensions);
                
                $file
                    ->addValidator($sizeValidator)
                    ->addValidator($imageValidator);
                
                // Validate the file
                $file->validate();
                
                // Generate temporary file name
                $ext   = JString::strtolower(JFile::makeSafe(JFile::getExt($image['name'])));
                
                jimport("itprism.string");
                $generatedName = new ITPrismString();
                $generatedName->generateRandomString(6);
                
                $imageName  = "extra_".$generatedName.".".$ext;
                $destFile   = $this->imagesFolder .DIRECTORY_SEPARATOR. $imageName;
                
                // Prepare uploader object.
                $uploader    = new ITPrismFileUploaderLocal($image);
                $uploader->setDestination($destFile);
                
                // Upload temporary file
                $file->setUploader($uploader);
                
                $file->upload();
                
                // Get file
                $sourceFile = $file->getFile();
                
                if(!is_file($sourceFile)){
                    throw new Exception('COM_VIPPORTFOLIO_ERROR_FILE_CANT_BE_UPLOADED');
                }
                
                $names = array("thumb" =>"", "image" =>"");
                
                $names['image'] = $imageName;
                $names["thumb"] = $this->createThumb($sourceFile, $thumbWidth, $thumbHeight, "extra_thumb_", $scale);
                
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
	
	
	public function setImagesFolder($folder) {
	    $this->imagesFolder = JPath::clean($folder);
	}
	public function setImagesUri($uri) {
	    $this->imagesURI = $uri;
	}
}