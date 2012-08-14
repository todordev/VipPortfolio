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
    
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        
        $params     = JComponentHelper::getParams("com_vipportfolio");
        $this->imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/vipportfolio"));
        $this->imagesPath   = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR.$this->imagesFolder );
        
        // create folder
        if(!is_dir($this->imagesPath)){
        
            // Create user folder
            if(true !== JFolder::create($this->imagesPath)) {
                $message = JText::sprintf("ITP_ERROR_CANNOT_CREATE_FOLDER", $this->imagesPath);
                JLog::add($message);
                echo $message;
                return false;
            }
            
            jimport('joomla.filesystem.file');
            
            // Copy index.html
            $indexFile = $this->imagesPath . DIRECTORY_SEPARATOR ."index.html";
            $html = '<html><body bgcolor="#FFFFFF"></body></html>';
            if(true !== JFile::write($indexFile,$html)) {
                $message = JText::sprintf("ITP_ERROR_CANNOT_SAVE_FILE", $indexFile);
                JLog::add($message);
            }
            
            echo JText::sprintf("ITP_MESSAGE_FOLDER_CREATED_SUCCESSFULLY", $this->imagesFolder); 
        }
        
        // Check for writeable folder
        if(!is_writable($this->imagesPath)) {
            echo JText::sprintf("ITP_MESSAGE_FOLDER_NOT_WRITABLE", $this->imagesFolder); 
        } else {
            echo JText::sprintf("ITP_MESSAGE_FOLDER_WRITABLE", $this->imagesFolder); 
        }
        
        // Do upgrade 
        $this->upgradeExtension();
        
        echo JText::_("ITP_MESSAGE_REVIEW_SAVE_SETTINGS");
    }
    
    /**
     * 
     * Do some things after upgrading
     */
    private function upgradeExtension() {
        
        if(!defined("VIPPORTFOLIO_COMPONENT_ADMINISTRATOR")) {
            define("VIPPORTFOLIO_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_vipportfolio");
        }
        JLoader::register("VipPortfolioVersion", VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR ."version.php");

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
                echo JText::sprintf("ITP_MESSAGE_FOLDER_NOT_COPIED",$sourceFolder, $this->imagesFolder);
            } else {
                JFolder::move($sourcePath, $newSourcePath);
                echo JText::sprintf("ITP_MESSAGE_FOLDER_COPIED", $sourceFolder, $this->imagesFolder);
                
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
