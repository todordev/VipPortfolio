<?php
/**
 * @package      VipPortfolio
 * @subpackage   Initializator
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

if (!defined("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR")) {
    define("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . "/components/com_vipportfolio");
}

if (!defined("VIPPORTFOLIO_PATH_COMPONENT_SITE")) {
    define("VIPPORTFOLIO_PATH_COMPONENT_SITE", JPATH_SITE . "/components/com_vipportfolio");
}

if (!defined("VIPPORTFOLIO_PATH_LIBRARY")) {
    define("VIPPORTFOLIO_PATH_LIBRARY", JPATH_LIBRARIES . "/vipportfolio");
}

// Import libraries
jimport('joomla.utilities.arrayhelper');

// Register libraries and helpers
JLoader::register("VipPortfolioHelper", VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR . "/helpers/vipportfolio.php");
JLoader::register("VipPortfolioHelperRoute", VIPPORTFOLIO_PATH_COMPONENT_SITE . "/helpers/route.php");

// Register some classes
JLoader::register("VipPortfolioCategories", VIPPORTFOLIO_PATH_LIBRARY . "/categories.php");
JLoader::register("Facebook", VIPPORTFOLIO_PATH_LIBRARY . "/facebook/facebook.php");
JLoader::register("VipPortfolioVersion", VIPPORTFOLIO_PATH_LIBRARY . "/version.php");

// Register some helpers
JHtml::addIncludePath(VIPPORTFOLIO_PATH_COMPONENT_SITE . '/helpers/html');
