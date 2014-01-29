<?php
/**
 * @package      VipPortfolio
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

if(!defined("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR")) {
    define("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_vipportfolio");
}

if(!defined("VIPPORTFOLIO_PATH_COMPONENT_SITE")) {
    define("VIPPORTFOLIO_PATH_COMPONENT_SITE", JPATH_SITE .DIRECTORY_SEPARATOR. "components" .DIRECTORY_SEPARATOR. "com_vipportfolio");
}

if(!defined("VIPPORTFOLIO_PATH_LIBRARY")) {
    define("VIPPORTFOLIO_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "vipportfolio");
}

// Import libraries
jimport('joomla.utilities.arrayhelper');

// Register libraries and helpers
JLoader::register("VipPortfolioHelper",  VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "vipportfolio.php");
JLoader::register("VipPortfolioHelperRoute", VIPPORTFOLIO_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");
JLoader::register("Facebook",            VIPPORTFOLIO_PATH_LIBRARY . DIRECTORY_SEPARATOR . "facebook".DIRECTORY_SEPARATOR."facebook.php");
JLoader::register("VipPortfolioVersion",  VIPPORTFOLIO_PATH_LIBRARY .DIRECTORY_SEPARATOR. "version.php");

// Register some helpers
JHtml::addIncludePath(VIPPORTFOLIO_PATH_COMPONENT_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'html');