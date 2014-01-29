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
	<div class="span6 form-vertical">
        <form action="<?php echo JRoute::_('index.php?option=com_vipportfolio'); ?>" method="post" name="adminForm" id="csseditor-form" class="form-validate" >
        
        <fieldset>
            <legend><?php echo JText::_("COM_VIPPORTFOLIO_MANAGE_CSS_FILES"); ?></legend>
            <div class="control-group">
                <div class="control-label">
                	<label for="vp_style_files" >
                	<?php echo JText::_("COM_VIPPORTFOLIO_STYLE_FILE");?>
                	<img src="../media/com_vipportfolio/images/ajax-loader.gif" style="display: none;" id="ajax_loader" />
                	</label>
                </div>
    			<div class="controls">
    				<select name="style_file" id="vp_style_files">
                    	<option value="0" <?php echo ($this->styleFile == 0) ? 'selected="selected"': ""; ?>><?php echo JText::_("COM_VIPPORTFOLIO_SELECT_FILE");?></option>
                    	<option value="1" <?php echo ($this->styleFile == 1) ? 'selected="selected"': ""; ?>><?php echo JText::_("COM_VIPPORTFOLIO_CATEGORY_LIST_LAYOUT");?></option>
                    	<option value="2" <?php echo ($this->styleFile == 2) ? 'selected="selected"': ""; ?>><?php echo JText::_("COM_VIPPORTFOLIO_LIST_LAYOUT");?></option>
                    	<option value="3" <?php echo ($this->styleFile == 3) ? 'selected="selected"': ""; ?>><?php echo JText::_("COM_VIPPORTFOLIO_LINEAL_LAYOUT");?></option>
                    </select>
    			</div>
            </div>
            <div class="clearfix"></div>
            <hr />
            <div class="clearfix"></div>
            <textarea id="vp_css_code" name="style_code" class="vp_css_code"></textarea>
                
        </fieldset>
        
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>