<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   ITPrism Captcha
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
 * ITPCaptcha
 *
 * @author  Todor Iliev
 * @package ITPrism Libraries
 */
class ItpCaptcha {
    
    private $font = "";
    
    public function generateImage($width = 120, $height=40){
        
        $code = $this->generateCode();
        
        /* font size will be 75% of the image height */
        $font_size = $height * 0.75;
        $image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
        /* set the colours */
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 20, 40, 100);
        $noise_color = imagecolorallocate($image, 100, 120, 180);
        /* generate random dots in background */
        for($i = 0; $i < ($width * $height) / 3; $i ++){
            imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
        }
        /* generate random lines in background */
        for($i = 0; $i < ($width * $height) / 150; $i ++){
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
        }
        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
        $x = ($width - $textbox[4]) / 2;
        $y = ($height - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font, $code) or die('Error in imagettftext function');
        /* output captcha image to browser */
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    
    }
    
    public function isValidCode($code){
        
        $session = JFactory::getSession();
        $originalCode = $session->get("captcha_code", null, "itprism");
        
        $session->clear("captcha_code", "itprism");
        
        if(strcmp($originalCode, $code) != 0){
            return false;
        }
        
        return true;
    }
    
    public function setFont($font) {
        
        $this->font = $font;
        
    }
    
    /**
     * Generates a security code
     * 
     * @param integer $num Maximal value - 32
     * @return string
     */
    protected function generateCode($num = 6){
        
        $num = abs($num);
        if(0 == $num) {
            $num = 6;
        }
        
        $code = md5(uniqid(rand(), true));
        $code = substr($code, 0, $num);
        
        $session = JFactory::getSession();
        $session->set("captcha_code", $code, "itprism");
        
        return $code;
    }
}