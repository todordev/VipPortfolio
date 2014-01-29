<?php
/**
 * @package      VipPortfolio
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>

 <?php foreach ( $this->items as $item ) {?>
    <div class="row-fluid">
        <?php if( !empty( $item->thumb ) ) { ?>
        <div class="span3">
           <?php if ($this->params->get("image_linkable") OR !empty($this->modal) ) {?>
               <a href="<?php echo $this->imagesUri.$item->image;?>" class="<?php echo $this->modalClass;?>" >
           <?php }?>
        
            <img
            width="<?php echo $this->params->get('thumb_width', 200); ?>" 
            height="<?php echo $this->params->get('thumb_height', 200); ?>"
            src="<?php echo $this->imagesUri . $item->thumb;?>" 
            alt="<?php echo $this->escape($item->title);?>" 
            title="<?php echo $this->escape($item->title);?>" 
            class="thumbnail"
            />  
            
            <?php if ($this->params->get("image_linkable") OR !empty($this->modal) ) {?></a><?php } ?>
            
            <?php if(isset($this->extraImages)){?>
            <div class="itp-vp-extra-image">
             <?php 
             if (isset($this->extraImages[$item->id]) AND !empty($this->extraImages[$item->id])){
                  $i = 0;
                 foreach($this->extraImages[$item->id] as $eImage){?>
                  
                    <a href="<?php echo $this->imagesUri.$eImage['image'];?>" class="<?php echo $this->modalClass;?>">
                        <img
                        width="<?php echo $this->params->get('ei_thumb_width', 50); ?>" 
                        height="<?php echo $this->params->get('ei_thumb_width', 50); ?>" 
                        src="<?php echo $this->imagesUri.$eImage['thumb'];?>" 
                        alt="" 
                        title="" 
                        class="thumbnail"
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
        <div class="span9">
             <?php if ($this->params->get("list_display_title")) {?>
             <h3>
             <?php if($this->params->get("title_linkable") AND $item->url) { ?>
             	<a href="<?php echo $item->url;?>" <?php echo $this->openLink;?>><?php echo $this->escape($item->title);?></a>
             <?php }else{?>
                 <?php echo $this->escape($item->title);?>
             <?php }?>
             </h3>
             <?php }?>
             
            <p><?php echo $item->description;?></p>
        </div>
    </div>
    <?php }?>