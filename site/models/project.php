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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Item model Model
 *
 * @package		ITPrism Components
 * @subpackage	Vip Portfolio
 */
class VipPortfolioModelProject extends JModelItem {
    
    /**
     * Increment the hit counter for the article.
     *
     * @param	int		Optional primary key of the article to increment.
     *
     * @return	boolean	True if successful; false otherwise and internal error set.
     */
    public function hit($id) {
        
        if(!empty($id)) {

            $db    = $this->getDbo();
            /** @var $db JDatabaseMySQLi **/
            
            $query = $db->getQuery(true);
            
            $query
                ->update("#__vp_projects")
                ->set("hits += 1")
                ->where("id = ". $db->quote($id));
                
            $db->setQuery($query);
            $db->query();
        }
    }

}
