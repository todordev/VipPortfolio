<?php
/**
 * @package      ITPrism Components
 * @subpackage   Vip Portfolio
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Portfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="itp-vp-lbox">
<?php if( !empty( $this->item->thumb ) ) { ?>
<div class="itp-vp-limage-box" style="width: <?php echo $this->params->get('lineal_thumb_width', 300); ?>; height: <?php echo $this->params->get('lineal_thumb_height', 300); ?>;">
   <?php if ($this->params->get("lLinkable") OR !empty($this->modal) ) {?>
       <a href="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$this->item->image;?>" rel="lightbox-item<?php echo $this->item->id;?>"  >
   <?php }?>

	<img
	width="<?php echo $this->params->get('lineal_thumb_width', 300); ?>" height="<?php echo $this->params->get('lineal_thumb_height', 300); ?>" 
	src="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$this->item->thumb;?>" 
	alt="<?php echo $this->escape($this->item->title);?>" 
	title="<?php echo $this->escape($this->item->title);?>" 
	/>  
	
   <?php if ($this->params->get("lLinkable") OR !empty($this->modal) ) {?> </a><?php }?>
    
	<div class="itp-vp-extra-image">
     <?php 
     if (isset($this->extraImages[$this->item->id]) AND !empty($this->extraImages[$this->item->id])){
          $i = 0;
         foreach($this->extraImages[$this->item->id] as $eImage){?>
          
            <a href="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$eImage['image'];?>" rel="lightbox-item<?php echo $this->item->id;?>" >
                <img
                width="<?php echo $this->params->get('lineal_extra_thumb_width', 50); ?>" 
                height="<?php echo $this->params->get('lineal_extra_thumb_height', 50); ?>" 
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
    
</div>
<?php } ?>
<div class="itp-vp-ltext-box">
    <?php if ($this->params->get("lDisplayTitle")) {?>
    <h3 class="itp-vp-ltitle" >
        <?php if($this->params->get("lTitleLinkable") AND $this->item->url ) { ?>
        <a href="<?php echo $this->item->url;?>" <?php echo $this->openLink;?>><?php echo $this->item->title;?></a>
        <?php } else { ?>
        <?php echo $this->item->title;?>
   	    <?php }?>
    </h3>
    <?php echo (!empty($this->event->afterDisplayTitle) ) ? $this->event->afterDisplayTitle : ""; ?>
    <?php }?>
    
    <?php echo (!empty($this->event->beforeDisplayContent) ) ? $this->event->beforeDisplayContent : ""; ?>
    
	<p><?php echo $this->item->description;?></p>
	
    <?php if ($this->params->get("lDisplayUrl")) {?>
    <a href="<?php echo $this->item->url;?>" title="<?php echo $this->item->title;?>" <?php echo $this->openLink;?>><?php echo $this->item->url;?></a>
    <?php }?>
    
    </div>
</div>