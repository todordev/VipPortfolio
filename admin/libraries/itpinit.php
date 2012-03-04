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

jimport('joomla.log.loggers.formattedtext');

if(!defined("VIPPORTFOLIO_COMPONENT_ADMINISTRATOR")) {
    define("VIPPORTFOLIO_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DS. "components" . DS ."com_vipportfolio");
}

// Register ITPrism library
JLoader::register("ItpResponse",VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DS . "libraries" . DS . "itp". DS . "itpresponse.php");
JLoader::register("ItpSecurity",VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DS . "libraries" . DS . "itp". DS . "itpsecurity.php");
JLoader::register("ItpException",VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DS . "libraries" . DS . "itp". DS . "exceptions" . DS . "itpexception.php");
JLoader::register("ItpUserException",VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DS . "libraries" . DS . "itp". DS . "exceptions" . DS . "itpuserexception.php");

// Register Component libraries
JLoader::register("VpVersion",VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DS . "libraries" . DS . "vipportfolio". DS . "vpversion.php");

// Register Component helpers
JLoader::register("VpHelper",VIPPORTFOLIO_COMPONENT_ADMINISTRATOR . DS . "helpers" . DS . "VpHelper.php");

// Options of the loffer
$registry = JRegistry::getInstance("loggerOptions");
