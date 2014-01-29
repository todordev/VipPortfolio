<?php
/**
 * @package		 VipPortfolio
 * @subpackage	 Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

class VipPortfolioProjects extends ArrayObject {
    
    /**
     * Database driver
     * 
     * @var $db JDatabaseMySQLi
     */
    protected $db;
    
    public function __construct($categoryId = null, $published = null) {
        
        $this->db = JFactory::getDBO();
        
        $items = $this->getItems($categoryId, $published);

        parent::__construct($items);
    }
    
    
    /**
     * Load all projects
     *
     * @param array $categories Category IDs
     * @param mixed $published  Indicator for published or not project
     * @return mixed array or null
     *
     */
    public function getItems($categoryId = null, $published = null) {
    
        $query  = $this->db->getQuery(true);
        $query
            ->select("*")
            ->from($this->db->quoteName("#__vp_projects") . " AS a");
    
        // Gets only published or not published
        if (!is_null($published)){
            if ($published) {
                $query->where("a.published = 1");
            } else {
                $query->where("a.published = 0");
            }
        }
    
        if (!is_null($categoryId)){
            
            if(is_array($categoryId)) {
                JArrayHelper::toInteger($categoryId);
        
                if (!empty($categoryId)){
                    $query->where("a.catid IN (" . implode(",", $categoryId) . ")");
                }
            } else {
                $query->where("a.catid = " . (int)$categoryId);
            }
        }
         
        $query->order("a.ordering");
        $this->db->setQuery($query);
    
        $results = $this->db->loadObjectList();
    
        if(!$results) {
            $results = array();
        }
        
        return $results;
    
    }
    
}

