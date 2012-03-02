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
 * It is Vip Portfolio helper class
 *
 */
class VpHelper {
	
    public static function createFolder($folder){
        
        if(!$folder) {
            return false;
        }
        
        // Create user folder
        if(true !== JFolder::create($folder)) {
            jimport( 'joomla.error.log' );
            $log = JLog::getInstance();
            // create entry array
            $entry = array(
                'LEVEL' => '500',
                'STATUS' => "Error file creating",
                'COMMENT' => "The system could not create folder:" . $folder
            );
            // add entry to the log
            $log->addEntry($entry);
            return false;
        }
        
        // Copy index.html
        $indexFile = $folder . DS ."index.html";
        $html = '<html><body bgcolor="#FFFFFF"></body></html>';
        if(true !== JFile::write($indexFile,$html)) {
            jimport( 'joomla.error.log' );
            $log = JLog::getInstance();
            // create entry array
            $entry = array(
                'LEVEL' => '500',
                'STATUS' => "Error file creating",
                'COMMENT' => "The system could not save index.html to : " . $indexFile
            );
            // add entry to the log
            $log->addEntry($entry);
            return false;
        }
        
        return true;
    }
    
	/**
	 * Get all categories
	 * 
	 * @return array Associative array
	 */
    public static function getCategories($index = "id") {
    	
    	$db                = JFactory::getDBO();
    	$tableCategories   = $db->nameQuote('');
    	
    	$query = "
    	   SELECT
    	       *
   	       FROM
   	            `#__vp_categories`";
    	
    	$db->setQuery($query);
    	
    	return $db->loadAssocList($index);
    }
    
    /**
     * Gets a category
     * 
     * @params  integer  The category Id
     * @return array Associative array
     */
    public static function getCategory($id) {
        
    	$category = null; 
    	if (!$id) {
    		return $category;
    	}
        $db                = JFactory::getDBO();
        
        $query = "
           SELECT
               *
           FROM
               `#__vp_categories`
           WHERE
               `id` = " . (int)$id;
        
        $db->setQuery($query);
        
        $category = $db->loadObject();
        
        return $category;
    }
    
    /**
     * Gets the category name
     * @param integer $id Category Id
     */
    public static function getCategoryName($id) {
    	
        $db                = JFactory::getDBO();
        $tableCategories   = $db->nameQuote('#__vp_categories');
        $columnId          = $db->nameQuote('id');
        $columnName        = $db->nameQuote('name');
        
        $query = "
           SELECT
               $columnName 
           FROM
               $tableCategories
           WHERE
               $columnId=" . (int)$id;
        
        $name   =   $db->getOne($query);
        
        return $name;
    }
    
    /**
     * Checking for published category
     * @param integer $id Category Id
     */
    public static function isCategoryPublished($id) {
        
        $db                = JFactory::getDBO();
        $tableCategories   = $db->nameQuote('#__vp_categories');
        $columnId          = $db->nameQuote('id');
        $columnPublished   = $db->nameQuote('published');
        
        $query = "
           SELECT
               `published`
           FROM
               `#__vp_categories` 
           WHERE
               `id`=" . (int)$id;
        
        $db->setQuery($query,0,1);
        
        $published   =   $db->loadResult();
        
        return (bool)$published;
    }
    
    /**
     * Loads all projects
     * @param mixed $published  Indicator for published or not project
     * @param array $categories Category IDs
     */
    public static function getProjects($categories = null, $published = null) {
        
    	$db            = JFactory::getDBO();
        
        $query = "
           SELECT
               *
           FROM
               `#__vp_projects`
           WHERE 1";
               
        // Gets only published or not published
        if (!is_null($published)){
            if ($published) {
                $query .= " AND published=1";
            } else {
                $query .= " AND published=0";
            }
        }
        
        if (!is_null($categories)){
            settype($categories,"array");
            if (!empty($categories)){
                $query .= " AND `catid` IN (" . implode(",",$categories) . ")";
            }
        }
               
        $db->setQuery($query);

        $result = $db->loadAssocList();
        
        if ($db->getErrorNum()) {
            throw new ItpException($db->getErrorMsg(),500);
        }
        
        return $result;
        
    }
    
    public static function getExtraImages($id){
    	
    	settype($id,"integer");
    	$images = array();
    	if (!$id){
    		return $images;
    	}
    	
    	$db                = JFactory::getDBO();
        
    	$query ="
	    	SELECT
	    	   *
	    	FROM
	    	   `#__vp_images`
	        WHERE
	            `projects_id`=" . (int)$id;
    	
        $db->setQuery($query);
        $images = $db->loadAssocList();
        
        if ($db->getErrorNum()) {
            throw new ItpException($db->getErrorMsg(),500);
        }
        
        return $images;
    }
    
    public static function getImageSize($image,$percent = false){
        
        jimport('joomla.filesystem.file');
        if(!JFile::exists($image)) {
            return false;
        }
        
        $size = getimagesize($image);   
        if(false === $size) {
            return false;
        }
        
        if(!empty($percent)) {
            if($size[1] > 1000){
                // Width
                $size[0] = abs(($size[0] / 100) * $percent);
                
                // Height
                $size[1] = abs(($size[1] / 100) * $percent);
            }
        }
        
        return $size;
        
    }
}