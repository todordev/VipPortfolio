<?php
/**
 * @package      VipPortfolio
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class VipPortfolioModelTabbed extends JModelList {
    
    /**
     * Constructor.
     *
     * @param   array   An optional associative array of configuration settings.
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array()){
        if(empty($config['filter_fields'])){
            $config['filter_fields'] = array(
                'id', 'a.id', 
                'title', 'a.title', 
                'catid', 'a.catid', 
                'published', 'a.published'
            );
        }
        
        parent::__construct($config);
    }
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return  void
     * @since   1.6
     */
    protected function populateState($ordering = 'ordering', $direction = 'ASC'){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $params = $app->getParams();
        $this->setState('params', $params);

        // List state information        
        $value = $app->input->getInt('limit', $app->getCfg('list_limit', 0));
        $this->setState('list.limit', $value);
        
        $value = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);
        
        $this->setState('list.ordering', 'a.rgt');
        $this->setState('list.direction', 'ASC');
        
        // Get categories IDs
        $value = $app->input->get("categories_ids", array(), "array");
        $this->setState('categories_ids', $value);

    }
    
    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string      $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = ''){

        $id .= ':' . implode(",", $this->getState('categories_ids') );
        
        return parent::getStoreId($id);
    }
    
    /**
     * Get the master query for retrieving a list of projects to the model state.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    public function getListQuery(){
        
        // Create a new query object.
        $db    = $this->getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        $query = $db->getQuery(true);
        
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select', 
                'a.id, a.title, a.alias, a.params'
            )
        );
        
        $query->from($db->quoteName('#__categories') .' AS a');
        
        // Get categories
        $categoriesIds = $this->getState("categories_ids");
        if(!empty($categoriesIds)) {
            $query->where("a.id IN (".implode(",",$categoriesIds) .")");
        }
        
        // Filter by state
        $query->where('a.published = 1');
        
        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));
        
        return $query;
    }
    
    protected function getOrderString() {
        
        $orderCol   = $this->getState('list.ordering',  'a.rgt');
        $orderDirn  = $this->getState('list.direction', 'ASC');
        
        return $orderCol.' '.$orderDirn;
    }
    
}