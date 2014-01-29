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

class VipPortfolioGalleria implements VipPortfolioInterfacePortfolio {
    
    protected static $loaded = false;
    
    protected $items;
    protected $imagesPath;
    protected $selector;
    
    protected $options;
    
    public function __construct($items, JRegistry $options) {
        $this->items = $items;
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
        
        $document->addStyleSheet('media/com_vipportfolio/js/galleria/themes/classic/galleria.classic.css');
        
        $document->addScript('media/com_vipportfolio/js/galleria/galleria.min.js');
        $document->addScript('media/com_vipportfolio/js/galleria/themes/classic/galleria.classic.min.js');
    }
    
    public function addScriptDeclaration(JDocument $document) {
        
        $js = '
        jQuery(document).ready(function() {
            Galleria.run("#'.$this->selector.'");
        });';
        
        $document->addScriptDeclaration($js);
        
        return $this;
    }
    
    /**
     * Generate HTML code displaying thumbnails and images.
     * 
     * <code>
     * 
     * $portfolio = new VipPortfolioGalleria($items, $options);
     * $portfolio->setSelector("vp-com-galleria");
     * $portfolio->render();
     * 
     * </code>
     * 
     * @return string
     * 
     */
    public function render() {
        
        $html = array();
        
        if(!empty($this->items)) {
            
            $html[] = '<div id="'.$this->selector.'">';
            
        	foreach($this->items as $item) {
        	    
        	    if(!$item->image OR !$item->thumb) {
        	        continue;
        	    }
        	    
        	    $html[] = '<a href="'.$this->imagesPath.$item->image.'"><img src="'.$this->imagesPath.$item->thumb.'" /></a>';
            }
            
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }
    
    /**
     * Generate HTML code displaying only images.
     * 
     * <code>
     * 
     * $portfolio = new VipPortfolioGalleria($items, $options);
     * $portfolio->setSelector("vp-com-galleria");
     * 
     * $portfolio->renderOnlyImages();
     * 
     * </code>
     * 
     * @return string
     * 
     */
    public function renderOnlyImages() {
        
        $html = array();
        
        if(!empty($this->items)) {
            
            $html[] = '<div id="'.$this->selector.'">';
            
        	foreach($this->items as $item) {
        	    
        	    if(!$item->image) {
        	        continue;
        	    }
        	    
        	    $html[] = '<img src="'.$this->imagesPath.$item->image.'" />';
            }
            
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }
}

