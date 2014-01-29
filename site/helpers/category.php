<?php
/**
 * @package      Vip Portfolio
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
	
}