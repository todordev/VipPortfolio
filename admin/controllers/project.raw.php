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

// No direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.controllers');

/**
 * Project controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Vip Portfolio
 * @since		1.6
 */
class VipPortfolioControllerProject extends JController {
    
    /** 
     * Deletes Extra Image
     *
     */
    public function removeExtraImage() {
       
        $id     = JRequest::getInt( 'id', 0, "post" );
        
        try {
            
            // Gets the model
            $model = $this->getModel( "Project", "VipPortfolioModel" );
            $model->removeExtraImage($id);
            
            $response = array(
            	"success" => true,
                "title"=> JText::_( 'COM_VIPPORTFOLIO_SUCCESS' ),
                "text" => JText::_( 'COM_VIPPORTFOLIO_IMAGE_DELETED' ),
            );

        } catch ( Exception $e ) {
            
            $itpSecurity = new ItpSecurity( $e );
            $itpSecurity->alertMe();
           
            JError::raiseError( 500, JText::_( 'ITP_ERROR_SYSTEM' ) );
            jexit(JText::_( 'ITP_ERROR_SYSTEM' ));
            
        }
        
        echo json_encode($response);
    }
    

}