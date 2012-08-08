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
<form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_vipportfolio&layout=edit&id='.(int) $this->item->id); ?>" 
method="post" name="adminForm" id="category-form" class="form-validate">
    <div class="width-40 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_("COM_VIPPORTFOLIO_CATEGORY_INFORMATION"); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('name'); ?>
                <?php echo $this->form->getInput('name'); ?></li>
    
                <li><?php echo $this->form->getLabel('alias'); ?>
                <?php echo $this->form->getInput('alias'); ?></li>
    
                <li><?php echo $this->form->getLabel('published'); ?>
                <?php echo $this->form->getInput('published'); ?></li>   
    
                <li><?php echo $this->form->getLabel('image'); ?>
                <?php echo $this->form->getInput('image'); ?></li>   
                
                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
                
            </ul>
            
            <div class="clr"></div>
            <?php echo $this->form->getLabel('desc'); ?>
            <div class="clr"></div>
            <?php echo $this->form->getInput('desc'); ?>
            <div class="clr"></div>
            
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('spacer'); ?></li>
                <li><?php echo $this->form->getLabel('meta_title'); ?>
                <?php echo $this->form->getInput('meta_title'); ?></li>
    
                <li><?php echo $this->form->getLabel('meta_keywords'); ?>
                <?php echo $this->form->getInput('meta_keywords'); ?></li>
    
                <li><?php echo $this->form->getLabel('meta_desc'); ?>
                <?php echo $this->form->getInput('meta_desc'); ?></li>   
    
                <li><?php echo $this->form->getLabel('meta_canonical'); ?>
                <?php echo $this->form->getInput('meta_canonical'); ?></li>   
                
            </ul>

        </fieldset>
    </div>
    <div class="clr"></div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<?php if (!empty($this->item->image)) {?>
<img src="<?php echo (JURI::root() . $this->params->get("images_directory") . "/". $this->item->image); ?>" alt="<?php echo $this->item->name;?>" />
<div>
    <img src="<?php echo (JURI::root() . "media/com_vipportfolio/images/remove_image.gif"); ?>" alt="<?php echo $this->item->name;?>" />
    <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&amp;task=category.removeImage&amp;id=" . $this->item->id); ?>" ><?php echo JText::_("COM_VIPPORTFOLIO_DELETE_IMAGE")?></a>
</div>
<?php }?>