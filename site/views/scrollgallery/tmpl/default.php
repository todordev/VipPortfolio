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
defined('_JEXEC') or die;
?>
<?php if ($this->params->get('show_page_heading', 1)) { ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php } ?>

<?php if($this->params->get("catDesc")) {?>
<?php   if(!empty($this->category)) { echo $this->category->desc; }; ?>
<?php }?>

<?php if(!empty($this->items)) {?>

<div id="gallery">
    <div id="scrollGalleryHead">
        <div id="thumbarea">
            <div id="thumbareaContent">
            <?php foreach ( $this->items as $item ) { ?>
            <img 
            src="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$item->thumb;?>" 
            width="<?php echo $this->params->get('sg_thumb_width', 200); ?>" 
            height="<?php echo $this->params->get('sg_thumb_height', 200); ?>"  />
            <?php }?>
            </div>
        </div>
    </div>
    <div id="scrollGalleryFoot">
        <div id="imagearea">
        
        <?php if(!$this->params->get('sg_display_caption')) { ?>
        	<div id="imageareaContent">
        	    <?php foreach ( $this->items as $item ) {?>
            		<img 
                    src="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$item->image;?>"
                    alt="<?php echo $this->escape( strip_tags($item->title) );?>" 
                    title="<?php echo $this->escape( strip_tags($item->title) );?>" 
                    <?php if($this->params->get('sg_image_width', 500)) { ?>
                    width="<?php echo $this->params->get('sg_image_width', 500); ?>"
                    <?php }?>
                    <?php if($this->params->get('sg_image_height', 500)) { ?>
                    height="<?php echo $this->params->get('sg_image_height', 500); ?>"
                    <?php }?>
                    />
        		<?php } ?>
            </div> 
        <?php } else {?>
            <div id="imageareaContent">
            <?php foreach ( $this->items as $item ) {?>
                <div class="caption_container">
                	<div>
                		<?php if($this->params->get("sg_title_linkable", 0) AND !empty($item->url)) {?>
                    	<h4><a href="<?php echo $item->url;?>" <?php echo $this->openLink;?>><?php echo $this->escape( strip_tags($item->title) );?></a></h4>
                    	<?php } else {?>
                    	<h4><?php echo $this->escape( strip_tags($item->title) );?></h4>
                    	<?php }?>
                    	<p><?php echo $this->escape( strip_tags($item->description) );?></p>
                	</div>
                	<img 
                    src="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$item->image;?>"
                    alt="<?php echo $this->escape( strip_tags($item->title) );?>" 
                    title="<?php echo $this->escape( strip_tags($item->title) );?>" 
                    <?php if($this->params->get('sg_image_width', 500)) { ?>
                    width="<?php echo $this->params->get('sg_image_width', 500); ?>"
                    <?php }?>
                    <?php if($this->params->get('sg_image_height', 500)) { ?>
                    height="<?php echo $this->params->get('sg_image_height', 500); ?>"
                    <?php }?>
                    />
            	</div>
           	<?php }?>
            </div>
        <?php }?>
    	</div>
    </div>
</div>
<?php } ?>