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

/**
 * It is Vip Portfolio helper class
 */
class VipPortfolioHelper {
	
    public static $extension         = 'com_vipportfolio';
    
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'dashboard') {
	    
	    JSubMenuHelper::addEntry(
			JText::_('COM_VIPPORTFOLIO_DASHBOARD'),
			'index.php?option='.self::$extension.'&view=dashboard',
			$vName == 'dashboard'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VIPPORTFOLIO_CATEGORIES'),
			'index.php?option='.self::$extension.'&view=categories',
			$vName == 'categories'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VIPPORTFOLIO_PROJECTS'),
			'index.php?option='.self::$extension.'&view=projects',
			$vName == 'projects'
		);
		
	}
	
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
    	    ->from($db->quoteName("#__vp_categories") . " AS a")
    	    ->order("a.name");
    	
    	$db->setQuery($query);
    	
    	return $db->loadAssocList($index);
    }
    
	/**
	 * Get all categories for using in options
	 * 
	 * @return array 
	 */
    public static function getCategoriesOption() {
    	
    	$db              = JFactory::getDBO();
    	/** @var $db JDatabaseMySQLi **/
    	
    	$query = $db->getQuery(true);
    	$query
    	    ->select("a.id AS value, a.name AS text")
    	    ->from($db->quoteName("#__vp_categories") . " AS a")
    	    ->order("a.name");
    	
    	$db->setQuery($query);
    	
    	return $db->loadAssocList();
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
        
        $db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);
        $query->select("*")
              ->from($db->quoteName("#__vp_categories") . " AS a")
              ->where("a.id = ". (int)$categoryId);
    	
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
    	
    	$db              = JFactory::getDBO();
    	/** @var $db JDatabaseMySQLi **/
    	
    	$query = $db->getQuery(true);
    	$query
    	    ->select("a.id, a.name")
    	    ->from($db->quoteName("#__vp_categories") . " AS a")
    	    ->where("a.id=".(int)$id);
    	    
        $db->setQuery($query, 0, 1);
        
        return (string)$db->getResult($query);
    }
    
    /**
     * Checking for published category
     * @param integer $id Category Id
     */
    public static function isCategoryPublished($categoryId) {
        
        $db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);
        $query->select("a.published")
              ->from($db->quoteName("#__vp_categories") . " AS a")
              ->where("a.id = ". (int)$categoryId);
        
        $db->setQuery($query,0,1);
        
        return (bool)$db->loadResult();
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
    	
        $query  = $db->getQuery(true);
        $query
            ->select("*")
            ->from($db->quoteName("#__vp_projects") ." AS a");
        
        // Gets only published or not published
        if (!is_null($published)){
            if ($published) {
                $query->where("a.published = 1");
            } else {
                $query->where("a.published = 0");
            }
        }
        
        if (!is_null($categories)){
            settype($categories,"array");
            JArrayHelper::toInteger($categories);
            
            if (!empty($categories)){
                $query->where("a.catid IN (" . implode(",",$categories) . ")");
            }
        }
               
        $query->order("a.ordering");
        $db->setQuery($query);

        $result = $db->loadAssocList();
        
        return $result;
        
    }
    
    public static function getExtraImages($projectId){
    	
    	$db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
    	
    	$query  = $db->getQuery(true);
        $query
            ->select("*")
            ->from($db->quoteName("#__vp_images") . " AS a")
            ->where("a.project_id =". (int)$projectId);
              
        $db->setQuery($query);
        
        return $db->loadAssocList();
    }
    
}