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

 <?php foreach ( $this->items as $item ) {?>
    <div class="itp-vp-box" style="width: <?php echo $this->params->get('thumb_width', 200); ?>; height: <?php echo $this->params->get('thumb_height', 200); ?>;">
        <?php if( !empty( $item->thumb ) ) { ?>
        <div class="itp-vp-image-box">
           <?php if ($this->params->get("image_linkable") OR !empty($this->modal) ) {?>
               <a href="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$item->image;?>" rel="lightbox-item<?php echo $item->id;?>"  >
           <?php }?>
        
            <img
            width="<?php echo $this->params->get('thumb_width', 200); ?>" 
            height="<?php echo $this->params->get('thumb_height', 200); ?>"
            src="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/" . $item->thumb;?>" 
            alt="<?php echo $this->escape($item->title);?>" 
            title="<?php echo $this->escape($item->title);?>" 
            />  
            
            <?php if ($this->params->get("image_linkable") OR !empty($this->modal) ) {?></a><?php } ?>
            
            <?php if(isset($this->extraImages)){?>
            <div class="itp-vp-extra-image">
             <?php 
             if (isset($this->extraImages[$item->id]) AND !empty($this->extraImages[$item->id])){
                  $i = 0;
                 foreach($this->extraImages[$item->id] as $eImage){?>
                  
                    <a href="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$eImage['image'];?>" rel="lightbox-item<?php echo $item->id;?>" >
                        <img
                        width="<?php echo $this->params->get('ei_thumb_width', 50); ?>" 
                        height="<?php echo $this->params->get('ei_thumb_width', 50); ?>" 
                        src="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$eImage['thumb'];?>" 
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
        <div class="itp-vp-text-box">
        <?php if ($this->params->get("list_display_title")) {?>
         <h3 class="itp-vp-title" >
         <?php if($this->params->get("title_linkable") AND $item->url) { ?>
         	<a href="<?php echo $item->url;?>" <?php echo $this->openLink;?>><?php echo $item->title;?></a>
         <?php }else{?>
             <?php echo $item->title;?>
         <?php }?>
         </h3>
         <?php }?>
        <p><?php echo $item->description;?></p>
        </div>
    </div>
    <?php }?>