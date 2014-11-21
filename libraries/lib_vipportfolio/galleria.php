<?php
/**
 * @package         VipPortfolio
 * @subpackage      Galleria
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("VipPortfolioInterfacePortfolio", JPATH_LIBRARIES . "/vipportfolio/interface/portfolio.php");

/**
 * This class provide functionality for managing Gallery.
 *
 * @package         VipPortfolio
 * @subpackage      Galleria
 */
class VipPortfolioGalleria implements VipPortfolioInterfacePortfolio
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
     * $portfolio = new VipPortfolioGalleria($items, $params);
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
     * $portfolio = new VipPortfolioGalleria($items, $params);
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
     * $portfolio = new VipPortfolioGalleria($items, $params);
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
     * VipPortfolioGalleria::load();
     * </code>
     */
    public static function load()
    {
        if (self::$loaded) {
            return;
        }

        self::$loaded = true;

        $document = JFactory::getDocument();

        $document->addStyleSheet('media/com_vipportfolio/js/galleria/themes/classic/galleria.classic.css');

        $document->addScript('media/com_vipportfolio/js/galleria/galleria.min.js');
        $document->addScript('media/com_vipportfolio/js/galleria/themes/classic/galleria.classic.min.js');
    }

    /**
     * Add script code to the document.
     *
     * <code>
     * $document = JFactory::getDocument();
     *
     * $portfolio = new VipPortfolioGalleria($items, $params);
     * $portfolio->addScriptDeclaration($document);
     * </code>
     *
     * @param JDocument  $document
     *
     * @return self
     */
    public function addScriptDeclaration(JDocument $document)
    {
        $js = '
        jQuery(document).ready(function() {
            Galleria.run("#' . $this->selector . '");
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
     */
    public function render()
    {
        $html = array();

        if (!empty($this->items)) {

            $html[] = '<div id="' . $this->selector . '">';

            foreach ($this->items as $item) {

                if (!$item->image or !$item->thumb) {
                    continue;
                }

                $html[] = '<a href="' . $this->imagesPath . $item->image . '"><img src="' . $this->imagesPath . $item->thumb . '" /></a>';
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
     */
    public function renderOnlyImages()
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

            $html[] = '</div>';
        }

        return implode("\n", $html);
    }
}
