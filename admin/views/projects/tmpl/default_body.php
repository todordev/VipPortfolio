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
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	    
        $disableClassName = '';
		$disabledLabel	  = '';
		if (!$this->saveOrder) {
			$disabledLabel    = JText::_('JORDERINGDISABLED');
			$disableClassName = 'inactive tip-top';
		}
		
	?>
	<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
		<td class="order nowrap center hidden-phone">
    		<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
    			<i class="icon-menu"></i>
    		</span>
    		<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
    	</td>
		<td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "projects."); ?>
        </td>
        
		<td>
			<a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&view=project&layout=edit&id=".(int)$item->id); ?>" ><?php echo $item->title; ?></a>
		</td>
		<td width="10%" class="center nowrap hidden-phone">
		   <?php echo (!empty($item->category)) ? $item->category : JText::_("COM_VIPPORTFOLIO_UNCATEGORISED") ?>
        </td>
        <td width="20%" class="center hidden-phone"><?php echo JHtmlString::truncate($item->url, 64); ?></td>
        <td class="center hidden-phone"><?php echo (int)$item->id;?></td>
	</tr>
<?php }?>
	  