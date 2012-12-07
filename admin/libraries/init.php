<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   ITPrism Initializators
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ITPrism Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.utilities.arrayhelper');

if(!defined("VIPPORTFOLIO_COMPONENT_ADMINISTRATOR")) {
    define("VIPPORTFOLIO_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_vipportfolio");
}

// Register Component libraries
JLoader::register("VipPortfolioVersion", VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR ."version.php");

// Register Component helpers
JLoader::register("VipPortfolioHelper", VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "vipportfoliohelper.php");
