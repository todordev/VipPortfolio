<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipPorfolio
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipPorfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr" >
    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <link rel="stylesheet" href="<?php echo JURI::root();?>media/com_vipportfolio/css/fb.css" type="text/css" />
      <link rel="stylesheet" href="<?php echo JURI::root();?>media/com_vipportfolio/js/lightface/css/LightFace.css" type="text/css" />
      
      <script src="<?php echo JURI::root();?>media/system/js/mootools-core.js" type="text/javascript"></script>
      <script src="<?php echo JURI::root();?>media/com_vipportfolio/js/lightface/LightFace.js" type="text/javascript"></script>
      <script src="<?php echo JURI::root();?>media/com_vipportfolio/js/lightface/LightFace.Image.js" type="text/javascript"></script>
      <script>
      window.addEvent('domready',function(){

          var modal = new LightFace.Image();
          $$('a[rel="lightface"]').addEvent('click', function(event) {
        	    event.preventDefault();
                modal.load(this.href,"Image Preview").open();
          });
      });

      window.fbAsyncInit = function() {
    	  FB.init({ 
  	        appId: "<?php echo $this->params->get("fbpp_app_id", "");?>", 
  	        cookie:true, 
  	        status:true, 
  	        xfbml:true,
  	        oauth  : true
  	     });

    	  FB.Canvas.setAutoGrow();
    	  
      };

      // Load the SDK Asynchronously
      (function(d){
         var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         d.getElementsByTagName('head')[0].appendChild(js);
       }(document));
      
      </script>
    </head>
    <body>

    <div id="itp-vp<?php echo $this->pageclass_sfx;?>">
        <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        <?php } ?>
    
        <?php if($this->params->get("catDesc")) {?>
        <?php   if(!empty($this->category)) { echo $this->category->desc; }; ?>
        <?php }?>
        <?php foreach ( $this->items as $item ) {?>
        <div class="itp-vp-box" style="width: <?php echo $this->params->get('thumb_width', 200); ?>; height: <?php echo $this->params->get('thumb_height', 200); ?>;">
            <!--  Start Images -->
            <?php if( !empty( $item->thumb ) ) { ?>
            <div class="itp-vp-image-box">
               <a href="<?php echo JURI::root(); ?>media/vipportfolio/<?php echo $item->image;?>"  rel="lightface">   
                <img
                width="<?php echo $this->params->get('thumb_width', 200); ?>" height="<?php echo $this->params->get('thumb_height', 200); ?>" 
                src="<?php echo JURI::root() . "media/vipportfolio/" . $item->thumb;?>" 
                alt="<?php echo htmlentities( strip_tags($item->title), ENT_QUOTES,"UTF-8" );?>" 
                title="<?php echo htmlentities( strip_tags($item->title), ENT_QUOTES,"UTF-8" );?>" 
                
                />  
              </a>
                <?php if(isset($this->extraImages)){?>
                <div class="itp-vp-extra-image">
                 <?php 
                 if (isset($this->extraImages[$item->id]) AND !empty($this->extraImages[$item->id])){
                      $i = 0;
                     foreach($this->extraImages[$item->id] as $eImage){?>
                      
                        <a href="media/vipportfolio/<?php echo $eImage['name'];?>" rel="lightface" >
                            <img
                            width="48" height="48" 
                            src="media/vipportfolio/ethumb_<?php echo $eImage['name'];?>" 
                            alt="" 
                            title="" 
                            />  
                        </a>
                        
                     <?php
                      $i++;
                      if($i==$this->extraMax){ break; }
                     }
                 }?>
                </div>
                <?php }?>
            </div>
            <?php } ?>
            <!--  End Images -->
            <!--  Start Information -->
            <div class="itp-vp-text-box">
             <h3 class="itp-vp-title" >
             <?php if($this->params->get("title_linkable") AND $item->url) { ?>
             <a href="<?php echo $item->url;?>"><?php echo $item->title;?></a>
             <?php }else{?>
             <?php echo $item->title;?>
             <?php }?>
             </h3>
            <p><?php echo $item->description;?></p>
            </div>
            <!--  End Information -->
        </div>
        <?php }?>
    </div>
    <?php echo $this->version->url;?>
   </body>
</html>