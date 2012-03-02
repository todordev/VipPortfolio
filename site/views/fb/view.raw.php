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

class VipPortfolioViewFb extends JView {
    
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
            
            default:
                
                $layout = "default";
                // Add template style
                $this->document->addStyleSheet(JURI::root() . 'media/com_vipportfolio/projects/' . $layout . '/style.css', 'text/css');
                $this->defaultLayout();
                
                break;
        }
        
        $this->assignRef("version", new VpVersion());
        
        parent::display($tpl);
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
    
}