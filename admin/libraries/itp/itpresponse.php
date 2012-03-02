<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   ITPrism Response
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ITPrism Library Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * ITP Response
 *
 * @author  Todor Iliev
 * @package ITPrism Libraries
 */
class ItpResponse {
	
	/**
     * Send message as JSON notation
     * @param string Message
     * @param integer Indicator for success [ 0 = failure, 1 = success ] 
     */
    public static function sendJsonMsg($message,$success = 0, $data = array()){
        
        $msg  =   array(
          "msg" =>strval($message),
          "success" => $success
        );
            
        if(!empty($data)) {
            $msg['data'] = json_encode($data);
        }
        
        echo json_encode($msg);
    }
	
}