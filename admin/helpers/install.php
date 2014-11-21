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
 * This class contains methods used
 * in the installation process of the extension.
 */
class VipPortfolioInstallHelper
{
    public static function startTable()
    {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered table-striped">';
    }

    /**
     * Display an HTML code for a row
     *
     * @param string $title
     * @param array  $result
     * @param string  $info
     *
     * $result = array(
     *    type => success, important, warning,
     *    text => yes, no, off, on, warning,...
     * );
     */
    public static function addRow($title, $result, $info)
    {
        $outputType = JArrayHelper::getValue($result, "type", "");
        $outputText = JArrayHelper::getValue($result, "text", "");

        $output = "";
        if (!empty($outputType) and !empty($outputText)) {
            $output = '<span class="label label-' . $outputType . '">' . $outputText . '</span>';
        }

        echo '
	    <tr>
            <td>' . $title . '</td>
            <td>' . $output . '</td>
            <td>' . $info . '</td>
        </tr>';
    }

    public static function addRowHeading($heading)
    {
        echo '
	    <tr class="info">
            <td colspan="3">' . $heading . '</td>
        </tr>';
    }

    public static function endTable()
    {
        echo "</table></div>";
    }

    public static function createFolder($path)
    {
        // Create image folder
        if (true !== JFolder::create($path)) {
            JLog::add(JText::sprintf("COM_VIPPORTFOLIO_ERROR_CANNOT_CREATE_FOLDER", $path));
        } else {

            // Copy index.html
            $indexFile = $path . DIRECTORY_SEPARATOR . "index.html";
            $html      = '<html><body style="background-color: #FFF;"></body></html>';
            if (true !== JFile::write($indexFile, $html)) {
                JLog::add(JText::sprintf("COM_VIPPORTFOLIO_ERROR_CANNOT_SAVE_FILE", $indexFile));
            }
        }

    }
}
