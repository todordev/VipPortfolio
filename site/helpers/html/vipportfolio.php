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

// no direct access
defined('_JEXEC') or die;

/**
 * Vip Portfolio Html Helper
 *
 * @package		Vip Portfolio
 * @subpackage	Components
 * @since		1.6
 */
abstract class JHtmlVipPortfolio {
    
	public static function boolean($value) {
	    
	    if(!$value) { // unpublished
		    $title  = "JUNPUBLISHED";
		    $class  = "unpublish";
	    } else {
	        $title  = "JPUBLISHED";
	        $class  = "ok";
	    }
		
		$html[] = '<a class="btn btn-micro" rel="tooltip" ';
		$html[] = ' href="javascript:void(0);" ';
		$html[] = ' title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
		$html[] = '<i class="icon-' . $class . '"></i>';
		$html[] = '</a>';
		
		return implode($html);
	}
    
}
