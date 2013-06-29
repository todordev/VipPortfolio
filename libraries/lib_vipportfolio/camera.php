<?php
/**
 * @package		 Vip Portfolio
 * @subpackage	 Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Portfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('JPATH_PLATFORM') or die;

jimport("vipportfolio.interface.portfolio");

class VipPortfolioCamera implements VipPortfolioInterfacePortfolio {
    
    protected $items;
    protected $document;
    protected $selector;
    protected $imagePath;
    
    protected $linkable   = 0;
    protected $linkTarget = "_blank";
    
    protected $alignment        = "center"; 
    protected $autoAdvance      = 1; 
    protected $barDirection     = "leftToRight"; 
    protected $barPosition      = "bottom"; 
    protected $fx               = "random"; 
    protected $navigation       = 1; 
    protected $navigationHover  = 1; 
    protected $pagination       = 1; 
    protected $playPause        = 1; 
    protected $pauseOnClick     = 1; 
    protected $time             = 7000; 
    protected $transPeriod      = 1500; 
    protected $thumbnails       = 0; 
    
    public function __construct($items, $document, $params) {
        $this->items     = $items;
        $this->document  = $document;
        
        $this->bind($params);
    }
    
    public function setSelector($selector) {
        $this->selector = $selector;
        return $this;
    }
    
    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
        return $this;
    }
    
    public function bind($params) {
        
        $this->linkable      = $params->get("camera_linkable", 0);
        $this->linkTarget    = $params->get("camera_link_target", "_blank");
        
        $this->alignment     = $params->get("camera_alignment", "center");
        $this->autoAdvance   = $params->get("camera_auto_advance", 1);
        $this->barDirection  = $params->get("camera_bar_direction", "leftToRight");
        $this->barPosition   = $params->get("camera_bar_position", "bottom");
        $this->fx            = $params->get("camera_fx", "random");
        $this->navigation    = $params->get("camera_navigation", 1);
        $this->navigationHover    = $params->get("camera_navigation_hover", 1);
        $this->pagination    = $params->get("camera_pagination", 1);
        $this->playPause     = $params->get("camera_play_pause", 1);
        $this->pauseOnClick  = $params->get("camera_pause_click", 1);
        $this->time          = $params->get("camera_time", 7000);
        $this->transPeriod   = $params->get("camera_trans_period", 1500);
        $this->thumbnails    = $params->get("camera_thumbnails", 0);
    }
    
    public function addStyleSheets() {
        $this->document->addStyleSheet('media/com_vipportfolio/projects/camera/style.css');
        return $this;
    }
    
    public function addScripts() {
        $this->document->addScript('media/com_vipportfolio/js/camera/camera.js');
        $this->document->addScript('media/com_vipportfolio/js/camera/jquery.easing.1.3.js');
        return $this;
    }
    
    public function addScriptDeclaration() {
        
        $js = '
jQuery(document).ready(function() {
        
	jQuery("#'.$this->selector.'").camera({
        alignment : "'.$this->alignment.'",
        autoAdvance : '.$this->autoAdvance.',
        barDirection : "'.$this->barDirection.'",
        barPosition : "'.$this->barPosition.'",
        fx : "'.$this->fx.'",
        navigation : '.$this->navigation.',
        navigationHover : '.$this->navigationHover.',
        pagination : '.$this->pagination.',
        playPause : '.$this->playPause.',
        pauseOnClick : '.$this->pauseOnClick.',
        time : '.$this->time.',
        transPeriod : '.$this->transPeriod.',
        thumbnails : '.$this->thumbnails.'
    });
        
});';
        
        $this->document->addScriptDeclaration($js);
        
        return $this;
    }
    
    public function render() {
        
        $html = array();
        
        if(!empty($this->items)) {
            
            $html[] = '<div id="'.$this->selector.'">';
        	foreach ( $this->items as $item ) {
        	    
        	    if(!$item->image) {
        	        continue;
        	    }
        	    
        	    // Set a link
        	    $dataLink   = "";
        	    $dataTarget = "";
        	    if(!empty($this->linkable) AND !empty($item->url)) {
        	        
    	        	$dataLink = ' data-link="'.$item->url.'"';
    	        	
    	        	// Set a link target
	        	    $dataTarget = ' data-target="'.$this->linkTarget.'"';
    	        	
        	    }
        	    
        	    // Set thumnails
        	    $dataThumb = "";
        	    if(!empty($this->thumbnails) AND !empty($item->thumb)) {
        	        $dataThumb = ' data-thumb="'.$this->imagePath.$item->thumb.'"';
        	    }
        	    
        	    $html[] = '<div data-src="'.$this->imagePath.$item->image.'" '.$dataLink. $dataTarget. $dataThumb.'></div>';
            }
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }
}

