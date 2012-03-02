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

class ItpException extends Exception {
  
  /**
   * 
   * @param  message[optional]
   * @param  code[optional]
   */
    public function __construct( $message, $code, $data = null ) {
    	
	   parent::__construct ( $message, $code );
	   
        if(!empty($data)) {
            if(!is_string($data)) {
                $data = var_export($data,1);
            }
            $this->log($data);
        }
       
    }

    public function log($text = "") {
        
       $message = "\nFILE : " . $this->getFile()  . "\n";
       $message .= "LINE : " . $this->getLine() . "\n";
       $message .= "CODE : " . $this->getCode() . "\n";
       $message .= "MESSAGE : " . $this->getMessage() . "\n";
       $message .= "EXTRA INFO : " . $text . "\n";
       
        // get an instance of JLog for myerrors log file
        $log = JLog::getInstance();
        // create entry array
        $entry = array(
            'LEVEL' => $this->getCode(),
            'STATUS' => $this->getMessage(),
            'COMMENT' => $message
        );
        // add entry to the log
        $log->addEntry($entry);
        
    }
    
	/**
	 * 
	 */
	public function __destruct() {
	
	}
}