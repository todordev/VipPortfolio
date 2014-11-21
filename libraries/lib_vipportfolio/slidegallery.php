<?php
/**
 * @package         VipPortfolio
 * @subpackage      SlideGallery
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("VipPortfolioInterfacePortfolio", JPATH_LIBRARIES . "/vipportfolio/interface/portfolio.php");

/**
 * This class provide functionality for managing Slide Gallery data.
 *
 * @package         VipPortfolio
 * @subpackage      SlideGallery
 */
class VipPortfolioSlideGallery implements VipPortfolioInterfacePortfolio
{
    protected static $loaded = false;

    protected $items;
    protected $imagesPath;
    protected $selector;

    /**
     * The gallery options.
     *
     * @var Joomla\Registry\Registry
     */
    protected $options;

    /**
     * Initialize the object.
     *
     * <code>
     * $portfolio = new VipPortfolioSlideGallery($items, $params);
     * </code>
     *
     * @param array  $items
     * @param Joomla\Registry\Registry $options
     */
    public function __construct($items, $options)
    {
        $this->items   = $items;
        $this->options = ($options instanceof JRegistry) ? $options : new JRegistry;
    }

    /**
     * Set the element selector.
     *
     * <code>
     * $portfolio = new VipPortfolioSlideGallery($items, $params);
     * $portfolio->setSelector("#js-selector");
     * </code>
     *
     * @param string  $selector
     *
     * @return self
     */
    public function setSelector($selector)
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * Set a path to the pictures.
     *
     * <code>
     * $imagesPath = "../.../..../";
     *
     * $portfolio = new VipPortfolioSlideGallery($items, $params);
     * $portfolio->setImagesPath($imagesPath);
     * </code>
     *
     * @param string  $imagesPath
     *
     * @return self
     */
    public function setImagesPath($imagesPath)
    {
        $this->imagesPath = $imagesPath;

        return $this;
    }

    /**
     * Include the files (CSS, JS) of the library to the document.
     *
     * <code>
     * VipPortfolioSlideGallery::load();
     * </code>
     */
    public static function load()
    {
        if (self::$loaded) {
            return;
        }

        self::$loaded = true;

        $document = JFactory::getDocument();

        // Add template style
        $document->addStyleSheet('media/com_vipportfolio/js/slidesjs/font-awesome.min.css');
        $document->addStyleSheet('media/com_vipportfolio/js/slidesjs/jquery.slides.css');

        $document->addScript('media/com_vipportfolio/js/slidesjs/jquery.slides.min.js');
    }

    /**
     * Add script code to the document.
     *
     * <code>
     * $document = JFactory::getDocument();
     *
     * $portfolio = new VipPortfolioSlideGallery($items, $params);
     * $portfolio->addScriptDeclaration($document);
     * </code>
     *
     * @param JDocument  $document
     *
     * @return self
     */
    public function addScriptDeclaration(JDocument $document)
    {
        $effects = $this->prepareEffects();
        $play    = $this->preparePlay();

        $js = '
jQuery(document).ready(function() {
	jQuery("#' . $this->selector . '").slidesjs({
        start: ' . $this->options->get("slidegallery_start", 1) . ',
        width: ' . $this->options->get("slidegallery_width", 600) . ',
        height: ' . $this->options->get("slidegallery_height", 400) . ',' .
            $effects . $play . '
    });
});';
        $document->addScriptDeclaration($js);

        return $this;
    }

    /**
     * Generate HTML code displaying thumbnails and images.
     *
     * <code>
     *
     * $portfolio = new VipPortfolioSlideGallery($items, $options);
     * $portfolio->setSelector("vp-com-galleria");
     * $portfolio->render();
     *
     * </code>
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        if (!empty($this->items)) {

            $html[] = '<div id="' . $this->selector . '">';

            foreach ($this->items as $item) {

                if (!$item->image) {
                    continue;
                }

                $html[] = '<img src="' . $this->imagesPath . $item->image . '" />';
            }

            $html[] = '<a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>';
            $html[] = '<a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>';

            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    private function prepareEffects()
    {
        $options = "";
        $effect  = $this->options->get("slidegallery_effect", "fade");
        $speed   = $this->options->get("slidegallery_speed", 200);

        $navigation = $this->options->get("slidegallery_navigation", 0);
        $pagination = $this->options->get("slidegallery_pagination", 1);

        if (strcmp("slide", $effect) == 0) {

            $options = '
            	navigation: {
            		active: ' . $navigation . ',
        			effect: "slide"
    			},
    			pagination: {
            		active: ' . $pagination . ',
        			effect: "slide"
    			},
            	effect: {
                  slide: {
                    speed: ' . (int)$speed . '
                  }
                }
            ';

        } elseif (strcmp("fade", $effect) == 0) {

            $options = '
            	navigation: {
            		active: ' . $navigation . ',
        			effect: "fade"
    			},
    			pagination: {
            		active: ' . $pagination . ',
        			effect: "fade"
    			},
            	effect: {
                  fade: {
                    speed: ' . (int)$speed . ',
                    crossfade: false
                  }
                }
            ';

        } elseif (strcmp("fade-crossfade", $effect) == 0) {
            $options = '
            	navigation: {
            		active: ' . $navigation . ',
        			effect: "fade"
    			},
    			pagination: {
            		active: ' . $pagination . ',
        			effect: "fade"
    			},
            	effect: {
                  fade: {
                    speed: ' . (int)$speed . ',
                    crossfade: true
                  }
                }
            ';
        }

        return $options;
    }

    private function preparePlay()
    {
        $options  = "";
        $play     = $this->options->get("slidegallery_play", 0);
        $effect   = $this->options->get("slidegallery_effect", "fade");
        $interval = $this->options->get("slidegallery_interval", 5000);
        $autoplay = $this->options->get("slidegallery_autoplay", 0);
        $swap     = $this->options->get("slidegallery_swap", 1);
        $pause    = $this->options->get("slidegallery_pause", 0);
        $restart  = $this->options->get("slidegallery_restart", 2500);

        if (!empty($play)) {

            $options = ',
            	play: {
                  active: true,
                  effect: "' . $effect . '",
                  interval: ' . $interval . ',
                  auto: ' . $autoplay . ',
                  swap: ' . $swap . ',
                  pauseOnHover: ' . $pause . ',
                  restartDelay: ' . $restart . '
                    // [number] restart delay on inactive slideshow
                }
            ';

        }

        return $options;
    }
}
