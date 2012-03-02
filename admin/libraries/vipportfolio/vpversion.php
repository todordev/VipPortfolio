<?php
/**
* @package      ITPrism Components
* @subpackage   Vip Portfolio
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Vip Portfolio is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_BASE') or die();

/**
 * Version information
 *
 * @package ITPrism Components
 * @subpackage   Vip Portfolio
  */
class VpVersion {
	
    /**
     * Extension name
     * 
     * @var string
     */
    public $product    = 'Vip Portfolio';
    
    /**
     * Main Release Level
     * 
     * @var integer
     */
    public $release    = '3.1';
    
    /**
     * Development Status
     * 
     * @var string
     */
    public $devStatus = 'Stable';
    
    /**
     * Sub Release Level
     * 
     * @var integer
     */
    public $devLevel  = '0';
    
    /**
     * Date
     * 
     * @var string
     */
    public $releaseDate= 'March-2012';
    
    /**
     * Copyright Text
     * 
     * @var string
     */
    public $copyright  = 'Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.';
    
    /**
     * URL
     * 
     * @var string
     */
    public $url        = '<div style="width:100%;text-align: left; font-size: xx-small; margin-top: 10px;"><a href="http://itprism.com/free-joomla-extensions/vip-portfolio-component">Joomla! portfolio</a></div>';

    /**
     *  Build long format of the vpversion text
     *
     * @return string Long format vpversion
     */
    public function getLongVersion() {
        
    	return 
    	   $this->product .' '. $this->release .'.'. $this->devLevel .' ' . 
    	   $this->devStatus . ' '. $this->releaseDate;
    }

    /**
     *  Build short format of the vpversion text
     *
     * @return string Short vpversion format
     */
    public function getShortVersion() {
        return $this->release .'.'. $this->devLevel;
    }

}