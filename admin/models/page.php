<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Category model.
 *
 * @package        VipPortfolio
 * @subpackage     Components
 * @since          1.5
 */
class VipPortfolioModelPage extends JModelLegacy
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param    string $type      The table type to instantiate
     * @param    string $prefix   A prefix for the table class name. Optional.
     * @param    array  $config   Configuration array for model. Optional.
     *
     * @return    JTable    A database object
     * @since    1.6
     */
    public function getTable($type = 'Page', $prefix = 'VipPortfolioTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Load data about Facebook pages and store it into database.
     *
     * @param Facebook $facebook
     *
     * @throws Exception
     */
    public function update($facebook)
    {
        $pagesIds = array();

        $pages = VipPortfolioHelper::getFacebookPages($facebook);

        // Get extra data from Facebook
        if (!empty($pages)) {

            foreach ($pages as &$page) {

                // Collect pages IDs
                $pagesIds[] = JArrayHelper::getValue($page, "id");

                try {

                    $fql = "
                    	SELECT page_url, pic, pic_square, fan_count, type, is_published
                    	FROM 
                    		page 
                		WHERE 
                			page_id = " . $page["id"];

                    $param     = array(
                        'method'   => 'fql.query',
                        'query'    => $fql,
                        'callback' => ''
                    );
                    $fqlResult = $facebook->api($param);

                    if (!empty($fqlResult)) {
                        if (isset($fqlResult[0])) {
                            $page["page_url"]   = JArrayHelper::getValue($fqlResult[0], "page_url");
                            $page["pic"]        = JArrayHelper::getValue($fqlResult[0], "pic");
                            $page["pic_square"] = JArrayHelper::getValue($fqlResult[0], "pic_square");
                            $page["fan_count"]  = JArrayHelper::getValue($fqlResult[0], "fan_count");
                            $page["type"]       = JArrayHelper::getValue($fqlResult[0], "type");
                            $page["published"]  = JArrayHelper::getValue($fqlResult[0], "is_published");
                        }
                    }

                } catch (Exception $e) {
                    throw new Exception($e->getMessage(), $e->getCode(), $e);
                }
            }

            // Update data
            $this->updateAccounts($pages);
            $this->removeMissedPages($pagesIds);
        }
    }

    /**
     *
     * Store data into database
     *
     * @param array $pages
     */
    private function updateAccounts($pages)
    {
        foreach ($pages as $page) {
            $table = $this->getTable();

            // Load data
            $table->load(array("page_id" => $page["id"]));

            // Set the new data
            $table->set("title", JArrayHelper::getValue($page, "name"));
            $table->set("page_id", JArrayHelper::getValue($page, "id"));
            $table->set("page_url", JArrayHelper::getValue($page, "page_url"));
            $table->set("pic", JArrayHelper::getValue($page, "pic"));
            $table->set("pic_square", JArrayHelper::getValue($page, "pic_square"));
            $table->set("fans", JArrayHelper::getValue($page, "fan_count"));
            $table->set("type", JArrayHelper::getValue($page, "type"));
            $table->set("published", JArrayHelper::getValue($page, "published"));

            $table->store();
        }
    }

    public function removeMissedPages($pagesIds)
    {
        if (!$pagesIds) {
            return;
        }

        $db = JFactory::getDbo();
        /** @var $db JDatabaseDriver */

        // Remove tabs
        $query = $db->getQuery(true);
        $query
            ->delete($db->quoteName("#__vp_tabs"))
            ->where($db->quoteName("page_id") . " NOT IN ( " . implode(",", $pagesIds) . ")");

        $db->setQuery($query);
        $db->execute();

        // Remove pages
        $query = $db->getQuery(true);
        $query
            ->delete($db->quoteName("#__vp_pages"))
            ->where($db->quoteName("page_id") . " NOT IN ( " . implode(",", $pagesIds) . ")");

        $db->setQuery($query);
        $db->execute();
    }
}
