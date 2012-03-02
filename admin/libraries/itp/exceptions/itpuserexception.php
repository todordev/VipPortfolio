<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   ITPrism Exceptions
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ITPrism Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

class ItpUserException extends ItpException {
  
  /**
   * Initialize and log the error data
   * @param  message[optional]
   * @param  code[optional]
   * @param string Extra information about the error
   */
    public function __construct( $message, $code, $data = "" ) {
    	
	   parent::__construct( $message, $code, $data );
	   
    }
    
	/**
	 * 
	 */
	public function __destruct() {
	
	}
}