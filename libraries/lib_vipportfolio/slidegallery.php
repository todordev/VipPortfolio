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

class VipPortfolioSlideGallery implements VipPortfolioInterfacePortfolio {
    
    protected static $loaded = false;
    
    protected $items;
    protected $imagesPath;
    protected $selector;
    
    protected $options;
    
    public function __construct($items, JRegistry $options) {
        $this->items   = $items;
        $this->options = $options;
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
        
        // Add template style
        $document->addStyleSheet('media/com_vipportfolio/js/slidesjs/font-awesome.min.css');
        $document->addStyleSheet('media/com_vipportfolio/js/slidesjs/jquery.slides.css');
        
        $document->addScript('media/com_vipportfolio/js/slidesjs/jquery.slides.min.js');
    }
    
    public function addScriptDeclaration(JDocument $document) {
        
        $effects = $this->prepareEffects();
        $play    = $this->preparePlay();
        
        $js = '
jQuery(document).ready(function() {
	jQuery("#'.$this->selector.'").slidesjs({
        start: '.$this->options->get("slidegallery_start", 1).',
        width: '.$this->options->get("slidegallery_width", 600).',
        height: '.$this->options->get("slidegallery_height", 400).','.
        $effects . $play.'
    });
});';
        
        $document->addScriptDeclaration($js);
        
        return $this;
    }
    
    
    public function render() {
        
        $html = array();
        
        if(!empty($this->items)) {
            
            $html[] = '<div id="'.$this->selector.'">';
            
        	foreach($this->items as $item) {
        	    
        	    if(!$item->image) {
        	        continue;
        	    }
        	    
        	    $html[] = '<img src="' .$this->imagesPath.$item->image. '" />';
            }
            
            $html[] = '<a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>';
            $html[] = '<a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>';
            
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }
    
    private function prepareEffects() {
    
        $options = "";
        $effect = $this->options->get("slidegallery_effect", "fade");
        $speed  = $this->options->get("slidegallery_speed", 200);
    
        $navigation  = $this->options->get("slidegallery_navigation", 0);
        $pagination  = $this->options->get("slidegallery_pagination", 1);
    
        if(strcmp("slide", $effect) == 0) {
    
            $options = '
            	navigation: {
            		active: '.$navigation.',
        			effect: "slide"
    			},
    			pagination: {
            		active: '.$pagination.',
        			effect: "slide"
    			},
            	effect: {
                  slide: {
                    speed: '.(int)$speed.'
                  }
                }
            ';
    
        } else if(strcmp("fade", $effect) == 0) {
    
            $options = '
            	navigation: {
            		active: '.$navigation.',
        			effect: "fade"
    			},
    			pagination: {
            		active: '.$pagination.',
        			effect: "fade"
    			},
            	effect: {
                  fade: {
                    speed: '.(int)$speed.',
                    crossfade: false
                  }
                }
            ';
    
        } else if(strcmp("fade-crossfade", $effect) == 0) {
            $options = '
            	navigation: {
            		active: '.$navigation.',
        			effect: "fade"
    			},
    			pagination: {
            		active: '.$pagination.',
        			effect: "fade"
    			},
            	effect: {
                  fade: {
                    speed: '.(int)$speed.',
                    crossfade: true
                  }
                }
            ';
        }
    
        return $options;
    }
    
    private function preparePlay() {
    
        $options     = "";
        $play        = $this->options->get("slidegallery_play", 0);
        $effect      = $this->options->get("slidegallery_effect", "fade");
        $interval    = $this->options->get("slidegallery_interval", 5000);
        $autoplay    = $this->options->get("slidegallery_autoplay", 0);
        $swap        = $this->options->get("slidegallery_swap", 1);
        $pause       = $this->options->get("slidegallery_pause", 0);
        $restart     = $this->options->get("slidegallery_restart", 2500);
    
        if(!empty($play)) {
    
            $options = ',
            	play: {
                  active: true,
                  effect: "'.$effect.'",
                  interval: '.$interval.',
                  auto: '.$autoplay.',
                  swap: '.$swap.',
                  pauseOnHover: '.$pause.',
                  restartDelay: '.$restart.'
                    // [number] restart delay on inactive slideshow
                }
            ';
    
        }
    
        return $options;
    }
    
}

