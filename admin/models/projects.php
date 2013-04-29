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
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

/**
 * It is a Projects model
 * 
 * @author Todor Iliev
 * @todo gets the destination dir from parameters
 */
class VipPortfolioModelProjects extends JModelList {
    
	 /**
     * Constructor.
     *
     * @param   array   An optional associative array of configuration settings.
     * @see     JController
     * @since   1.6
     */
    public function  __construct($config = array()) {
        
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'published', 'a.published',
                'ordering', 'a.ordering',
                'name', 'b.name',
            );
        }

        parent::__construct($config);
		
    }
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        
        // Load the filter state.
        $value  = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        $value  = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $value);

        $value  = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', 0, 'integer');
        $this->setState('filter.category_id', $value);

        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.ordering', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string      $id A prefix for the store id.
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '') {
        
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }
    
   /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery() {
        
        // Create a new query object.
        $db     = $this->getDbo();
        /** @var $db JDatabaseMySQLi **/
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.url,' .
                'a.published, a.ordering, a.catid, ' . 
                'b.name as category_name' 
            )
        );
        
        $query->from($db->quoteName('#__vp_projects') .' AS a');
        $query->innerJoin($db->quoteName('#__vp_categories') .' AS b ON a.catid = b.id');

        // Filter by category
        $categoryId = $this->getState('filter.category_id');
        if (!empty($categoryId)) {
            $query->where('a.catid =' . (int)$categoryId);
        }
        
        // Filter by published state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.published = '.(int) $state);
        } else if ($state === '') {
            $query->where('(a.published IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            } else {
                $search = $db->quote('%'.$db->escape($search, true).'%');
                $query->where('(a.title LIKE '.$search.')');
            }
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }
    
    protected function getOrderString() {
        
        $orderCol   = $this->getState('list.ordering',  'a.id');
        $orderDirn  = $this->getState('list.direction', 'asc');
        if ($orderCol == 'a.ordering') {
            $orderCol = 'a.catid '.$orderDirn.', a.ordering';
        }
        
        return $orderCol.' '.$orderDirn;
    }
}