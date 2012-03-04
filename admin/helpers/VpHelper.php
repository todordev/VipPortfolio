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
           
            $registry = JRegistry::getInstance("loggerOptions");
            $loggerOptions = $registry->toArray();
            
            $entry     = new JLogEntry("The system could not create folder:" . $folder);
            $logger    = new JLoggerFormattedText($loggerOptions);
            $logger->addEntry($entry, JLog::ALERT);
            return false;
        }
        
        // Copy index.html
        $indexFile = $folder . DS ."index.html";
        $html = '<html><body bgcolor="#FFFFFF"></body></html>';
        if(true !== JFile::write($indexFile,$html)) {
            $registry = JRegistry::getInstance("loggerOptions");
            $loggerOptions = $registry->toArray();
            
            $entry     = new JLogEntry("The system could not save index.html to : " . $folder);
            $logger    = new JLoggerFormattedText($loggerOptions);
            $logger->addEntry($entry, JLog::ALERT);
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
    	
    	$db              = JFactory::getDBO();
    	$tableCategories = $db->quoteName("#__vp_categories");
    	
    	$query = "
    	   SELECT
    	       *
   	       FROM
   	           " .$tableCategories;
    	
    	$db->setQuery($query);
    	
    	return $db->loadAssocList($index);
    }
    
    /**
     * Gets a category
     * 
     * @params  integer  Category Id
     * @return array Associative array
     */
    public static function getCategory($categoryId) {
        
        $category = array(); 
        if (!$categoryId) {
    		return $category;
    	}
        
        $db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
        
        $tableCategories = $db->quoteName("#__vp_categories");
    	$columnId        = $db->quoteName("id");
    	
        $query  = $db->getQuery(true);
        $query->select("*")
              ->from($tableCategories)
              ->where($columnId." = ". (int)$categoryId);
    	
        $db->setQuery($query);
        
        $category = $db->loadObject();
        
        return $category;
    }
    
    /**
     * Gets the category name
     * @param integer $id Category Id
     */
    public static function getCategoryName($id) {
    	
        $db               = JFactory::getDBO();
        $tableCategories  = $db->quoteName("#__vp_categories");
    	$columnId         = $db->quoteName("id");
    	$columnName       = $db->quoteName("name");
    	
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
    public static function isCategoryPublished($categoryId) {
        
        $db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
        
        $tableCategories = $db->quoteName("#__vp_categories");
    	$columnPublished = $db->quoteName("published");
    	$columnId        = $db->quoteName("id");
    	
        $query  = $db->getQuery(true);
        $query->select($columnPublished)
              ->from($tableCategories)
              ->where($columnId." = ". (int)$categoryId);
        
        $db->setQuery($query,0,1);
        
        $published   =   $db->loadResult();
        
        return (bool)$published;
    }
    
    /**
     * Load all projects
     * 
     * @param array $categories Category IDs
     * @param mixed $published  Indicator for published or not project
     * @return mixed array or null
     * 
     */
    public static function getProjects($categories = null, $published = null) {
        
    	$db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
    	
    	$tableProjects   = $db->quoteName("#__vp_projects");
    	$columnPublished = $db->quoteName("published");
    	$columnCatId     = $db->quoteName("catid");
    	$columnOrdering  = $db->quoteName("ordering");
    	
        $query  = $db->getQuery(true);
        $query->select("*")
              ->from($tableProjects);
        
        // Gets only published or not published
        if (!is_null($published)){
            if ($published) {
                $query->where($columnPublished."=1");
            } else {
                $query->where($columnPublished."=0");
            }
        }
        
        if (!is_null($categories)){
            settype($categories,"array");
            JArrayHelper::toInteger($categories);
            
            if (!empty($categories)){
                $query->where($columnCatId." IN (" . implode(",",$categories) . ")");
            }
        }
               
        $query->order($columnOrdering);
        $db->setQuery($query);

        $result = $db->loadAssocList();
        
        return $result;
        
    }
    
    public static function getExtraImages($projectId){
    	
    	settype($projectId,"integer");
    	$images = array();
    	if (!$projectId){
    		return $images;
    	}
    	
    	$db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
    	
    	$tableImages     = $db->quoteName("#__vp_images");
    	$columnProjectsId= $db->quoteName("projects_id");
    	
        
    	$query ="
	    	SELECT
	    	   *
	    	FROM
	    	   $tableImages
	        WHERE
	           $columnProjectsId =" . (int)$projectId;
    	
        $db->setQuery($query);
        $images = $db->loadAssocList();
        
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