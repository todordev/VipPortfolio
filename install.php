<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of VipPortfolio component.
 */
class pkg_vipPortfolioInstallerScript
{
    private $imagesFolder = "";
    private $imagesPath = "";

    /**
     * Method to install the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param $parent
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        if (!defined("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR")) {
            define("VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . "/components/com_vipportfolio");
        }

        // Register Install Helper
        JLoader::register("VipPortfolioInstallHelper", VIPPORTFOLIO_PATH_COMPONENT_ADMINISTRATOR . "/helpers/install.php");

        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $params             = JComponentHelper::getParams("com_vipportfolio");
        $this->imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/vipportfolio"));
        $this->imagesPath   = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . $this->imagesFolder);

        // Create images folder
        if (!is_dir($this->imagesPath)) {
            VipPortfolioInstallHelper::createFolder($this->imagesPath);
        }

        // Start table with the information
        VipPortfolioInstallHelper::startTable();

        // Requirements
        VipPortfolioInstallHelper::addRowHeading(JText::_("COM_VIPPORTFOLIO_MINIMUM_REQUIREMENTS"));

        // Display result about verification for existing folder
        $title = JText::_("COM_VIPPORTFOLIO_IMAGE_FOLDER");
        $info  = $this->imagesFolder;
        if (!is_dir($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writeable folder
        $title = JText::_("COM_VIPPORTFOLIO_WRITABLE_FOLDER");
        $info  = $this->imagesFolder;
        if (!is_writable($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);

        // Display result about verification for GD library
        $title = JText::_("COM_VIPPORTFOLIO_GD_LIBRARY");
        $info  = "";
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_VIPPORTFOLIO_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_("COM_VIPPORTFOLIO_CURL_LIBRARY");
        $info  = "";
        if (!extension_loaded('curl')) {
            $info   = JText::_("COM_VIPPORTFOLIO_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("COM_VIPPORTFOLIO_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_("COM_VIPPORTFOLIO_MAGIC_QUOTES");
        $info  = "";
        if (get_magic_quotes_gpc()) {
            $info   = JText::_("COM_VIPPORTFOLIO_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JOFF"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_("COM_VIPPORTFOLIO_PHP_VERSION");
        $info  = "";
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            $result = array("type" => "important", "text" => JText::_("COM_VIPPORTFOLIO_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);

        // Installed extensions
        VipPortfolioInstallHelper::addRowHeading(JText::_("COM_VIPPORTFOLIO_INSTALLED_EXTENSIONS"));

        // Display result about verification of installed ITPrism Library
        jimport("itprism.version");
        $title = JText::_("COM_VIPPORTFOLIO_ITPRISM_LIBRARY");
        $info  = "";
        if (!class_exists("ITPrismVersion")) {
            $info   = JText::_("COM_VIPPORTFOLIO_ITPRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipPortfolioInstallHelper::addRow($title, $result, $info);

        // End table with the information
        VipPortfolioInstallHelper::endTable();

        echo JText::sprintf("COM_VIPPORTFOLIO_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_vipportfolio"));
    }
}
