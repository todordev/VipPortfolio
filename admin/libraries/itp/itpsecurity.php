<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   ITPrism Security
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

class ItpSecurity {
	
	private $e = null;
	private $loggerOptions = array();
	
	/**
	 * 
	 * @param $e Exception object
	 */
	public function __construct( $e = null ) {
		$this->e = $e;
	}
	
	/**
	 * Send information to my email
	 * 
	 * @param $message
	 * @todo Do email sending.
	 */
	public function alertMe() {
		
	    $message = $this->genMessage();
		$this->log($message);
		$this->sendMail($message);
           
	}
	
    public function log($message) {
        
        $entry     = new JLogEntry($message);
        $logger    = new JLoggerFormattedText($this->loggerOptions);
        $logger->addEntry($entry, JLog::ALERT);
    }
	
    public function genMessage() {
        
        $message = "";
        if(empty($this->e)) {
            return $message;
        }
        $trace = "";
        foreach($this->e->trace as $v) {
            $trace .="===================================\n";
			$trace .="FILE:"     . $v['file'] . "\n";
			$trace .="LINE:"     . $v['line'] . "\n";
			$trace .="CLASS:"    . $v['class'] . "\n";
			$trace .="FUNCTION:" . $v['function'] . "\n";
//			$trace .="ARGS:"     . var_export($v['args'], true) . "\n";
			$trace .="====================================\n";
		}
		
		$message = "*****************************************\n";
        $message = "\nFILE : " . $this->e->getFile()  . "\n";
        $message .= "LINE : " . $this->e->getLine() . "\n";
        $message .= "CODE : " . $this->e->getCode() . "\n";
        $message .= "MESSAGE : " . $this->e->getMessage() . "\n";
        if($this->data) {
            $message .= "EXTRA INFO : " . $this->e->data . "\n";
        }
        if($trace) {
            $message .= "TRACE : " . $trace . "\n";
        }
        $message = "*****************************************\n";
        
    }
    
    /**
     * Send a mail to the administrator
     */
    public function sendMail($message) {
        
    }
    
	/**
	 * 
	 */
	public function __destruct() {
	
	}
}