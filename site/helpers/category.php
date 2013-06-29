<?php
/**
 * @package      Vip Portfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Portfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
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