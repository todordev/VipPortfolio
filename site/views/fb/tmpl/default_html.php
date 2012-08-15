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
defined('_JEXEC') or die;
?>
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
           <a href="<?php echo $this->params->get("images_directory", "images/vipportfolio") . "/".$item->image;?>"  rel="lightface">   
            <img
            width="<?php echo $this->params->get('thumb_width', 200); ?>" 
            height="<?php echo $this->params->get('thumb_height', 200); ?>" 
            src="<?php echo $this->params->get("images_directory", "images/vipportfolio") . "/".$item->thumb;?>" 
            alt="<?php echo $this->escape($item->title);?>" 
            title="<?php echo $this->escape($item->title);?>" 
            />  
          </a>
            <?php if(isset($this->extraImages)){?>
            <div class="itp-vp-extra-image">
             <?php 
             if (isset($this->extraImages[$item->id]) AND !empty($this->extraImages[$item->id])){
                  $i = 0;
                 foreach($this->extraImages[$item->id] as $eImage){?>
                  
                    <a href="<?php echo $this->params->get("images_directory", "images/vipportfolio") . "/".$eImage['image'];?>" rel="lightface" >
                        <img
                        width="<?php echo $this->params->get('ei_thumb_width', 50); ?>" 
                    	height="<?php echo $this->params->get('ei_thumb_width', 50); ?>" 
                        src="<?php echo $this->params->get("images_directory", "images/vipportfolio") . "/".$eImage['thumb'];?>" 
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
         <a href="<?php echo $item->url;?>"><?php echo $this->escape($item->title);?></a>
         <?php }else{?>
         <?php echo $this->escape($item->title);?>
         <?php }?>
         </h3>
        <p><?php echo $item->description;?></p>
        </div>
        <!--  End Information -->
    </div>
    <?php }?>
</div>
<?php echo $this->version->backlink;?>
