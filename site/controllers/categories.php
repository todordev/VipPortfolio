<?php
/**
 * @package      ITPrism Components
 * @subpackage   Vip Portfolio
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Portfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.controller' );

/**
 * VipPortfolio categories Controller
 *
 * @package     ITPrism Components
 * @subpackage  Vip Portfolio
  */
class VipPortfolioControllerCategories extends JController {
    
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
        $viewName  = JRequest::getCmd('view', 'categories');
        JRequest::setVar('view', $viewName);

        $safeurlparams = array(
            'id'                => 'INT',
            'limit'             => 'INT',
            'limitstart'        => 'INT',
            'filter_order'      => 'CMD',
            'filter_order_Dir'  => 'CMD',
            'lang'              => 'CMD',
            'layout'            => 'CMD',
        );

        try {
            return parent::display($cachable, $safeurlparams);
        } catch ( Exception $e ) {
            
            $itpSecurity = new ItpSecurity( $e );
            $itpSecurity->AlertMe();
           
            JError::raiseError( 500, JText::_( 'ITP_ERROR_SYSTEM' ) );
            jexit( JText::_( 'ITP_ERROR_SYSTEM' ) );
            
        }
        
    }

}