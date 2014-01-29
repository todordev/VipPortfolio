<?php
/**
 * @package		 Vip Portfolio
 * @subpackage	 Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

interface VipPortfolioInterfacePortfolio {
    
    public static function load();
    public function addScriptDeclaration(JDocument $document);
    public function setImagesPath($imagesPath);
    public function setSelector($selector);
    public function render();
    
}
