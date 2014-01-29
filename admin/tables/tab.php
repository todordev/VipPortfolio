<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class VipPortfolioTableTab extends JTable {
    
    public function __construct( $db ) {
        parent::__construct( '#__vp_tabs', 'id', $db );
    }
    
}