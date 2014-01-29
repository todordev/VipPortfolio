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

jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

/**
 * It is a project model
 * 
 * @author Todor Iliev
 * @todo gets the destination dir from parameters
 */
class VipPortfolioModelTab extends JModelAdmin {
    
    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_VIPPORTFOLIO';
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Tab', $prefix = 'VipPortfolioTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Stock method to auto-populate the model state.
     *
     * @return  void
     *
     * @since   11.1
     */
    protected function populateState() {
    
        parent::populateState();
         
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
    
        // Facebook page ID
        $pageId = $app->getUserStateFromRequest($this->option.'.tabs.pid', 'pid', 0);
        $this->setState('page_id', $pageId);
    
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array   $data       An optional array of data for the form to interogate.
     * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true){

        // Get the form.
        $form = $this->loadForm($this->option.'.tab', 'tab', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
            return false;
        }
        
        return $form;
    }
    

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData(){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Check the session for previously entered form data.
        $data = $app->getUserState($this->option.'.edit.tab.data', array());
        
        if(empty($data)){
            $data          = $this->getItem();
            $data->page_id = $app->getUserState($this->option.'.tabs.pid', 'pid', 0);
        }
        
        return $data;
    }
    
    /**
     * Save item data into the DB
     * 
     * @param array $data   The data about item
     * @return     ID of the item
     */
    public function save($data){
        
        $id             = JArrayHelper::getValue($data, "id");
        $title          = JArrayHelper::getValue($data, "title");
        $appId          = JArrayHelper::getValue($data, "app_id");
        $pageId         = JArrayHelper::getValue($data, "page_id");
        $published      = JArrayHelper::getValue($data, "published");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("title", $title);
        $row->set("app_id", $appId);
        $row->set("page_id", $pageId);
        $row->set("published", $published);
        
        $row->store();
        
        return $row->id;
    
    }
    
	/**
     * Check for installed tab in the system.
     * 
     * @param integer $pageId
     * @param bigint $appId
     * @param integer $itemId
     */
    public function isInstalled($pageId, $appId, $itemId = null){
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        $query = $db->getQuery(true);
        $query
            ->select("COUNT(*)")
            ->from($db->quoteName("#__vp_tabs") . ' AS a')
            ->where("a.page_id = " . $db->quote($pageId))
            ->where("a.app_id  = " . $db->quote($appId));
            
        if(!empty($itemId)) {
            $query->where("a.id != " . (int)$itemId);
        }
            
        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();
        
        return (!$result) ? false : true;
    }
    
    /**
     * Check for installed tab on a facebook page
     * 
     * @param integer $pageId
     * @param bigint $appId
     */
    public function isInstalledFacebookTab($item, $params){
    
        // Create a Facebook object
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
    
        $facebookUserId = $facebook->getUser();
    
        if(!$facebookUserId) {
            throw new Exception(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_NOT_CONNECT"), 500);
        }
    
        // Install the page
        if(!$item->page_id) {
            throw new Exception(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_INVALID_PAGE"), 500);
        }
    
        // Check for installed tab
        $uri          = $item->page_id."/tabs/".$item->app_id;
        $tabData      = $facebook->api($uri);
    
        $data         = JArrayHelper::getValue($tabData, "data");
    
        return (!empty($data)) ? true : false;
    }
    
    /**
     * Update the information about tab on Facebook
     * 
     * @param array $data
     * @param JRegistry $params
     */
    public function installFacebookTab($item, $params) {
        
        // Create a Facebook object
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
        
        $facebookUserId = $facebook->getUser();
        
        if(!$facebookUserId) {
            throw new Exception(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_NOT_CONNECT"), 500);
        }
        
        // Install a tab
        if(!empty($item->page_id)) {
            
            // Check for installed tab
            $uri     = $item->page_id."/tabs/".$item->app_id;
            $tabData = $facebook->api($uri);
            $tabData = JArrayHelper::getValue($tabData, "data");
            
            $accessToken  = VipPortfolioHelper::getFacebookPageAccessToken($facebook, $item->page_id);
            
            // Create a tab
            if(empty($tabData)) {
            
                // Prepare a data that will be sent to facebook
                $tabParams = array(
                    "access_token" => $accessToken,
                    "app_id"	   => $item->app_id
                );
                
                $uri                 = "/".$item->page_id."/tabs/";
                $response            = $facebook->api($uri, "POST", $tabParams);
            }
            
            // Update the tab
            $tabParams = array(
                "access_token" => $accessToken,
                "custom_name"  => $item->title
            );
                
            $uri         = "/".$item->page_id."/tabs/app_".$item->app_id;
            $response    = $facebook->api($uri, "POST", $tabParams);
            
        }
        
    }
    
    /**
     * Update the information about tab on Facebook
     *
     * @param array $data
     * @param JRegistry $params
     */
    public function updateFacebookTab($item, $params) {
    
        // Create a Facebook object
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
    
        $facebookUserId = $facebook->getUser();
    
        if(!$facebookUserId) {
            throw new Exception(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_NOT_CONNECT"), 500);
        }
    
        // Update a tab
        if(!empty($item->page_id)) {
    
            // Check for installed tab
            $uri     = $item->page_id."/tabs/".$item->app_id;
            $tabData = $facebook->api($uri);
            $tabData = JArrayHelper::getValue($tabData, "data");
    
            // Update a tab
            if(!empty($tabData)) {
    
                $accessToken  = VipPortfolioHelper::getFacebookPageAccessToken($facebook, $item->page_id);
                
                // Update the tab
                $tabParams = array(
                    "access_token" => $accessToken,
                    "custom_name"  => $item->title
                );
                    
                $uri         = "/".$item->page_id."/tabs/app_".$item->app_id;
                $response    = $facebook->api($uri, "POST", $tabParams);
            }
    
        }
    
    }
    
    public function uninstallFacebookTab($item, $params) {
        
        // Create a Facebook object
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
        
        $facebookUserId = $facebook->getUser();
        
        if(!$facebookUserId) {
            throw new Exception(JText::_("COM_VIPPORTFOLIO_ERROR_FACEBOOK_NOT_CONNECT"), 500);
        }
        
        // Check for installed tab
        $uri     = $item->page_id."/tabs/".$item->app_id;
        $tabData = $facebook->api($uri);
        $tabData = JArrayHelper::getValue($tabData, "data");
        
        if(!empty($tabData)) {
            $accessToken  = VipPortfolioHelper::getFacebookPageAccessToken($facebook, $item->page_id);
            // Remove the tab
            $tabParams = array(
                "access_token" => $accessToken
            );
                
            $uri         = "/".$item->page_id."/tabs/app_".$item->app_id;
            $facebook->api($uri, "DELETE", $tabParams);
        }
    }
    
}