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

/**
 * This class contains methods used 
 * in the installation process of the extension.
 *
 */
class VipPortfolioInstallHelper {
	
    public static function startTable() {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered">';
    }
    
	/**
	 * Display an HTML code for a row
	 *
	 */
	public static function addRow($title, $result, $info) {
	    
	    switch($result) {
	        case "yes":
	            $result = '<span class="label label-success">'.JText::_("JYES").'</span>';
	            break;
	        case "no":
	            $result = '<span class="label label-important">'.JText::_("JNO").'</span>';
	            break;
	        case "warning":
	            $result = '<span class="label label-warning">'.JText::_("COM_VIPPORTFOLIO_WARNING").'</span>';
	            break;
	    }
	        
	    echo '
	    <tr>
            <td>'.$title.'</td>
            <td>'.$result.'</td>
            <td>'.$info.'</td>
        </tr>';
	}
	
    public static function endTable() {
        echo "</div></table>";
    }
    
    public static function createImagesFolder($imagesPath) {
        
        // Create image folder
        if(true !== JFolder::create($imagesPath)) {
            JLog::add(JText::sprintf("COM_VIPPORTFOLIO_ERROR_CANNOT_CREATE_FOLDER", $imagesPath));
        } else {
            
            // Copy index.html
            $indexFile = $imagesPath . DIRECTORY_SEPARATOR ."index.html";
            $html = '<html><body bgcolor="#FFFFFF"></body></html>';
            if(true !== JFile::write($indexFile,$html)) {
                JLog::add(JText::sprintf("COM_VIPPORTFOLIO_ERROR_CANNOT_SAVE_FILE", $indexFile));
            }
            
        }
        
    }
}