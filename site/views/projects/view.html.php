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
    
    /**
     * Display the view
     *
     * @return  mixed   False on error, null otherwise.
     */
    function display($tpl = null){
        
        // Check for valid category
        $categoryId = JRequest::getInt("catid", 0, "GET");
        
        if(!empty($categoryId)){
            // Checking for published category
            $published = VpHelper::isCategoryPublished($categoryId);
            if(!$published){
                throw new ItpException(JText::_("ITP_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
            }
        }
        
        // Initialise variables
        $state      = $this->get('State');
        $items      = $this->get('Items');
        $pagination = $this->get('Pagination');
    
        $params     = &$state->params;
        
        if($params->get("catDesc")) {
            $category = VpHelper::getCategory($categoryId);
            $this->assignRef('category', $category);
        }
        
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
        
        $this->assignRef('params', $params);
        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        
        $layout = $this->getLayout();
        
        switch($layout){
            
            case "lineal":
                
                // Add template style
                $this->document->addStyleSheet(JURI::base() . 'media/com_vipportfolio/projects/' . $layout . '/style.css', 'text/css', null);
                
                $this->assignRef('item', JArrayHelper::getValue($this->items, 0));
                $this->linealLayOut();
                break;
            
            default:
                
                $layout = "default";
                // Add template style
                $this->document->addStyleSheet(JURI::root() . 'media/com_vipportfolio/projects/' . $layout . '/style.css', 'text/css');
                $this->defaultLayout();
                
                break;
        }
        
        $this->assignRef("version", new VpVersion());
        
        $this->prepareLightBox();
        $this->_prepareDocument();
        
        parent::display($tpl);
    }
    
    protected function prepareLightBox() {
        
        $modalParams = "";
        $layout = $this->getLayout();
        $isModal = false;
        
        switch($layout) {
            case "lineal":
                $isModal = (bool)$this->params->get("lModal");
                break;
            default:
                $isModal = (bool)$this->params->get("modal");
                break;
        }
        
        switch($this->params->get("modalLib")) {
            
            case "slimbox":
                if ($isModal) {
                    JHTML::_('behavior.framework');
                    
                    $this->document->addStyleSheet(JURI::root() . 'media/com_vipportfolio/slimbox/css/slimbox.css');
                    $this->document->addScript(JURI::root() . 'media/com_vipportfolio/slimbox/slimbox.js');
                    
                    $modalParams = 'rel="lightbox%s"';
                }
                break;
            
            default: // Joomla! native
                if ($isModal) {
                    // Adds a JavaScript needs for modal windows
                    JHTML::_('behavior.modal', 'a.vip-modal');
                    $modalParams = 'class="vip-modal" rel="{handler: \'image\'}"';
                }
                break;
        }
        
        $this->assign("modalParams", $modalParams);
    }
    
    protected function defaultLayout(){
        
        if($this->params->get("extra_image")){
            foreach($this->items as $item){
                // Extra Images
                $extraImages[$item->id] = VpHelper::getExtraImages($item->id);
            }
            $this->assignRef('extraImages', $extraImages);
            $this->assign('extraMax', $this->params->get("extra_max"));
        }
    
    }
    
    protected function linealLayOut(){
        
        if($this->params->get("lExtraImage") AND isset($this->item)){
            // Extra Images
            $extraImages[$this->item->id] = VpHelper::getExtraImages($this->item->id);
            
            $this->assignRef('extraImages', $extraImages);
            $this->assign('extraMax', $this->params->get("lExtraMax"));
        
        }
    
    }
    
    /**
     * Prepares the document
     */
    protected function _prepareDocument(){
        $app = JFactory::getApplication();
        
        $title = null;
        
        $categoryId = JRequest::getInt("catid");
        $category = VpHelper::getCategory($categoryId);
        
        $menus = $app->getMenu();
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        
        // Get page heading
        if(!$this->params->get("page_heading")){
            if(!empty($category) AND !empty($category->name)) {
                $this->params->def('page_heading', $category->name);
            } else {
                if($menu) {
                    $this->params->def('page_heading', $menu->title);
                } else {
                    $this->params->def('page_heading', JText::_('COM_VIPORTFOLIO_DEFAULT_PAGE_TITLE'));
                }
            }
        }
          
        // Set a page title
        $title = "";
        if(!$category) { // Uncategorised
            // Get title from the page title option
            $title = $this->params->get("page_title");

            if(!$title) {
                $title = $app->getCfg('sitename');
            }
        } else{
            
            $title = $category->meta_title;
            if(!$title){
                // Get title from the page title option
                $title = $this->params->get("page_title");
    
                if(!$title) {
                    $title = $app->getCfg('sitename');
                }
            }elseif($app->getCfg('sitename_pagetitles', 0)){
                $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
            }
        }
        $this->document->setTitle($title);
        
        // Meta Description
        if(!$category) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($category->meta_desc);
        }
        // Meta keywords
        if(!$category) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $category->meta_keywords);
        }
        
        // Canonical URL
        if(!empty($category) AND !empty($category->meta_canonical)) {
           $cLink = '<link href="' . $category->meta_canonical . '" rel="canonical"  />';
           $this->document->addCustomTag($cLink);
        }
        
        // Adds the category name into breadcrumbs
        if($this->params->get('cat_breadcrumbs')){
            
            if(!empty($categoryId) AND !empty($category->name)){
                $pathway    = $app->getPathway();
                $pathway->addItem($category->name);
            }
        }
    }

}