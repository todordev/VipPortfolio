<?php
/**
 * @package      VipPortfolio
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport("vipportfolio.init");

/**
 * Method to build Route
 *
 * @param array $query
 *
 * @return string
 */
function VipPortfolioBuildRoute(&$query)
{
    $segments = array();

    // get a menu item based on Itemid or currently active
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();

    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if (empty($query['Itemid'])) {
        $menuItem = $menu->getActive();
    } else {
        $menuItem = $menu->getItem($query['Itemid']);
    }

    $mOption = (empty($menuItem->query['option'])) ? null : $menuItem->query['option'];
    $mView   = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
    $mId     = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

    if (isset($query['view'])) {
        $view = $query['view'];

        if (empty($query['Itemid']) or ($mOption !== "com_vipportfolio")) {
            $segments[] = $query['view'];
        }
        unset($query['view']);
    }

    // are we dealing with a category that is attached to a menu item?
    if (isset($view) and ($mView == $view) and (isset($query['id'])) and ($mId == intval($query['id']))) {
        unset($query['view']);
        unset($query['id']);

        return $segments;
    }

    // Category view
    if (isset($query['id'])) {

        $categoryId = $query['id'];
        unset($query['id']);

        VipPortfolioHelperRoute::prepareCategoriesSegments($categoryId, $segments, $mId);
    }

    // Layout
    if (isset($query['layout'])) {
        if (!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
            if ($query['layout'] == $menuItem->query['layout']) {
                unset($query['layout']);
            }
        } else {
            if ($query['layout'] == 'default') {
                unset($query['layout']);
            }
        }
    };

    return $segments;
}

/**
 * Method to parse Route
 *
 * @param array $segments
 *
 * @return string
 */
function VipPortfolioParseRoute($segments)
{
    $vars = array();

    // Get the active menu item.
    $app      = JFactory::getApplication();
    $menu     = $app->getMenu();
    $menuItem = $menu->getActive();

    // Count route segments
    $count = count($segments);

    // Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
    // the first segment is the view and the last segment is the id of the quote, category or author.
    if (!isset($menuItem)) {
        $vars['view'] = $segments[0];
        $vars['id']   = $segments[$count - 1];

        return $vars;
    }

    // if there is only one segment, then it points to either an quote, author or a category
    // we test it first to see if it is a category.  If the id and alias match a category
    // then we assume it is a category.  If they don't we assume it is an quote

    list($id, $alias) = explode(':', $segments[0], 2);

    // First we check if it is a category
    $category = JCategories::getInstance('VipPortfolio')->get($id);
    if ($category && $category->alias == $alias) {

        // Get the category id from the menu item
        if (isset($menuItem->query['projects_view'])) {
            $vars['view'] = $menuItem->query['projects_view'];
        }
        $vars['id'] = $id;

        return $vars;
    }

    return $vars;
}
