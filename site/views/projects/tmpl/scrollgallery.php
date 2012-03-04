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
<div id="itp-vp<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php if($this->params->get("catDesc")) {?>
    <?php   if(!empty($this->category)) { echo $this->category->desc; }; ?>
    <?php }?>
    
    <div id="gallery">
        <div id="scrollGalleryHead">
            <div id="thumbarea">
                <div id="thumbareaContent">
                <?php foreach ( $this->items as $item ) { ?>
                <img 
                src="media/vipportfolio/<?php echo $item->thumb;?>" 
                width="<?php echo $this->params->get('sgThumbWidth', 200); ?>" height="<?php echo $this->params->get('sgThumbHeight', 200); ?>"  />
                <?php }?>
                </div>
            </div>
        </div>
        <div id="scrollGalleryFoot">
            <div id="imagearea">
            
            <?php if(!$this->params->get('sgDisplayCaption')) { ?>
            	<div id="imageareaContent">
            	    <?php foreach ( $this->items as $item ) {?>
                		<img 
                        src="media/vipportfolio/<?php echo $item->image;?>"
                        alt="<?php echo $this->escape( strip_tags($item->title) );?>" 
                        title="<?php echo $this->escape( strip_tags($item->title) );?>" 
                        <?php if($this->params->get('sgImageWidth', 500)) { ?>
                        width="<?php echo $this->params->get('sgImageWidth', 500); ?>"
                        <?php }?>
                        <?php if($this->params->get('sgImageHeight', 500)) { ?>
                        height="<?php echo $this->params->get('sgImageHeight', 500); ?>"
                        <?php }?>
                        />
            		<?php } ?>
                </div> 
            <?php } else {?>
                <div id="imageareaContent">
                <?php foreach ( $this->items as $item ) {?>
                    <div class="caption_container">
                    	<div>
                        	<h4><?php echo $this->escape( strip_tags($item->title) );?></h4>
                        	<p><?php echo $this->escape( strip_tags($item->description) );?></p>
                    	</div>
                    	<img 
                        src="media/vipportfolio/<?php echo $item->image;?>"
                        alt="<?php echo $this->escape( strip_tags($item->title) );?>" 
                        title="<?php echo $this->escape( strip_tags($item->title) );?>" 
                        <?php if($this->params->get('sgImageWidth', 500)) { ?>
                        width="<?php echo $this->params->get('sgImageWidth', 500); ?>"
                        <?php }?>
                        <?php if($this->params->get('sgImageHeight', 500)) { ?>
                        height="<?php echo $this->params->get('sgImageHeight', 500); ?>"
                        <?php }?>
                        />
                	</div>
               	<?php }?>
                </div>
            <?php }?>
        	</div>
        </div>
    </div>

</div>
<?php echo $this->version->url;?>