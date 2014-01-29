<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

class VipPortfolioCategories extends JCategories {
    
	public function __construct($options = array()) {
		$options['table']     = '#__vp_projects';
		$options['extension'] = 'com_vipportfolio';
		parent::__construct($options);
	}
	
	public static function getCategories() {
	    
	    $db = JFactory::getDbo();
	    /** @var $db JDatabaseMySQLi **/
	    
	    $query = $db->getQuery(true);
	    $query
	        ->select("id, title")
	        ->from("#__categories")
	        ->where($db->quoteName(extension). " = " . $db->quote("com_vipportfolio"));
	    
	    $db->setQuery($query);
	    return $db->loadAssocList("id", "title");
	}
	
}