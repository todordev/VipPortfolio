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
    $ordering   = ($this->listOrder == 'a.ordering');
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('vipportfolio.boolean', $item->published); ?>
        </td>
        <td class="center hidden-phone">
        	<img src="<?php echo $item->pic_square;?>" />
        </td>
		<td>
			<a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&view=tabs&pid=". $item->page_id);?>" ><?php echo $item->title; ?></a>
		</td>
		<td class="center hidden-phone">
			<a href="<?php echo $item->page_url;?>" target="_blank"><?php echo JText::_("COM_VIPPORTFOLIO_LINK_TO_PAGE"); ?></a>
		</td>
		<td class="center hidden-phone">
			<?php echo $item->fans; ?>
		</td>
        <td class="center hidden-phone">
            <?php echo $item->id;?>
        </td>
	</tr>
<?php } ?>
	  