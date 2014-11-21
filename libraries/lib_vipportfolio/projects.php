<?php
/**
 * @package         VipPortfolio
 * @subpackage      Projects
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provide functionality for managing projects.
 *
 * @package         VipPortfolio
 * @subpackage      Projects
 */
class VipPortfolioProjects implements Iterator, Countable, ArrayAccess
{
    protected $items = array();

    protected $position = 0;

    /**
     * Database driver
     *
     * @var $db JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $projects   = new VipPortfolioProjects(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Load the projects.
     *
     * <code>
     * $options  = array(
     *     "category_id" => 100,
     *     "published" => 1
     * );
     *
     * $projects   = new VipPortfolioProjects(JFactory::getDbo());
     * $projects->load($options);
     *
     * foreach( $projects as $project ) {
     *     echo $project->title;
     * }
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("*")
            ->from($this->db->quoteName("#__vp_projects", "a"));

        $categoryId = JArrayHelper::getValue($options, "category_id");
        if (!is_null($categoryId)) {

            if (is_array($categoryId)) {
                JArrayHelper::toInteger($categoryId);

                if (!empty($categoryId)) {
                    $query->where("a.catid IN (" . implode(",", $categoryId) . ")");
                }
            } else {
                $query->where("a.catid = " . (int)$categoryId);
            }
        }

        // Gets only published or not published
        $published = JArrayHelper::getValue($options, "published");
        if (!is_null($published)) {
            if ($published) {
                $query->where("a.published = 1");
            } else {
                $query->where("a.published = 0");
            }
        }

        $query->order("a.ordering");
        $this->db->setQuery($query);

        $this->items = $this->db->loadObjectList();

        if (!$this->items) {
            $this->items = array();
        }
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return (!isset($this->items[$this->position])) ? null : $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    public function count()
    {
        return (int)count($this->items);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
}
