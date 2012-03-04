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

defined('_JEXEC') or die();

/**
 * Method to build Route
 * @param array $query
 */
function VipPortfolioBuildRoute(&$query){
    
    $segments = array();
    
    // get a menu item based on Itemid or currently active
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();
    
    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if(empty($query['Itemid'])){
        $menuItem = $menu->getActive();
    }else{
        $menuItem = $menu->getItem($query['Itemid']);
    }
    $mView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
    $mCatid = (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];
    //    $mProjectLayout    = (empty($menuItem->query['project_layout'])) ? null : $menuItem->query['project_layout'];

    if(isset($query['view'])){
        $view = $query['view'];
        if(empty($query['Itemid'])){
            $segments[] = $query['view'];
        }
        unset($query['view']);
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
        
        // Does the menu layout match with the query layout 
        if(!empty($query['Itemid']) && isset($menuItem->query['layout'])){
            if($query['layout'] == $menuItem->query['layout']){
                unset($query['layout']);
            }
        }else{
            // Check for menu item 'categories' and project_layout different from 'default'
            if(isset($menuItem->query['project_layout']) AND ($menuItem->query['project_layout'] == 'default')){
                unset($query['layout']);
            }
        }
        
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
    
    /*
    jimport('joomla.log.loggers.formattedtext');
    $loggerOptions = array();
    $entry     = new JLogEntry(var_export($segments, 1));
    $logger    = new JLoggerFormattedText($loggerOptions);
    $logger->addEntry($entry, JLog::DEBUG);
    */
    
    $query = array();
    
    //Get the active menu item.
    $app        = JFactory::getApplication();
    $menu       = $app->getMenu();
    $menuItem   = $menu->getActive();
    
    $count      = count($segments);
    $categoryAlias = null;
    
    if(!isset($menuItem)) {
        $query['view']   = $segments[0];
        return $query;
    } else {
        
//        $query['view'] = "projects";
        
        // Get the category id from the menu item
        if( isset($menuItem->query['catid'])) {
            $query['catid']  = intval($menuItem->query['catid']);
        }
        
        if(!isset($query['catid']) AND isset($segments[$count-1])) {
            $categoryAlias = $segments[$count-1];
        }
        
    }
    
    
    /**** Categories ****/
    if(!isset($query['catid']) AND !empty($categoryAlias) ){
        
        static $categories = array();
        
        if(!$categories){
            
            $db = & JFactory::getDBO();
            
            $sqlQuery = "
	              SELECT 
	                  `id`,
	                  `alias`
	              FROM
	                 `#__vp_categories`";
            
            $db->setQuery($sqlQuery);
            $categories_ = $db->loadAssocList();
            
            foreach($categories_ as $category){
                $categories[$category['id']] = $category['alias'];
            }
        
        }
        
        $alias = array_pop($segments);
        $alias = str_replace(":", "-", $alias);
        
        $categoryId = array_search($alias, $categories);
        
        if(false === $categoryId){
            return $query;
        }
        
        $query['catid'] = intval($categoryId);
    
    }
    
    return $query;
}