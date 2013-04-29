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
<div id="itp-formdata">
    <form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_vipportfolio'); ?>" method="post" name="adminForm" id="project-form" class="form-validate" >
    <div class="width-40 itp-prjform">
        <fieldset class="adminform">
            <legend><?php echo JText::_("COM_VIPPORTFOLIO_PROJECT_INFORMATION"); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?></li>
    
    			<li><?php echo $this->form->getLabel('alias'); ?>
                <?php echo $this->form->getInput('alias'); ?></li>
                
                <li><?php echo $this->form->getLabel('catid'); ?>
                <?php echo $this->form->getInput('catid'); ?></li>
                
                <li><?php echo $this->form->getLabel('url'); ?>
                <?php echo $this->form->getInput('url'); ?></li>
    
                <li><?php echo $this->form->getLabel('published'); ?>
                <?php echo $this->form->getInput('published'); ?></li>   
    
                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
                
                <li><?php echo $this->form->getLabel('image'); ?>
                    <?php echo $this->form->getInput('image'); ?></li> 
                    
                <li><?php echo $this->form->getLabel('thumb'); ?>
                    <?php echo $this->form->getInput('thumb'); ?></li> 
            </ul>
            
            <div class="clr"></div>
            <?php echo $this->form->getLabel('description'); ?>
            <div class="clr"></div>
            <?php echo $this->form->getInput('description'); ?>
            <div class="clr"></div>
            
                
        </fieldset>
        
        <?php echo $this->loadTemplate("resize");?>
        
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    </form>
    
    <?php if(!empty($this->item->id)){?>
    <div class="width-40 itp-prjextra">
       
        <form id="fileupload" action="<?php echo JRoute::_('index.php?option=com_vipportfolio&format=raw&task=project.addExtraImage&id='.$this->item->id); ?>" method="POST" enctype="multipart/form-data">
    	
    	<fieldset>
            <legend><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_IMAGES"); ?></legend>
            
        	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
            <div class="fileupload-buttonbar">
                <div >
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-success fileinput-button">
                        <i class="icon-upload icon-white"></i>
                        <span><?php echo JText::_("COM_VIPPORTFOLIO_UPLOAD");?></span>
                        <input type="file" name="files[]" multiple />
                    </span>
                </div>
            </div>
           	<img src="../media/com_vipportfolio/images/ajax-loader.gif" id="ajax_loader" style="display: none;" />
           	<div class="clearfix"></div>
           	
           	<div class="control-group">
				<div class="control-label">
    				<label for="extra_thumb_width" id="extra_thumb_width-lbl"><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_THUMB_WIDTH");?></label>			
				</div>
				<div class="clearfix"></div>
				<div class="controls">
    				<input type="text" class="inputbox" value="50" id="extra_thumb_width" name="extra_thumb_width">		
    			</div>
        	</div>
        	<div class="control-group">
				<div class="control-label">
    				<label for="extra_thumb_height" id="extra_thumb_height-lbl"><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_THUMB_HEIGHT");?></label>			
				</div>
				<div class="clearfix"></div>
				<div class="controls">
    				<input type="text" class="inputbox" value="50" id="extra_thumb_height" name="extra_thumb_height">		
    			</div>
        	</div>
        	<div class="clearfix"></div>
        	
           	<table class="table table-bordered" id="itp-extra-images">
           		<?php if(!empty($this->extraImages)){?>
                   <?php foreach($this->extraImages as $image) {?>
                   <tr id="ai_box<?php echo $image['id'];?>">
                       <td class="span10">
                           <img src="<?php echo "../" . $this->params->get("images_directory", "images/vipportfolio") . "/". $image['thumb']; ?>" data-image-url="<?php echo "../" . $this->params->get("images_directory", "images/vipportfolio") . "/". $image['image']; ?>" class="ai-imglink"/>
                       </td>
                       <td class="span2">
                       		<button class="btn ai_ri" data-image-id="<?php echo $image['id'];?>" ><?php echo JText::_("COM_VIPPORTFOLIO_REMOVE");?></button>
                       </td>
                   </tr>
                   <?php }?>
               <?php }?>
               
           	</table>
            <div class="clearfix"></div>
    	</fieldset>
     </form>
    </div>
    <?php }?>
    
</div>

<div class="clr"></div>
<?php if (!empty($this->item->thumb)) {?>
<h4><?php echo JText::_("COM_VIPPORTFOLIO_THUMBNAIL");?></h4>
<img src="<?php echo "../" . $this->params->get("images_directory", "images/vipportfolio") . "/". $this->item->thumb; ?>"  />
<div>
    <img src="../media/com_vipportfolio/images/remove_image.png" />
    <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&task=project.removeImage&type=thumb&id=" . $this->item->id); ?>" ><?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?></a>
</div>
<?php }?>
<div>&nbsp;</div>
<?php if (!empty($this->item->image)) {?>
<h4><?php echo JText::_("COM_VIPPORTFOLIO_LARGE_IMAGE");?></h4>
<img src="<?php echo "../" . $this->params->get("images_directory", "images/vipportfolio") . "/". $this->item->image; ?>" />
<div>
    <img src="../media/com_vipportfolio/images/remove_image.png" />
    <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&task=project.removeImage&type=image&id=" . $this->item->id); ?>" ><?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?></a>
</div>
<?php }?>
