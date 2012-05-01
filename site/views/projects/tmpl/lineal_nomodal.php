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
<div class="itp-vp-lbox" style="width: <?php echo $this->params->get('lThumbWidth', 300); ?>; height: <?php echo $this->params->get('lThumbHeight', 300); ?>;">
    <?php if( !empty( $this->item->thumb ) ) { ?>
    <div class="itp-vp-limage-box">
       <?php if ($this->params->get("lLinkable")) {?>
       <a href="media/vipportfolio/<?php echo $this->item->image;?>" > 
       <?php }?>
    	<img
    	width="<?php echo $this->params->get('lThumbWidth', 300); ?>" height="<?php echo $this->params->get('lThumbHeight', 300); ?>" 
    	src="media/vipportfolio/<?php echo $this->item->thumb;?>" 
    	alt="<?php echo htmlentities( strip_tags($this->item->title), ENT_QUOTES,"UTF-8" );?>" 
    	title="<?php echo htmlentities( strip_tags($this->item->title), ENT_QUOTES,"UTF-8" );?>" 
    	/>  
        <?php if ($this->params->get("lLinkable")) {?></a><?php } ?>
    	
    	<div class="itp-vp-extra-image">
         <?php 
         if (isset($this->extraImages[$this->item->id]) AND !empty($this->extraImages[$this->item->id])){
              $i = 0;
             foreach($this->extraImages[$this->item->id] as $eImage){?>
                <a href="media/vipportfolio/<?php echo $eImage['name'];?>">
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
        
    </div>
    <?php } ?>
    <div class="itp-vp-ltext-box">
    <?php if ($this->params->get("lDisplayTitle")) {?>
     <h3 class="itp-vp-ltitle" >
     <?php if($this->params->get("lTitleLinkable") AND $this->item->url ) { ?>
     	<a href="<?php echo $this->item->url;?>"><?php echo $this->item->title;?></a>
     <?php } else { ?>
         <?php echo $this->item->title;?>
     <?php }?>
     </h3>
    <?php }?>
    <p><?php echo $this->item->description;?></p>
    <?php if ($this->params->get("lDisplayUrl")) {?>
    <a href="<?php echo $this->item->url;?>" title="<?php echo $this->item->title;?>" ><?php echo $this->item->url;?></a>
    <?php }?>
    
    </div>
</div>