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

jimport('joomla.application.component.controllerform');

/**
 * Category controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Vip Portfolio
 * @since		1.6
 */
class VipPortfolioControllerCategory extends JControllerForm {
    
    // Check the table in so it can be edited.... we are done with it anyway
    private    $defaultLink = 'index.php?option=com_vipportfolio';
    
    /**
     * Save an item
     *
     */
    public function save() {
        
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $msg = "";

        // Gets the data from the form
        $data       = JRequest::getVar('jform', array(), 'post', 'array');
        
        $model = $this->getModel( "Category", "VipPortfolioModel");
        
        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);
        /* @var $form JForm */
       
        try {
            
            if (!$form) {
                throw new ItpException($model->getError(), 500);
            }
            
            // Test if the data is valid.
            $validData = $model->validate($form, $data);
    
            // Check for validation errors.
            if ($validData === false) {
                $itemId = JArrayHelper::getValue($data, "id");
                // Get the validation messages.
                throw new ItpUserException($model->getError(), 500);
            }
            
            $itemId = $model->save($validData);
            $msg = JText::_( 'COM_VIPPORTFOLIO_CATEGORY_SAVED' );

        } catch ( ItpUserException $e ) {
            
            JError::raiseWarning(500, $e->getMessage());
            $this->defaultLink .= "&view=category&layout=edit";
            if(!empty($itemId)) {
                $this->defaultLink .= "&id=" . (int)$itemId; 
            }
            $this->setRedirect(JRoute::_($this->defaultLink, false));
            return false;
            
        } catch ( Exception $e ) {
            
            $itpSecurity = new ItpSecurity( $e );
            $itpSecurity->alertMe();
           
            JError::raiseError( 500, JText::_( 'ITP_ERROR_SYSTEM' ) );
            return false;
            
        }
        
        $task = $this->getTask();
        
        // Prepare redirection
        switch($task) {
            
            case "apply":
                $this->defaultLink .= "&view=category&layout=edit";
                if(!empty($itemId)) {
                    $this->defaultLink .= "&id=" . (int)$itemId; 
                }
                break;
                
            case "save2new":
                $this->defaultLink .= "&view=category&layout=edit";
                break;
                
            default:
                $this->defaultLink .= "&view=categories";
                break;
        }
        
        $this->setRedirect( JRoute::_($this->defaultLink, false), $msg );
        
    }
    
    /**
     * Delete image
     *
     */
    public function removeImage() {
        
        $id = JRequest::getInt( 'id', 0, "get" );

        try {
            
            // Gets the model
            $model = $this->getModel("Category", "VipPortfolioModel");
            $model->removeImage($id);
            
            $this->defaultLink .= "&amp;view=category&;layout=edit&id=" . (int)$id;
            $msg = JText::_('COM_VIPPORTFOLIO_IMAGE_DELETED');

        } catch ( Exception $e ) {
            
            $itpSecurity = new ItpSecurity( $e );
            $itpSecurity->alertMe();
           
            JError::raiseError( 500, JText::_( 'ITP_ERROR_SYSTEM' ) );
            jexit(JText::_( 'ITP_ERROR_SYSTEM' ));
            
        }
        
        $this->setRedirect( JRoute::_($this->defaultLink, false), $msg );
        
    }

    /**
     * Cancel operations
     *
     */
    public function cancel() {
        
        $msg = "";
        $this->setRedirect( JRoute::_($this->defaultLink . "&view=categories", false), $msg );
        
    }
    
}