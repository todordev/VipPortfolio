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

jimport('joomla.application.component.controller');

class VipPortfolioController extends JControllerLegacy {
    
    /**
     * Method to display a view.
     *
     * @param   boolean         If true, the view output will be cached
     * @param   array           An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false) {
        
        // Initialise variables.
        $cachable   = true; // Huh? Why not just put that in the constructor?

        // Set the default view name and format from the Request.
        // Note we are using catid to avoid collisions with the router and the return page.
        // Frontend is a bit messier than the backend.
        $viewName  = $this->input->getCmd('view', 'categorylist');
        $this->input->set('view', $viewName);

        $safeurlparams = array(
            'id'                => 'INT',
            'limit'             => 'INT',
            'limitstart'        => 'INT',
            'filter_order'      => 'CMD',
            'filter_order_Dir'  => 'CMD',
            'lang'              => 'CMD',
            'layout'            => 'CMD',
            'catid'             => 'INT',
        );

        parent::display($cachable, $safeurlparams);
        return $this;
        
    }
	
} 