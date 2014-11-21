<?php
/**
 * @package      VipPortfolio
 * @subpackage   Categories
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * This class provide functionality for managing categories.
 *
 * @package         VipPortfolio
 * @subpackage      Categories
 */
class VipPortfolioCategories extends JCategories
{
    /**
     * Initialize the object.
     *
     * <code>
     * $options = array(
     *     "table" => "#__vp_projects",
     *     "extension" => "com_vipportfolio",
     * );
     *
     * $portfolio = new VipPortfolioCategories($options);
     * </code>
     *
     * @param array  $options
     */
    public function __construct($options = array())
    {
        $options['table']     = '#__vp_projects';
        $options['extension'] = 'com_vipportfolio';
        parent::__construct($options);
    }

    /**
     * Return the categories in array. The keys of the array is the ID of a category.
     *
     * <code>
     * $categories = VipPortfolioCategories::getCategories();
     * </code>
     *
     * @return array
     */
    public static function getCategories()
    {
        $db = JFactory::getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("a.id, a.title")
            ->from($db->quoteName("#__categories", "a"))
            ->where("a.extension = " . $db->quote("com_vipportfolio"));

        $db->setQuery($query);

        $results = $db->loadAssocList("id", "title");

        if (!$results) {
            $results = array();
        }

        return $results;
    }
}
