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

/**
 * Facebook Page controller
 *
 * @package        VipPortfolio
 * @subpackage     Components
 * @since          1.6
 */
class VipPortfolioControllerPage extends JControllerAdmin
{
    // Check the table in so it can be edited.... we are done with it anyway
    private $defaultLink = 'index.php?option=com_vipportfolio';

    /**
     * Cancel operations
     */
    public function cancel()
    {
        $this->setRedirect(JRoute::_($this->defaultLink . "&view=" . $this->view_list, false));
    }
}
