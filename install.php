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
     * @todo Translate message
     */
    public function postflight($type, $parent) {
    
        jimport('joomla.filesystem.folder');
        
        $params = JComponentHelper::getParams("com_vipportfolio");
        $destFolder = $params->get("images_directory", "images/vipportfolio");
        $folder = JPATH_SITE.DIRECTORY_SEPARATOR.$destFolder;
        
        $folder = JFolder::makeSafe($folder);
        
        if(!is_dir($folder)){
        
            // Create user folder
            if(true !== JFolder::create($folder)) {
                $message = JText::sprintf("ITP_ERROR_CANNOT_CREATE_FOLDER", $folder);
                JLog::add($message);
                echo $message;
                return false;
            }
            
            jimport('joomla.filesystem.file');
            
            // Copy index.html
            $indexFile = $folder . DIRECTORY_SEPARATOR ."index.html";
            $html = '<html><body bgcolor="#FFFFFF"></body></html>';
            if(true !== JFile::write($indexFile,$html)) {
                $message = JText::sprintf("ITP_ERROR_CANNOT_SAVE_FILE", $indexFile);
                JLog::add($message);
            }
            
            echo JText::sprintf("ITP_MESSAGE_FOLDER_CREATED_SUCCESSFULLY", $destFolder); 
        }
        
        if(!is_writable($folder)) {
            echo JText::sprintf("ITP_MESSAGE_FOLDER_NOT_WRITABLE", $destFolder); 
        } else {
            echo JText::sprintf("ITP_MESSAGE_FOLDER_WRITABLE", $destFolder); 
        }
        
    }
}
