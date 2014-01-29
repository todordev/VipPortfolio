<?php
/**
 * @package		 VipPortfolio
 * @subpackage	 Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("VipPortfolioInterfacePortfolio", JPATH_LIBRARIES .DIRECTORY_SEPARATOR. "vipportfolio" .DIRECTORY_SEPARATOR. "interface" .DIRECTORY_SEPARATOR. "portfolio.php");

class VipPortfolioCamera implements VipPortfolioInterfacePortfolio {
    
    protected static $loaded = false;
    
    protected $items;
    protected $imagesPath;
    protected $selector;
    
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
    
    protected $options;
    
    public function __construct($items, JRegistry $options) {
        $this->items     = $items;
        $this->options   = $options;
    }
    
    public function setSelector($selector) {
        $this->selector = $selector;
        return $this;
    }
    
    public function setImagesPath($imagesPath) {
        $this->imagesPath = $imagesPath;
        return $this;
    }
    
    public static function load() {
        
        if(self::$loaded) {
            return;
        }
        
        self::$loaded = true;
        
        $document = JFactory::getDocument();
        
        $document->addStyleSheet('media/com_vipportfolio/js/camera/css/camera.css');
        $document->addScript('media/com_vipportfolio/js/camera/camera.js');
        $document->addScript('media/com_vipportfolio/js/camera/jquery.easing.1.3.js');
    }
    
    public function addScriptDeclaration(JDocument $document) {
        
        $js = '
jQuery(document).ready(function() {
        
	jQuery("#'.$this->selector.'").camera({
        alignment : "'.$this->options->get("camera_alignment", "center").'",
        autoAdvance : '.$this->options->get("camera_auto_advance", 1).',
        barDirection : "'.$this->options->get("camera_bar_direction", "leftToRight").'",
        barPosition : "'.$this->options->get("camera_bar_position", "bottom").'",
        fx : "'.$this->options->get("camera_fx", "random").'",
        navigation : '.$this->options->get("camera_navigation", 1).',
        navigationHover : '.$this->options->get("camera_navigation_hover", 1).',
        pagination : '.$this->options->get("camera_pagination", 1).',
        playPause : '.$this->options->get("camera_play_pause", 1).',
        pauseOnClick : '.$this->options->get("camera_pause_click", 1).',
        time : '.$this->options->get("camera_time", 7000).',
        transPeriod : '.$this->options->get("camera_trans_period", 1500).',
        thumbnails : '.$this->options->get("camera_thumbnails", 0).'
    });
        
});';
        
        $document->addScriptDeclaration($js);
        
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
        	    if($this->options->get("camera_linkable", 0) AND !empty($item->url)) {
        	        
    	        	$dataLink = ' data-link="'.$item->url.'"';
    	        	
    	        	// Set a link target
	        	    $dataTarget = ' data-target="'.$this->options->get("camera_link_target", "_blank").'"';
    	        	
        	    }
        	    
        	    // Set thumnails
        	    $dataThumb = "";
        	    if($this->options->get("camera_thumbnails", 0) AND !empty($item->thumb)) {
        	        $dataThumb = ' data-thumb="'.$this->imagesPath.$item->thumb.'"';
        	    }
        	    
        	    $html[] = '<div data-src="'.$this->imagesPath.$item->image.'" '.$dataLink. $dataTarget. $dataThumb.'></div>';
            }
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }
}

