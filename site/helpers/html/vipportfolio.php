<?php
/**
 * @package      Vip Portfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Vip Portfolio Html Helper
 *
 * @package        Vip Portfolio
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlVipPortfolio
{
    protected static $extension = "com_vipportfolio";

    /**
     * @var   array   array containing information for loaded files
     */
    protected static $loaded = array();

    public static function boolean($value)
    {
        if (!$value) { // unpublished
            $title = "JUNPUBLISHED";
            $class = "unpublish";
        } else {
            $title = "JPUBLISHED";
            $class = "ok";
        }

        $html[] = '<a class="btn btn-micro" rel="tooltip" ';
        $html[] = ' href="javascript:void(0);" ';
        $html[] = ' title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
        $html[] = '<i class="icon-' . $class . '"></i>';
        $html[] = '</a>';

        return implode($html);
    }

    public static function lightbox_nivo()
    {

        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        $document = JFactory::getDocument();

        $document->addStylesheet(JUri::root() . 'media/' . self::$extension . '/js/nivo/nivo-lightbox.css');
        $document->addStylesheet(JUri::root() . 'media/' . self::$extension . '/js/nivo/themes/default/default.css');
        $document->addScript(JUri::root() . 'media/' . self::$extension . '/js/nivo/nivo-lightbox.js');

        self::$loaded[__METHOD__] = true;

    }

    public static function lightbox_duncan()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        $document = JFactory::getDocument();

        $document->addStylesheet(JUri::root() . 'media/' . self::$extension . '/js/duncan/jquery.lightbox.min.css');
        $document->addScript(JUri::root() . 'media/' . self::$extension . '/js/duncan/jquery.lightbox.min.js');

        self::$loaded[__METHOD__] = true;
    }
}
