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

jimport('itprism.controller.admin');

/**
 * Vip Portfolio Facebook Pages Controller
 *
 * @package     VipPortfolio
 * @subpackage  Components
  */
class VipPortfolioControllerPages extends ITPrismControllerAdmin {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Page', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function connect() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        // Get parameters
        $params = JComponentHelper::getParams($this->option);
        
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
        
        $uri         = JURI::getInstance();
        
        // Generate the link that will return the user back to administration.
        $redirectUrl = $uri->getScheme()."://".$uri->getHost().$uri->getPath()."?option=com_vipportfolio&view=pages";
        
        $loginUrl   = $facebook->getLoginUrl(
            array(
                'scope'         => 'manage_pages',
                'redirect_uri'  => $redirectUrl
            )
        );
        
        $this->setRedirect($loginUrl);
    }
    
    /**
     * 
     * Get information about Facebook pages
     */
    public function update() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        // Get parameters
        $params = JComponentHelper::getParams($this->option);
        
        $redirectOptions = array(
            "view" => $this->view_list
        );
        
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
        
        $facebookUserId = $facebook->getUser();
        if(!$facebookUserId) {
            $this->displayNotice(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_NOT_CONNECT"), $redirectOptions);
            return;
        }
        
        // Get a model
        $model = $this->getModel();
        /** @var $model VipPortfolioModelPage **/
        
        try {
            $model->update($facebookUserId, $facebook);
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            $this->displayError(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK"), $redirectOptions);
            return;
        }
        
        $this->displayMessage(JText::_("COM_VIPPORTFOLIO_FACEBOOK_PAGES_UPDATED"), $redirectOptions);
        
    }
    
    
}