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

defined('_JEXEC') or die;

/**
 * Method to build Route
 * @param array $query
 */
function VipPortfolioBuildRoute(&$query){
    
    $segments = array();
    
    // get a menu item based on Itemid or currently active
    $app      = JFactory::getApplication();
    $menu     = $app->getMenu();
    
    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if(empty($query['Itemid'])){
        $menuItem = $menu->getActive();
    }else{
        $menuItem = $menu->getItem($query['Itemid']);
    }
    $mView  = (empty($menuItem->query['view']))  ? null : $menuItem->query['view'];
    $mCatid = (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];

    if(isset($query['view'])){
        $view = $query['view'];
        unset($query['view']);
        
        if(empty($query['Itemid'])){
            $segments[] = $view;
        }
    }
    
    if(isset($query['catid'])){
        
        $categoryId = $query['catid'];
        unset($query['catid']);
        
        static $categories = array();
        
        if(!$categories){
            $db = JFactory::getDbo();
            $db->setQuery("
              SELECT 
                  `id`,
                  `alias`
              FROM
                  `#__vp_categories`");
            
            $categories = $db->loadAssocList("id");
        
        }
        
        if(array_key_exists($categoryId, $categories)){
            $segments[] = $categories[$categoryId]['alias'];
        }
    
    }
    
    // Check for existing layout
    if(isset($query['layout'])){
        unset($query['layout']);
    }
    
    if(isset($query['format'])){
        unset($query['format']);
    }
    
    return $segments;
}

/**
 * Method to parse Route
 * @param array $segments
 */
function VipPortfolioParseRoute($segments){
    
    // Debug
//    JLog::addLogger(array('text_file' => 'router_log.php'));
//    JLog::add(var_export($segments, true));

    $query = array();
    
    // Get the active menu item.
    $app            = JFactory::getApplication();
    $menu           = $app->getMenu();
    $menuItem       = $menu->getActive();
    
    $count          = count($segments);
    $categoryAlias  = null;
    
    if(is_null($menuItem)) {
        $query['view']   = $segments[0];
        return $query;
    } else {
        
        // Get the category id from the menu item
        if(isset($menuItem->query['projects_view'])) {
            $query['view']  = $menuItem->query['projects_view'];
        }
        
        // Get the category id from the menu item
        if(isset($menuItem->query['catid'])) {
            $query['catid']  = intval($menuItem->query['catid']);
        }
        
        // Get the alias from the URI 
        // because missing the parameter "catid" in the object $menuItem.
        if(!isset($query['catid']) AND isset($segments[$count-1])) {
            $categoryAlias = $segments[$count-1];
        }
        
    }
    
    // Get catid using category alias
    if(!isset($query['catid']) AND !empty($categoryAlias) ){
        
        static $categories = array();
        
        if(!$categories){
            
            $db     = JFactory::getDBO();
            /** @var $db JDatabaseMySQLi **/ 
            
            $sql  = $db->getQuery(true);
            $sql
                ->select("`id`, `alias`")
                ->from("`#__vp_categories`");
            
            $db->setQuery($sql);
            $categories_ = $db->loadAssocList();
            
            foreach($categories_ as $category){
                $categories[$category['id']] = $category['alias'];
            }
        
        }
        
        $alias      = array_pop($segments);
        $alias      = str_replace(":", "-", $alias);

        $categoryId = array_search($alias, $categories);
        
        if(false === $categoryId){
            return $query;
        }
        
        $query['catid'] = intval($categoryId);
    
    }
    
    return $query;
}