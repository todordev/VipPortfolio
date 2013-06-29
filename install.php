<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipPortfolio
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipPortfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of VipPortfolio component
 */
class pkg_vipPortfolioInstallerScript {
    
    private $imagesFolder   = "";
    private $imagesPath     = "";
    
    /**
     * method to install the component
     *
     * @return void
     */
    public function install($parent) {
    }
    
    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($parent) {
    }
    
    /**
     * method to update the component
     *
     * @return void
     */
    public function update($parent) {
    }
    
    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    public function preflight($type, $parent) {
    }
    
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent) {
    
        if(!defined("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR")) {
            define("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_vipportfolio");
        }
        
        // Register Install Helper
        JLoader::register("VipPortfolioInstallHelper", VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR ."install.php");
        
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        
        $params             = JComponentHelper::getParams("com_vipportfolio");
        $this->imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/vipportfolio"));
        $this->imagesPath   = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR.$this->imagesFolder );
        $this->bootstrap    = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."com_vipportfolio".DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR."bootstrap.min.css" );
        
        $style = '<style>'.file_get_contents($this->bootstrap).'</style>';
        echo $style;
        
        // Create images folder
        if(!is_dir($this->imagesPath)){
            VipPortfolioInstallHelper::createFolder($this->imagesPath);
        }
        
        // Start table with the information
        VipPortfolioInstallHelper::startTable();
        
        // Requirements
        VipPortfolioInstallHelper::addRowHeading(JText::_("COM_VIPPORTFOLIO_MINIMUM_REQUIREMENTS"));
        
        // Display result about verification for existing folder 
        $title  = JText::_("COM_VIPPORTFOLIO_IMAGE_FOLDER");
        $info   = $this->imagesFolder;
        if(!is_dir($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for writeable folder 
        $title  = JText::_("COM_VIPPORTFOLIO_WRITABLE_FOLDER");
        $info   = $this->imagesFolder;
        if(!is_writable($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for GD library
        $title  = JText::_("COM_VIPPORTFOLIO_GD_LIBRARY");
        $info   = "";
        if(!extension_loaded('gd') AND function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_VIPPORTFOLIO_WARNING"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JON"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for cURL library
        $title  = JText::_("COM_VIPPORTFOLIO_CURL_LIBRARY");
        $info   = "";
        if( !extension_loaded('curl') ) {
            $info   = JText::_("COM_VIPPORTFOLIO_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("COM_VIPPORTFOLIO_WARNING"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JON"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification Magic Quotes
        $title  = JText::_("COM_VIPPORTFOLIO_MAGIC_QUOTES");
        $info   = "";
        if( get_magic_quotes_gpc() ) {
            $info   = JText::_("COM_VIPPORTFOLIO_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JOFF"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);
        
        // Installed extensions
        VipPortfolioInstallHelper::addRowHeading(JText::_("COM_VIPPORTFOLIO_INSTALLED_EXTENSIONS"));
        
        // Display result about verification of installed ITPrism Library
        jimport("itprism.version");
        $title  = JText::_("COM_VIPPORTFOLIO_ITPRISM_LIBRARY");
        $info   = "";
        if( !class_exists("ITPrismVersion") ) {
            $info   = JText::_("COM_VIPPORTFOLIO_ITPRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);
        
        // End table with the information
        VipPortfolioInstallHelper::endTable();
        
        // Do upgrade 
        $this->upgradeExtension();
        
        echo JText::sprintf("COM_VIPPORTFOLIO_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_vipportfolio"));
        
    }
    
    /**
     * 
     * Do some things after upgrading
     */
    private function upgradeExtension() {
        
        // Load version class from the library
        jimport('vipportfolio.version');
        
        // If class exists, the version is larger than 3.3
        if(class_exists("VipPortfolioVersion")) {
            return;
        }
        
        JLoader::register("VipPortfolioVersion", VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR ."version.php");

        $version         = new VipPortfolioVersion();
        $currentVersion  = $version->getShortVersion();
        
        switch($currentVersion) {
            case "3.3":
                $this->upgrade3_3();
                break;
        }
            
    }
    
    /**
     * Copy files from media/vipportfolio to images/vipportfolio
     * Upgrade for version 3.3
     */
    private function upgrade3_3() {
        
        $sourceFolder      = "media".DIRECTORY_SEPARATOR."vipportfolio";
        $sourcePath        = JPATH_SITE.DIRECTORY_SEPARATOR.$sourceFolder;
        $newSourcePath     = JPATH_SITE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."tmp_vipportfolio";
        
        if(is_dir($sourcePath)) {
            if(!JFolder::copy($sourcePath, $this->imagesPath, "", true)) {
                echo JText::sprintf("COM_VIPPORTFOLIO_MESSAGE_FOLDER_NOT_COPIED",$sourceFolder, $this->imagesFolder);
            } else {
                JFolder::move($sourcePath, $newSourcePath);
                echo JText::sprintf("COM_VIPPORTFOLIO_MESSAGE_FOLDER_COPIED", $sourceFolder, $this->imagesFolder);
                
                // Make thumbnail
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                
                $query
                    ->select("image")
                    ->from("#__vp_images");

                $db->setQuery($query);
                $result = $db->loadColumn();
                
                if(!empty($result)) {
                    
                    jimport('joomla.image.image');
                    $image = new JImage();
                    
                    foreach($result as $fileName) {
                        
                        $sourceFile  = JPath::clean($this->imagesPath.DIRECTORY_SEPARATOR.$fileName);
                        
                        $newFileName = "extra_thumb_".$fileName;
                        $newFile     = JPath::clean($this->imagesPath.DIRECTORY_SEPARATOR.$newFileName);
                        
                        $ext         = (string)JFile::getExt($fileName);
                        $ext         = strtolower(JFile::makeSafe($ext));
                        
                        $image->loadFile($sourceFile);
                        if (!$image->isLoaded()) {
                            continue;
                        }
                        
                        // Resize the file
                        $image->resize(50, 50, false);
                        
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
                        
                        // Store new file name
                        $query = $db->getQuery(true);
                        $query
                            ->update("#__vp_images")
                            ->set("thumb=".$db->quote($newFileName))
                            ->where("image=".$db->quote($fileName));
                            
                        $db->setQuery($query);
                        $db->query();     
                    }
                }
            }
        }
        
    }
}
