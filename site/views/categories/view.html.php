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

class VipPortfolioViewCategories extends JView {
    
    protected $state = null;
    protected $items = null;
    protected $pagination = null;
    
    public function display($tpl = null) {
        
        $option     = JRequest::getCmd("option", "com_vipportfolio", "GET");
        
        // Initialise variables
        $state      = $this->get('State');
        $items      = $this->get('Items');
        $pagination = $this->get('Pagination');
    
        $params     = &$state->params;
        
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
        
        $this->assignRef('params', $params);
        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        
        $this->assign('projectLayout', JRequest::getCmd("project_layout", "default"));
        
        $layout = $this->getLayout();
        
    	switch( $layout ) {
            case "tabbed":
                $this->tabbedLayout($layout, $option);
                break;

            case "imagemenu":
                $this->imagemenuLayout($layout, $option);
                break;
                
    		default:
    			$layout =   "default";
    			// Add template style
                $this->document->addStyleSheet( JURI::base() . 'media/'.$option.'/categories/' . $layout . '/style.css', 'text/css', null );
    			break;
    	}
        
        $this->assignRef( "version",    new VpVersion() );
        
        $this->prepareLightBox($option);
        $this->prepareDocument();
                
        parent::display($tpl);
    }
    
    protected function tabbedLayout($layout, $option) {
        
        // Add template style
        $this->document->addStyleSheet( JURI::base() . 'media/'.$option.'/categories/' . $layout . '/style.css', 'text/css', null );
                
        // Only loads projects from the published categories
        foreach ($this->items as $item){
            $categories[] = $item->id;
        }
        
        $published = 1;
        $projects_ = VpHelper::getProjects($categories, $published);
        
        $projects  = array();
        foreach ($projects_ as &$item){
            $projects[$item['catid']][] = $item;  
        }
        
        $this->assignRef('projects',    $projects);
        
    }
    
    protected function imagemenuLayout($layout, $option) {
        
        // Add template style
        $this->document->addStyleSheet( JURI::base() . 'media/'.$option.'/categories/' . $layout . '/style.css', 'text/css', null );
        $this->document->addScript(JURI::root() . 'media/'.$option.'/js/imagemenu/ImageMenu.js');
        
        $cssStyles = "";
        foreach($this->items as $item) {
            $cssStyles .= "
            #itp-vp-image-menu ul li.item" .$item->id."  a {
                background: url('".JURI::root() . "media/vipportfolio/" . $item->image."') repeat scroll 0%;
            }
            ";
        }
        if(!empty($cssStyles)) {
            $this->document->addStyleDeclaration($cssStyles);
        }
        
        $js = "window.addEvent('domready', function(){
    	  var myMenu = new ImageMenu(
    	    $$('#itp-vp-image-menu a'),{
        	    openWidth:" . $this->params->get("cimgmenuOpenWidth", 310) .", 
        	    border:" . $this->params->get("cimgmenuBorder", 2).", 
        	    duration:" .  $this->params->get("cimgmenuDuration", 400).", 
    	    	OnClickOpen:function(e,i){ 
        	     window.location = e;
        	    }
    	    });
        });";
        
        $this->document->addScriptDeclaration($js);
                
    }
    
    protected function prepareLightBox($option) {
        
        $modalParams    = "";
        $layout         = $this->getLayout();
        $hasModal       = false;
        
        switch($layout) {
            case "tabbed":
                $hasModal = (bool)$this->params->get("ctabModal");
                break;
            default:
                break;
        }
        
        if ($hasModal) {
            switch($this->params->get("modalLib")) {
                
                case "slimbox":
                    
                    JHTML::_('behavior.framework');
                    
                    $this->document->addStyleSheet(JURI::root() . 'media/'.$option.'/js/slimbox/css/slimbox.css');
                    $this->document->addScript(JURI::root() . 'media/'.$option.'/js/slimbox/slimbox.js');
                    
                    break;
                
                default: // Joomla! native
                    // Adds a JavaScript needs for modal windows
                    JHTML::_('behavior.modal', 'a.vip-modal');
                    break;
            }
        }
        
        $this->assign("hasModal", $hasModal);
        $this->assign("modalLib", $this->params->get("modalLib"));
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if($menu){
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }else{
            $this->params->def('page_heading', JText::_('COM_VIPPORTFOLIO_CATEGORIES_DEFAULT_PAGE_TITLE'));
        }
        
        /*** Set page title ***/
        $title = $this->params->get('page_title', '');
        if(empty($title)){
            $title = $app->getCfg('sitename');
        }elseif($app->getCfg('sitename_pagetitles', 0)){
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        $this->document->setTitle($title);
        
        /*** Meta Description ***/
        if($this->params->get('menu-meta_description')){
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }
        
        /*** Meta keywords ***/
        if($this->params->get('menu-meta_keywords')){
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }
        
    }
    
}