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
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td class="center">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
		<td >
			<a href="index.php?option=com_vipportfolio&amp;view=project&amp;layout=edit&amp;id=<?php echo $item->id;?>" ><?php echo $item->title; ?></a>
		</td>
		<td width="150" align="center">
		   <?php 
		   if(!empty($item->category_name)) {
		   ?>
           <a href="index.php?option=com_vipportfolio&amp;view=category&amp;layout=edit&amp;id=<?php echo $item->catid;?>" ><?php echo $item->category_name; ?></a>
           <?php } else {
               echo JText::_("COM_VIPPORTFOLIO_UNCATEGORISED");
		   }?>
        </td>
        <td width="300">
			<?php echo $item->url; ?>
		</td>
		<td class="order">
		 <?php
            if($this->saveOrder) {
                if ($this->listDirn == 'asc') {?>
                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'projects.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'projects.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                <?php } elseif ($this->listDirn == 'desc') {?>
                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'projects.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'projects.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                <?php } 
            }
            $disabled = $this->saveOrder ?  '' : 'disabled="disabled"';?>
            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
        </td>
        <td align="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "projects."); ?>
        </td>
        <td align="center">
            <?php echo $item->id;?>
        </td>
	</tr>
<?php }?>
	  