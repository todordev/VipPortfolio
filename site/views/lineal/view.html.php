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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class VipPortfolioViewLineal extends JView {

    protected $state;
    protected $items;
    protected $pagination;
    
    protected $event;
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    /**
     * Display the view
     *
     * @return  mixed   False on error, null otherwise.
     */
    function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Check for valid category
        $this->categoryId = $app->input->get->getInt("catid");
        $this->category   = null;
        
        if(!empty($this->categoryId)){
            $this->category = VipPortfolioHelper::getCategory($this->categoryId);
            // Checking for published category
            if(!$this->category OR !$this->category->published){
                throw new Exception(JText::_("COM_VIPPORTFOLIO_ERROR_CATEGORY_DOES_NOT_EXIST"));
            }
        }
        
        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params     = $this->state->params;
        
        $this->item       = JArrayHelper::getValue($this->items, 0);
        
        if(!empty($this->item)) {
        
            // Extra Images
            $extraImages = array();
            if($this->params->get("lineal_extra_image") AND isset($this->item)){
                
                $extraImages[$this->item->id] = VipPortfolioHelper::getExtraImages($this->item->id);
                
                $this->extraImages = $extraImages;
                $this->extraMax = $this->params->get("lineal_extra_max");
            
            }
        
            // Prepare Joomla! plugin onContentPrepare
            if($this->params->get("joomla_plugins")) {
                $offset = $this->state->get("list.start", null);
                 
                $this->item->text = $this->item->description;
                $this->item->link = "index.php?option=com_vipportfolio&view=projects&layout=lineal";
                $this->item->image_intro = $this->params->get("images_directory", "images/vipportfolio")."/".$this->item->thumb;
                
                $dispatcher	= JDispatcher::getInstance();
                JPluginHelper::importPlugin('content');
    		    $results = $dispatcher->trigger('onContentPrepare', array ('com_vipportfolio.lineal', &$this->item, &$this->params, $offset));
    		    
    		    // Remove redundant parameters
    	        $this->item->description = $this->item->text;
    	        unset($this->item->text);
    	        
                $this->event = new stdClass();
        		$results = $dispatcher->trigger('onContentAfterTitle', array('com_vipportfolio.project', &$this->item, &$this->params, $offset));
        		$this->event->afterDisplayTitle = trim(implode("\n", $results));
        
        		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_vipportfolio.project', &$this->item, &$this->params, $offset));
        		$this->event->beforeDisplayContent = trim(implode("\n", $results));
        
        		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_vipportfolio.project', &$this->item, &$this->params, $offset));
        		$this->event->afterDisplayContent = trim(implode("\n", $results));
            }
            
            // Open link target
            $this->openLink = 'target="'.$this->params->get("lineal_open_link", "_self").'"';
            
            $this->prepareLightBox();
        }
        
        $this->prepareDocument();
        
        parent::display($tpl);
    }
    
    protected function prepareLightBox() {
        
        $modal = $this->params->get("lModal");
        
        switch($modal) {
            
            case "slimbox":
                
                JHTML::_('behavior.framework');
                
                $this->document->addStyleSheet('media/'.$this->option.'/js/slimbox/css/slimbox.css');
                $this->document->addScript('media/'.$this->option.'/js/slimbox/slimbox.js');
                    
                break;
            
            case "native": // Joomla! native
                
                JHTML::_('behavior.framework');
                
                // Adds a JavaScript needs for modal windows
                JHTML::_('behavior.modal', 'a.vip-modal');
                
                break;
        }
        
        $this->modal = $modal;
    }
    
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){

        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $title      = "";
        
        $menus      = $app->getMenu();
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu       = $menus->getActive();
        
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Set page heading 
        if(!$this->params->get("page_heading")){
            if(!empty($this->category) AND !empty($this->category->name)) {
                $this->params->def('page_heading', $this->category->name);
            } else {
                if($menu) {
                    $this->params->def('page_heading', $menu->title);
                } else {
                    $this->params->def('page_heading', JText::_('COM_VIPPORTFOLIO_DEFAULT_PAGE_TITLE'));
                }
            }
        }
          
        // Set page title 
        if(!$this->category) { // Uncategorised
            // Get title from the page title option
            $title = $this->params->get("page_title");

            if(!$title) {
                $title = $app->getCfg('sitename');
            }
            
        } else{
            
            $title = $this->category->meta_title;
            
            if(!$title){
                // Get title from the page title option
                $title = $this->params->get("page_title");
    
                if(!$title) {
                    $title = $app->getCfg('sitename');
                }
                
            }elseif($app->getCfg('sitename_pagetitles', 0)){ // Set site name if it is necessary ( the option 'sitename' = 1 )
                $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
                
            }
            
        }
        
        $this->document->setTitle($title);
        
        // Meta Description
        if(!$this->category) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($this->category->meta_desc);
        }
        
        // Meta keywords 
        if(!$this->category) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $this->category->meta_keywords);
        }
        
        // Canonical URL 
        if(!empty($this->category) AND !empty($this->category->meta_canonical)) {
           $cLink = '<link href="' . $this->category->meta_canonical . '" rel="canonical"  />';
           $this->document->addCustomTag($cLink);
        }
        
        // Add the category name into breadcrumbs
        if($this->params->get('cat_breadcrumbs')){
            if(!empty($this->category->name)){
                $pathway    = $app->getPathway();
                $pathway->addItem($this->category->name);
            }
        }
        
        // JavaScript and Styles
        
        $view = JString::strtolower($this->getName());
        
        // Add template style
        $this->document->addStyleSheet('media/'. $this->option. '/projects/' . $view . '/style.css');
        
        // If tmpl is set that mean the user loads the page from Facebook
        // So we should Auto Grow the tab.
        $tmpl = $app->input->get("tmpl");
        if(!empty($tmpl)) {
            if($this->params->get("fbpp_auto_grow") ){
                VipPortfolioHelper::facebookAutoGrow($this->document, $this->params);
            }            
        }
        
    }
    

}