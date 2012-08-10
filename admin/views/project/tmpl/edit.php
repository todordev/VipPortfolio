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
<div id="itp-formdata">
    <form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_vipportfolio'); ?>" method="post" name="adminForm" id="project-form" class="form-validate" >
    <div class="width-40 itp-prjform">
        <fieldset class="adminform">
            <legend><?php echo JText::_("COM_VIPPORTFOLIO_PROJECT_INFORMATION"); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?></li>
    
                <li><?php echo $this->form->getLabel('catid'); ?>
                <?php echo $this->form->getInput('catid'); ?></li>
                
                <li><?php echo $this->form->getLabel('url'); ?>
                <?php echo $this->form->getInput('url'); ?></li>
    
                <li><?php echo $this->form->getLabel('published'); ?>
                <?php echo $this->form->getInput('published'); ?></li>   
    
                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
                
            </ul>
            
            <div class="clr"></div>
            <?php echo $this->form->getLabel('description'); ?>
            <div class="clr"></div>
            <?php echo $this->form->getInput('description'); ?>
            <div class="clr"></div>
            
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('image'); ?>
                    <?php echo $this->form->getInput('image'); ?></li> 
            </ul>
                
        </fieldset>
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    </form>
    
    <div class="width-40 itp-prjextra">
       
            <fieldset class="adminform">
                <legend><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_IMAGES"); ?></legend>
                <form method="post" action="<?php echo JRoute::_('index.php?option=com_vipportfolio&format=raw&task=project.addExtraImage&id='.$this->item->id); ?>" enctype="multipart/form-data" id="uploadForm">
                    <div>
                      <div class="formRow">
                        <label for="file" class="floated"><?php echo JText::_("COM_VIPPORTFOLIO_FILE"); ?>:</label>
                        <input type="file" id="file" name="file[]" multiple /><br />
                      </div>
                    
                      <div class="formRow">
                        <input type="submit" name="upload" value="<?php echo JText::_("COM_VIPPORTFOLIO_UPLOAD"); ?>" class="btn_eupload">
                   		<img src="../media/com_vipportfolio/images/ajax-loader.gif" style="display: none;" id="ajax_loader" />
                      </div>
                      
                    </div>
                </form>
                <div class="clr"></div>
                <div class="width-100 itp-extra-images" id="itp-extra-images">
                    <?php if(!empty($this->extraImages)){?>
                       <?php foreach($this->extraImages as $image) {?>
                       <div class="ai_box" id="ai_box<?php echo $image['id'];?>">
                           <a href="<?php echo JURI::root() . $this->params->get("images_directory") . "/". $image['image']; ?>">
                               <img src="<?php echo JURI::root() . $this->params->get("images_directory") . "/". $image['thumb']; ?>" />
                           </a>
                           
                           <img class="ai_ri" data-image-id="<?php echo $image['id'];?>" src="../media/com_vipportfolio/images/icon_remove_16.png" />
                           
                       </div>
                       <?php }?>
                   <?php }?>
                </div>
                <div class="clr"></div>
                
            </fieldset>
    </div>
    
</div>

<div class="clr"></div>
<?php if (!empty($this->item->thumb)) {?>
<h4><?php echo JText::_("COM_VIPPORTFOLIO_THUMBNAIL");?></h4>
<img src="<?php echo (JURI::root() . $this->params->get("images_directory") . "/". $this->item->thumb); ?>"  />
<div>
    <img src="<?php echo (JURI::root() . "media/com_vipportfolio/images/remove_image.gif"); ?>" />
    <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&amp;task=project.removeImage&amp;type=thumb&amp;id=" . $this->item->id); ?>" ><?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?></a>
</div>
<?php }?>
<div>&nbsp;</div>
<?php if (!empty($this->item->image)) {?>
<h4><?php echo JText::_("COM_VIPPORTFOLIO_LARGE_IMAGE");?></h4>
<img src="<?php echo (JURI::root() . $this->params->get("images_directory") . "/". $this->item->image); ?>" />
<div>
    <img src="<?php echo (JURI::root() . "media/com_vipportfolio/images/remove_image.gif"); ?>" />
    <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&amp;task=project.removeImage&amp;type=image&amp;id=" . $this->item->id); ?>" ><?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?></a>
</div>
<?php }?>
