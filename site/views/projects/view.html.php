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
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class VipPortfolioViewProjects extends JView {
    
    protected $state = null;
    protected $items = null;
    protected $pagination = null;
    
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
                throw new Exception(JText::_("ITP_ERROR_CATEGORY_DOES_NOT_EXIST"));
            }
        }
        
        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params     = $this->state->params;
        
        $layout = $this->getLayout();
        
        switch($layout){
            
            case "lineal":
                $this->item = JArrayHelper::getValue($this->items, 0);
                $this->linealLayout($layout);
                break;
            
            case "scrollgallery":
                $this->scrollgalleryLayout($layout);
                break;
                
            default:
                $layout = "default";
                $this->defaultLayout($layout);
                break;
        }
        
        $this->version = new VipPortfolioVersion();
        
        $this->prepareLightBox();
        $this->prepareDocument();
        
        parent::display($tpl);
    }
    
    protected function prepareLightBox() {
        
        $layout      = $this->getLayout();
        $modal       = "";
        
        switch($layout) {
            case "lineal":
                $modal = $this->params->get("lModal");
                break;
            default:
                $modal = $this->params->get("modal");
                break;
        }
        
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
    
    protected function defaultLayout($layout){
        
        // Add template style
        $this->document->addStyleSheet('media/'.$this->option.'/projects/' . $layout . '/style.css', 'text/css');
                
        if($this->params->get("extra_image")){
            foreach($this->items as $item){
                // Extra Images
                $extraImages[$item->id] = VipPortfolioHelper::getExtraImages($item->id);
            }
            
            $this->extraImages = $extraImages;
            $this->extraMax    = $this->params->get("extra_max");
        }
        
        // Open link target
        $this->openLink = 'target="'.$this->params->get("list_open_link", "_self").'"';
    
    }
    
    protected function linealLayout($layout){
        
        // Add template style
        $this->document->addStyleSheet('media/'. $this->option. '/projects/' . $layout . '/style.css', 'text/css', null);
                
        if($this->params->get("lineal_extra_image") AND isset($this->item)){
            // Extra Images
            $extraImages[$this->item->id] = VipPortfolioHelper::getExtraImages($this->item->id);
            
            $this->extraImages = $extraImages;
            $this->extraMax = $this->params->get("lineal_extra_max");
        
        }
    
        // Open link target
        $this->openLink = 'target="'.$this->params->get("lineal_open_link", "_self").'"';
    }
    
    protected function scrollgalleryLayout($layout){
        
        JHTML::_('behavior.framework');
        
        // Add template style
        $this->document->addStyleSheet('media/'. $this->option. '/projects/' . $layout . '/style.css', 'text/css', null);

		// Add scripts
		$this->document->addScript('media/'.$this->option.'/js/scrollgallery/scrollGallery.js');
		$this->document->addScript('media/'.$this->option.'/js/site/scrollgallery.js');
    
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
                    $this->params->def('page_heading', JText::_('COM_VIPORTFOLIO_DEFAULT_PAGE_TITLE'));
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
    }

}