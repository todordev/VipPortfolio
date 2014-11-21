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
 * It is Vip Portfolio helper class
 */
class VipPortfolioHelper
{
    public static $extension = 'com_vipportfolio';

    /**
     * Configure the Linkbar.
     *
     * @param    string  $vName  The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_VIPPORTFOLIO_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPPORTFOLIO_CATEGORIES'),
            'index.php?option=com_categories&extension=' . self::$extension,
            $vName == 'categories'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPPORTFOLIO_PROJECTS'),
            'index.php?option=' . self::$extension . '&view=projects',
            $vName == 'projects'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPPORTFOLIO_FACEBOOK_PAGES'),
            'index.php?option=' . self::$extension . '&view=pages',
            $vName == 'pages'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPPORTFOLIO_CSS_EDITOR'),
            'index.php?option=' . self::$extension . '&view=csseditor',
            $vName == 'csseditor'
        );
    }

    /**
     * Return a category
     *
     * @param  integer  $categoryId
     *
     * @return null|object
     */
    public static function getCategory($categoryId)
    {
        $db = JFactory::getDBO();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("*")
            ->from($db->quoteName("#__categories", "a"))
            ->where("a.id = " . (int)$categoryId);

        $db->setQuery($query);
        $category = $db->loadObject();

        if (!$category) {
            $category = null;
        }

        return $category;
    }

    /**
     * Gets the category name
     *
     * @param integer $id Category Id
     *
     * @return string
     */
    public static function getCategoryName($id)
    {
        $db = JFactory::getDBO();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("a.title")
            ->from($db->quoteName("#__categories", "a"))
            ->where("a.id=" . (int)$id);

        $db->setQuery($query, 0, 1);

        return (string)$db->loadResult($query);
    }

    /**
     * Checking for published category
     *
     * @param integer $categoryId
     *
     * @return bool
     */
    public static function isCategoryPublished($categoryId)
    {
        $db = JFactory::getDBO();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("a.published")
            ->from($db->quoteName("#__vp_categories", "a"))
            ->where("a.id = " . (int)$categoryId);

        $db->setQuery($query, 0, 1);

        return (bool)$db->loadResult();
    }

    /**
     * Load all projects
     *
     * @param array $categories Category IDs
     * @param mixed $published  Indicator for published or not project
     *
     * @return mixed array or null
     *
     */
    public static function getProjects($categories = null, $published = null)
    {
        $db = JFactory::getDBO();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("*")
            ->from($db->quoteName("#__vp_projects", "a"));

        // Gets only published or not published
        if (!is_null($published)) {
            if ($published) {
                $query->where("a.published = 1");
            } else {
                $query->where("a.published = 0");
            }
        }

        if (!is_null($categories)) {
            settype($categories, "array");
            JArrayHelper::toInteger($categories);

            if (!empty($categories)) {
                $query->where("a.catid IN (" . implode(",", $categories) . ")");
            }
        }

        $query->order("a.ordering");
        $db->setQuery($query);

        $result = $db->loadAssocList();

        return $result;
    }

    public static function getExtraImages($projectId)
    {
        $db = JFactory::getDBO();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("*")
            ->from($db->quoteName("#__vp_images", "a"))
            ->where("a.project_id =" . (int)$projectId);

        $db->setQuery($query);

        return $db->loadAssocList();
    }

    public static function getStyleFile($styleFile)
    {
        $filename    = "";
        $mediaFolder = JPATH_ROOT . DIRECTORY_SEPARATOR . "media" . DIRECTORY_SEPARATOR . "com_vipportfolio" . DIRECTORY_SEPARATOR;

        switch ($styleFile) {

            case 1:
                $filename .= $mediaFolder . "categories" . DIRECTORY_SEPARATOR . "categorieslist" . DIRECTORY_SEPARATOR . "style.css";
                break;

            case 2:
                $filename .= $mediaFolder . "projects" . DIRECTORY_SEPARATOR . "list" . DIRECTORY_SEPARATOR . "style.css";
                break;

            case 3:
                $filename .= $mediaFolder . "projects" . DIRECTORY_SEPARATOR . "lineal" . DIRECTORY_SEPARATOR . "style.css";
                break;

        }

        return $filename;
    }

