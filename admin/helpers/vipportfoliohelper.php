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
class VipPortfolioHelper {
	
	/**
	 * Get all categories
	 * 
	 * @return array Associative array
	 */
    public static function getCategories($index = "id") {
    	
    	$db              = JFactory::getDBO();
    	/** @var $db JDatabaseMySQLi **/
    	
    	$query = $db->getQuery(true);
    	$query
    	    ->select("*")
    	    ->from("#__vp_categories");
    	
    	$db->setQuery($query);
    	
    	return $db->loadAssocList($index);
    }
    
    /**
     * Gets a category
     * 
     * @params  integer  Category Id
     * @return array Associative array
     * 
     * @return mixed object or null
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
        
        if(!$category) {
            $category = null;
        }
        
        return $category;
    }
    
    /**
     * Gets the category name
     * @param integer $id Category Id
     * 
     * @return string
     */
    public static function getCategoryName($id) {
    	
        $db               = JFactory::getDBO();
        $tableCategories  = $db->quoteName("#__vp_categories");
    	$columnId         = $db->quoteName("id");
    	$columnName       = $db->quoteName("name");
    	
    	$db              = JFactory::getDBO();
    	/** @var $db JDatabaseMySQLi **/
    	
    	$query = $db->getQuery(true);
    	$query
    	    ->select("id, name")
    	    ->from("#__vp_categories")
    	    ->where("id=".(int)$id);
    	    
        $db->setQuery($query);
        $name   =  (string)$db->getOne($query);
        
        return $name;
    }
    
    /**
     * Checking for published category
     * @param integer $id Category Id
     */
    public static function isCategoryPublished($categoryId) {
        
        $db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);
        $query->select("published")
              ->from("#__vp_categories")
              ->where("id = ". (int)$categoryId);
        
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
    	
        $images = array();
        
    	settype($projectId, "integer");
    	if (!$projectId){
    		return $images;
    	}
    	
    	$db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
    	
    	$query  = $db->getQuery(true);
        $query->select("*")
              ->from("#__vp_images")
              ->where("projects_id =". (int)$projectId);
              
        $db->setQuery($query);
        $images = $db->loadAssocList();
        
        return $images;
    }
    
}