<?php
/**
 * @package      VipPortfolio
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Item model Model
 *
 * @package        ITPrism Components
 * @subpackage     Vip Portfolio
 */
class VipPortfolioModelProject extends JModelItem
{
    /**
     * Increment the hit counter for the article.
     *
     * @param    int    $id    Optional primary key of the article to increment.
     *
     * @return    boolean    True if successful; false otherwise and internal error set.
     */
    public function hit($id)
    {
        if (!empty($id)) {

            $db = $this->getDbo();
            /** @var $db JDatabaseDriver */

            $query = $db->getQuery(true);

            $query
                ->update($db->quoteName("#__vp_projects"))
                ->set($db->quoteName("hits") ." += 1")
                ->where($db->quoteName("id"). " = " . $db->quote($id));

            $db->setQuery($query);
            $db->execute();
        }
    }
}
