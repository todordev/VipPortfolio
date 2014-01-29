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

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Component Route Helper that help to find a menu item.
 * IMPORTANT: It help us to find right MENU ITEM.
 * 
 * Use router ...BuildRoute to build a link
 *
 * @static
 * @package		ITPrism Components
 * @subpackage	VipPortfolio
 * @since 1.5
 */
abstract class VipPortfolioHelperRoute {
    
	protected static $lookup;

	/**
	 * Routing a link for list view.
	 * 
	 * @param integer $catid
	 */
	public static function getListViewRoute($catid) {
	    
		if ($catid instanceof JCategoryNode) {
			$id       = $catid->id;
			$category = $catid;
		} else {
			$id       =  (int)$catid;
			$category = JCategories::getInstance('VipPortfolio')->get($id);
		}

		if ($id < 1) {
			$link = '';
		} else {
			$needles = array(
				'list'   => array($id)
			);

			// Get menu item ( Itemid )
			if ($item = self::_findItem($needles)) {
			    
				$link = 'index.php?Itemid='.$item;
			
			} else { // Continue to search and deep inside
			    
				//Create the link
				$link = 'index.php?option=com_vipportfolio&view=list&id='.$id;

				if ($category) {
					$catids  = array_reverse($category->getPath());
					
					$needles = array(
						'list' => $catids
					);
					
					// Looking for menu item (Itemid)
					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					} elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}
	
	/**
	 * Routing a link for lineal view.
	 * 
	 * @param integer $catid
	 */
	public static function getLinealViewRoute($catid, $offset) {
	    
		if ($catid instanceof JCategoryNode) {
			$id       = $catid->id;
			$category = $catid;
		} else {
			$id       =  (int)$catid;
			$category = JCategories::getInstance('VipPortfolio')->get($id);
		}

		if ($id < 1) {
			$link = '';
		} else {
			$needles = array(
				'lineal'   => array($id)
			);

			// Get menu item ( Itemid )
			if ($item = self::_findItem($needles)) {
			    
				$link = 'index.php?Itemid='.$item;
			
			} else { // Continue to search and deep inside
			    
				//Create the link
				$link = 'index.php?option=com_vipportfolio&view=lineal&id='.$id;

				if ($category) {
					$catids  = array_reverse($category->getPath());
					
					$needles = array(
						'lineal' => $catids
					);
					
					// Looking for menu item (Itemid)
					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					} elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		if(!empty($offset)){
		  $link .= "&start=".(int)$offset;
		}
		return $link;
	}
	
	/**
	 * Routing a link for camera view.
	 * 
	 * @param integer $catid
	 */
	public static function getCameraViewRoute($catid) {
	    
		if ($catid instanceof JCategoryNode) {
			$id       = $catid->id;
			$category = $catid;
		} else {
			$id       =  (int)$catid;
			$category = JCategories::getInstance('VipPortfolio')->get($id);
		}

		if ($id < 1) {
			$link = '';
		} else {
			$needles = array(
				'camera'   => array($id)
			);

			// Get menu item ( Itemid )
			if ($item = self::_findItem($needles)) {
			    
				$link = 'index.php?Itemid='.$item;
			
			} else { // Continue to search and deep inside
			    
				//Create the link
				$link = 'index.php?option=com_vipportfolio&view=camera&id='.$id;

				if ($category) {
					$catids  = array_reverse($category->getPath());
					
					$needles = array(
						'camera' => $catids
					);
					
					// Looking for menu item (Itemid)
					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					} elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}
	
	/**
	 * Routing a link for slide gallery view.
	 * 
	 * @param integer $catid
	 */
	public static function getSlideGalleryViewRoute($catid) {
	    
		if ($catid instanceof JCategoryNode) {
			$id       = $catid->id;
			$category = $catid;
		} else {
			$id       =  (int)$catid;
			$category = JCategories::getInstance('VipPortfolio')->get($id);
		}

		if ($id < 1) {
			$link = '';
		} else {
			$needles = array(
				'slidegallery'   => array($id)
			);

			// Get menu item ( Itemid )
			if ($item = self::_findItem($needles)) {
			    
				$link = 'index.php?Itemid='.$item;
			
			} else { // Continue to search and deep inside
			    
				//Create the link
				$link = 'index.php?option=com_vipportfolio&view=slidegallery&id='.$id;

				if ($category) {
					$catids  = array_reverse($category->getPath());
					
					$needles = array(
						'slidegallery' => $catids
					);
					
					// Looking for menu item (Itemid)
					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					} elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}
	
	/**
	 * Routing a link for galleria view.
	 *
	 * @param integer $catid
	 */
	public static function getGalleriaViewRoute($catid) {
	     
	    if ($catid instanceof JCategoryNode) {
	        $id       = $catid->id;
	        $category = $catid;
	    } else {
	        $id       =  (int)$catid;
	        $category = JCategories::getInstance('VipPortfolio')->get($id);
	    }
	
	    if ($id < 1) {
	        $link = '';
	    } else {
	        $needles = array(
                'galleria'   => array($id)
	        );
	
	        // Get menu item ( Itemid )
	        if ($item = self::_findItem($needles)) {
	             
	            $link = 'index.php?Itemid='.$item;
	            	
	        } else { // Continue to search and deep inside
	             
	            //Create the link
	            $link = 'index.php?option=com_vipportfolio&view=galleria&id='.$id;
	
	            if ($category) {
	                $catids  = array_reverse($category->getPath());
	                	
	                $needles = array(
                        'galleria' => $catids
	                );
	                	
	                // Looking for menu item (Itemid)
	                if ($item = self::_findItem($needles)) {
	                    $link .= '&Itemid='.$item;
	                } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
	                    $link .= '&Itemid='.$item;
	                }
	            }
	        }
	    }
	
	    return $link;
	}
	
	
	/**
	 * Routing a link for tabbed view.
	 * 
	 */
	public static function getTabbedViewRoute() {
	    
		/**
	     *
	     * # category
	     * We will check for view category first. If find a menu item with view "category" and "id" eqallity of the key,
	     * we will get that menu item ( Itemid ).
	     *
	     * # categories view
	     * If miss a menu item with view "category" we continue with searchin but now for view "categories".
	     * It is assumed view "categories" will be in the first level of the menu.
	     * The view "categories" won't contain category ID so it has to contain 0 for ID key.
	     */
	    $needles = array(
            'tabbed' => array(0)
	    );
	
	    //Create the link
	    $link = 'index.php?option=com_vipportfolio&view=tabbed';
	
	    // Looking for menu item (Itemid)
	    if ($item = self::_findItem($needles)) {
	        $link .= '&Itemid='.$item;
	    } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
	        $link .= '&Itemid='.$item;
	    }
	
	    return $link;
	}
	
	/**
	 * Routing a link for cateogry list view.
	 * 
	 */
	public static function getCategoryListViewRoute() {
	    
		/**
	     *
	     * # category
	     * We will check for view category first. If find a menu item with view "category" and "id" eqallity of the key,
	     * we will get that menu item ( Itemid ).
	     *
	     * # categories view
	     * If miss a menu item with view "category" we continue with searchin but now for view "categories".
	     * It is assumed view "categories" will be in the first level of the menu.
	     * The view "categories" won't contain category ID so it has to contain 0 for ID key.
	     */
	    $needles = array(
            'categorylist' => array(0)
	    );
	
	    //Create the link
	    $link = 'index.php?option=com_vipportfolio&view=categorylist';
	
	    // Looking for menu item (Itemid)
	    if ($item = self::_findItem($needles)) {
	        $link .= '&Itemid='.$item;
	    } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
	        $link .= '&Itemid='.$item;
	    }
	
	    return $link;
	}
	
	protected static function _findItem($needles = null) {
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		// Collect all menu items and creat an array that contains 
		// the ID from the query string of the menu item as a key, 
		// and the menu item id (Itemid) as a value
		// Example:
		// array( "category" => 
		//     1(catid) => 100 (Itemid),
		//     2(catid) => 101 (Itemid)
		// );
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_vipportfolio');
			$items		= $menus->getItems('component_id', $component->id);

			if ($items) {
				foreach ($items as $item) {
					if (isset($item->query) && isset($item->query['view'])) {
						$view = $item->query['view'];

						if (!isset(self::$lookup[$view])) {
							self::$lookup[$view] = array();
						}

						if (isset($item->query['id'])) {
							self::$lookup[$view][$item->query['id']] = $item->id;
						} else { // If it is a root element that have no a request parameter ID ( categories, authors ), we set 0 for an key
					        self::$lookup[$view][0] = $item->id;
						}
					}
				}
			}
		}

		if ($needles) {
		    
			foreach ($needles as $view => $ids) {
				if (isset(self::$lookup[$view])) {
					
				    foreach($ids as $id) {
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
					
				}
			}
			
		} else {
			$active = $menus->getActive();
			if ($active) {
				return $active->id;
			}
		}

		return null;
	}
	
	/**
	 * 
	 * Prepeare categories path to the segments.
	 * We use this method in the router "VipPortfolioParseRoute".
	 * 
	 * @param integer   $catId		Category Id
	 * @param array 	$segments	
	 * @param integer 	$mId 		Id parameter from the menu item query
	 */
	public static function prepareCategoriesSegments($catId, &$segments, $mId = null) {
	    
	    $menuCatid    = $mId;
		$categories   = JCategories::getInstance('VipPortfolio');
		$category     = $categories->get($catId);

		if ($category) {
			//TODO Throw error that the category either not exists or is unpublished
			$path = $category->getPath();
			$path = array_reverse($path);

			$array = array();
			foreach($path as $id) {
				if ((int)$id == (int)$mId) {
					break;
				}

				$array[] = $id;
			}
			$segments = array_merge($segments, array_reverse($array));
		}
	}
	
}