    public static function getFacebookPageName($pageId)
    {
        $db = JFactory::getDBO();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("a.title")
            ->from($db->quoteName("#__vp_pages", "a"))
            ->where("a.page_id =" . $db->quote($pageId));

        $db->setQuery($query, 0, 1);
        $name = $db->loadResult();

        return $name;
    }

    /**
     * Make a request to facebook and get pages
     *
     * @param Facebook $facebook
     *
     * @return array
     */
    public static function getFacebookPages($facebook)
    {
        $accounts = $facebook->api("/me/accounts");
        $accounts = JArrayHelper::getValue($accounts, "data");

        $pages = array();

        if (!empty($accounts)) {

            // Get only pages and exlude applications
            foreach ($accounts as $account) {
                if (strcmp("Application", $account["category"])) {
                    $pages[] = $account;
                }
            }
        }

        return $pages;
    }

    public static function getFacebookPageAccessToken($facebook, $pageId)
    {
        $accessToken = "";
        $pages       = self::getFacebookPages($facebook);

        foreach ($pages as $page) {
            if ($pageId == $page["id"]) {
                $accessToken = $page["access_token"];
                break;
            }
        }

        return $accessToken;
    }

    /**
     * Include a JS code that grow facebook window.
     *
     * @param object $document
     * @param Joomla\Registry\Registry $params
     */
    public static function facebookAutoGrow($document, $params)
    {
        $js = 'window.fbAsyncInit = function() {
    	  FB.init({ 
  	        appId: "' . $params->get("fbpp_app_id", "") . '", 
  	        cookie : true, 
  	        status : true, 
  	        xfbml  : true,
  	        oauth  : true
  	     });

    	  FB.Canvas.setAutoGrow();
    	  
      };

      // Load the SDK Asynchronously
      (function(d){
         var js, id = "facebook-jssdk"; if (d.getElementById(id)) {return;}
         js = d.createElement("script"); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         d.getElementsByTagName("head")[0].appendChild(js);
       }(document));';

        $document->addScriptDeclaration($js);

    }

    /**
     * Prepare an image that will be used for meta data.
     *
     * @param object $category
     * @param array     $items
     *
     * @return NULL|string
     */
    public static function getIntroImage($category, $items)
    {
        $categoryParams = json_decode($category->params);

        $uri = JUri::getInstance();

        $image = null;
        if (!empty($categoryParams->image)) {
            if (0 !== strpos($categoryParams->image, "http")) {
                $imagesUri = $uri->toString(array("scheme", "host")) . "/";
                $image     = $imagesUri . $categoryParams->image;
            } else {
                $image = $categoryParams->image;
            }

        } else {
            foreach ($items as $item) {
                if (!empty($item->thumb)) {

                    $params    = JComponentHelper::getParams("com_vipportfolio");
                    /** @var  $params Joomla\Registry\Registry */

                    $imagesUri = $uri->toString(array("scheme", "host")) . "/" . $params->get("images_directory", "images/vipportfolio") . "/";

                    $image = $imagesUri . $item->thumb;
                    break;

                }
            }
        }

        return $image;
    }

    public static function getCategoryImage($categories)
    {
        foreach ($categories as $category) {
            if (!empty($category)) {
                if (!empty($category->image)) {
                    return JUri::root() . $category->image;
                }
            }
        }

        return null;
    }

    public static function getModalClass($modal)
    {

        switch ($modal) {

            case "nivo":
                $class = "js-com-nivo-modal";
                break;

            case "duncan":
                $class = "js-com-duncan-modal";
                break;

            default:
                $class = "";
                break;
        }

        return $class;
    }
}
