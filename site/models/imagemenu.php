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

jimport('joomla.application.component.modellist');

class VipPortfolioModelImageMenu extends JModelList {
    
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
                'published', 'a.published', 
                'ordering', 'a.ordering'
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
        
        // Get parameters
        $params    = $app->getParams();
        $this->setState('params', $params);
        
        $this->setState('list.ordering', 'a.ordering');
        $this->setState('list.direction', 'ASC');
        
        $categoriesIds = $app->input->get("categories_ids", array(), "array");
        $this->setState('categories_ids', $categoriesIds);
        
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
            $this->getState('list.select', 
            	'a.id, a.name, a.alias, a.image'
            )
        );
        
        $query->from($db->quoteName('#__vp_categories') .' AS a');
        
        // Get categories
        $categoriesIds = $this->getState("categories_ids");
        if(!empty($categoriesIds)) {
            $query->where("a.id IN (".implode(",", $categoriesIds) .")");
        }
        
        // Filter by state
        $query->where('a.published = 1');
        
        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));
        
        return $query;
    }
    
    protected function getOrderString() {
        
        $orderCol   = $this->getState('list.ordering',  'a.ordering');
        $orderDirn  = $this->getState('list.direction', 'ASC');
        
        return $orderCol.' '.$orderDirn;
    }
    
}