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
defined('_JEXEC') or die;

jimport( 'joomla.application.component.controlleradmin' );

/**
 * Vip Portfolio Categories Controller
 *
 * @package     ITPrism Components
 * @subpackage  Vip Portfolio
  */
class VipPortfolioControllerCategories extends JControllerAdmin {
    
    /**
     * 
     * A link to the extension
     * @var string
     */
    private    $defaultLink = 'index.php?option=com_vipportfolio';
    
    /**
     * @var     string  The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_VIPPORTFOLIO';
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Category', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    
    public function delete() {
        
        // Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $cid = $app->input->post->get('cid', array(), "array");
        
		if (!is_array($cid) OR empty($cid)){
		    throw new Exception(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		
		// Get the model.
		$model = $this->getModel();

		// Make sure the item ids are integers
		JArrayHelper::toInteger($cid);

		// Remove the items.
		$pe = $model->isProjectsExists($cid);
		
		if(!$model->isProjectsExists($cid)) { // Remove categories
		    $model->delete($cid);
		    $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
		} else { // Error: Some of categories contains projects
		    $this->setMessage(JText::_("COM_VIPPORTFOLIO_ERROR_PROJECT_EXISTS"), "notice");
		}
		
		$this->setRedirect(JRoute::_($this->defaultLink. '&view=' . $this->view_list, false));
        
    }
    
    public function backToControlPanel() {
        $this->setRedirect( JRoute::_($this->defaultLink, false) );
    }
    
}