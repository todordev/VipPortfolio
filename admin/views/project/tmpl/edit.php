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
    <form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_vipportfolio&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="project-form" class="form-validate" >
    <div id="itp-prjform">
        <div class="width-40 fltlft">
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
        
                    <li><?php echo $this->form->getLabel('thumb'); ?>
                    <?php echo $this->form->getInput('thumb'); ?></li>
                    
                    <li><?php echo $this->form->getLabel('image'); ?>
                    <?php echo $this->form->getInput('image'); ?></li>   
                    
                    <li><?php echo $this->form->getLabel('id'); ?>
                    <?php echo $this->form->getInput('id'); ?></li>
                    
                </ul>
                
                <div class="clr"></div>
                <?php echo $this->form->getLabel('description'); ?>
                <div class="clr"></div>
                <?php echo $this->form->getInput('description'); ?>
                <div class="clr"></div>
            </fieldset>
        </div>
    </div>
    
    <div id="itp-prjextra">
        <div class="width-40 fltlft">
            <fieldset class="adminform">
                <legend><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_IMAGES"); ?></legend>
                <ul class="adminformlist">
                    <li>
                    <label><?php echo JText::_("COM_VIPPORTFOLIO_FIELD_IMAGE");?> 1</label>                
                    <input type="file" size="60" class="inputbox" value="" name="extra[1]" />
                    </li>
                    
                    <li>
                    <label><?php echo JText::_("COM_VIPPORTFOLIO_FIELD_IMAGE");?> 2</label>                
                    <input type="file" size="60" class="inputbox" value="" name="extra[2]" />
                    </li>
                    
                    <li>
                    <label><?php echo JText::_("COM_VIPPORTFOLIO_FIELD_IMAGE");?> 3</label>                
                    <input type="file" size="60" class="inputbox" value="" name="extra[3]" />
                    </li>
                </ul>
                <div class="clr"></div>
                <?php if(!empty($this->extraImages)){?>
                <div id="itp-extra-images">
                       <?php foreach($this->extraImages as $image) {?>
                       <a class="trashable" id="<?php echo $image['id']?>" href="<?php echo JURI::root() . "media/vipportfolio/" . $image['name']; ?>" rel="lightbox-atomium" >
                        <img style="cursor:pointer;" width="48" height="48" src="<?php echo JURI::root() . "media/vipportfolio/ethumb_" . $image['name']; ?>" alt=""/>
                       </a>
                       <?php }?>
                </div>
                <div class="clr"></div>
                <div id="trash"></div>
                <div class="clr"></div>
                <p class="itp_note"><?php echo JText::_("COM_VIPPORTFOLIO_EXTRA_IMAGES_NOTE"); ?></p>
                <?php }?>
            </fieldset>
        </div>
    </div>
    <div class="clr"></div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    </form>
</div>

<div class="clr"></div>
<?php if (!empty($this->item->thumb)) {?>
<h4><?php echo JText::_("COM_VIPPORTFOLIO_THUMBNAIL");?></h4>
<img src="<?php echo (JURI::root() . "media/vipportfolio/" . $this->item->thumb); ?>"  />
<div>
    <img src="<?php echo (JURI::root() . "media/com_vipportfolio/images/remove_image.gif"); ?>" />
    <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&amp;task=project.removeImage&amp;type=thumb&amp;id=" . $this->item->id); ?>" ><?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?></a>
</div>
<?php }?>
<div>&nbsp;</div>
<?php if (!empty($this->item->image)) {?>
<h4><?php echo JText::_("COM_VIPPORTFOLIO_LARGE_IMAGE");?></h4>
<img src="<?php echo (JURI::root() . "media/vipportfolio/" . $this->item->image); ?>" />
<div>
    <img src="<?php echo (JURI::root() . "media/com_vipportfolio/images/remove_image.gif"); ?>" />
    <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&amp;task=project.removeImage&amp;type=image&amp;id=" . $this->item->id); ?>" ><?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?></a>
</div>
<?php }?>
