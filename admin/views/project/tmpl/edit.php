<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
	<div class="span6 form-horizontal">
	    <form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_vipportfolio'); ?>" method="post" name="adminForm" id="project-form" class="form-validate" >
    
            <fieldset>
            
            	<div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                </div>
            	<div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
                </div>
            	<div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
                </div>
            	<div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('url'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('url'); ?></div>
                </div>
            	<div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
                </div>
            	<div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('thumb'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('thumb'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('image'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
                </div>
                    
            </fieldset>
            
            <?php echo $this->loadTemplate("resize");?>
            
            <div class="clearfix"></div>
            <?php if (!empty($this->item->thumb)) {?>
            <h4><?php echo JText::_("COM_VIPPORTFOLIO_THUMBNAIL");?></h4>
            <img src="<?php echo "../" . $this->params->get("images_directory", "images/vipportfolio") . "/". $this->item->thumb; ?>"  />
            <br />
            
            <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&task=project.removeImage&type=thumb&id=" . $this->item->id); ?>" class="btn btn-danger mtop20" >
                <i class="icon-remove icon-white"></i>
                <?php echo JText::_("COM_VIPPORTFOLIO_DELETE_THUMBNAIL")?>
            </a>
            <?php }?>
            
            <div>&nbsp;</div>
            
            <?php if (!empty($this->item->image)) {?>
            <h4><?php echo JText::_("COM_VIPPORTFOLIO_LARGE_IMAGE");?></h4>
            <img src="<?php echo "../" . $this->params->get("images_directory", "images/vipportfolio") . "/". $this->item->image; ?>" />
            <br />
            
            <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&task=project.removeImage&type=image&id=" . $this->item->id); ?>" class="btn btn-danger mtop20" >
            	<i class="icon-remove icon-white"></i>
                <?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?>
            </a>
            <?php }?>
    
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
	</div>
	
    <?php if(!empty($this->item->id)){?>
    <div class="span6">
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
    				<div class="controls">
        				<input type="text" class="inputbox" value="50" id="extra_thumb_width" name="extra_thumb_width">		
        			</div>
            	</div>
            	<div class="control-group">
					<div class="control-label">
        				<label for="extra_thumb_height" id="extra_thumb_height-lbl"><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_THUMB_HEIGHT");?></label>			
    				</div>
    				<div class="controls">
        				<input type="text" class="inputbox" value="50" id="extra_thumb_height" name="extra_thumb_height">		
        			</div>
            	</div>
            	<div class="control-group">
					<div class="control-label">
        				<label for="extra_thumb_scale" id="extra_thumb_scale-lbl"><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_THUMB_SCALE");?></label>			
    				</div>
    				<div class="controls">
    				    <select class="inputbox" name="extra_thumb_scale" id="extra_thumb_scale">
                        	<option value="1"><?php echo JText::_("COM_VIPPORTFOLIO_FILL");?></option>
                        	<option value="2" selected="selected"><?php echo JText::_("COM_VIPPORTFOLIO_INSIDE");?></option>
                        	<option value="3"><?php echo JText::_("COM_VIPPORTFOLIO_OUTSIDE");?></option>
                        </select>
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
                           		<button class="btn btn-danger ai_ri" data-image-id="<?php echo $image['id'];?>" ><?php echo JText::_("COM_VIPPORTFOLIO_REMOVE");?></button>
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

