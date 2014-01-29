<?php
/**
 * @package      Vip Portfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Projects Controller
 *
 * @package     VipPortfolio
 * @subpackage  Components
 */
class VipPortfolioControllerProjects extends JControllerAdmin {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Project', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 * @return  void
	 * @since   3.0
	 */
	public function saveOrderAjax() {
	    
		// Get the input
		$app     = JFactory::getApplication();
		$pks     = $app->input->post->get('cid', array(), 'array');
		$order   = $app->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the item
        try {
            $model->saveorder($pks, $order);
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPPORTFOLIO_ERROR_SYSTEM'));
        }
        
        $response = array(
        	"success" => true,
            "title"=> JText::_( 'COM_VIPPORTFOLIO_SUCCESS' ),
            "text" => JText::_( 'JLIB_APPLICATION_SUCCESS_ORDERING_SAVED' ),
            "data" => array()
        );
            
        echo json_encode($response);
        JFactory::getApplication()->close();
		
	}
    
}