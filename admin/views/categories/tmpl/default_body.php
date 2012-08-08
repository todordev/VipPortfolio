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
<?php foreach ($this->items as $i => $item) {
    $ordering   = ($this->listOrder == 'a.ordering');
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="center">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
		<td >
			<a href="index.php?option=com_vipportfolio&amp;view=category&amp;layout=edit&amp;id=<?php echo $item->id;?>" ><?php echo $item->name; ?></a>
		</td>
		<td class="center">
			( <?php echo JArrayHelper::getValue($this->numbers, $item->id, 0); ?> )
		</td>
		<td class="order">
            <?php
            $disabled = $this->saveOrder ?  '' : 'disabled="disabled"';
            if($this->saveOrder) {
                if ($this->listDirn == 'asc') {
                    $showOrderUpIcon = (isset($this->items[$i-1]) AND (!empty($this->items[$i-1]->ordering)) AND ( $item->ordering >= $this->items[$i-1]->ordering )) ;
                    $showOrderDownIcon = (isset($this->items[$i+1]) AND ($item->ordering <= $this->items[$i+1]->ordering));
                ?>
                    <span><?php echo $this->pagination->orderUpIcon($i, $showOrderUpIcon, 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, $showOrderDownIcon, 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                <?php } elseif ($this->listDirn == 'desc') {
                    $showOrderUpIcon = (isset($this->items[$i-1]) AND ($item->ordering <= $this->items[$i-1]->ordering));
                    $showOrderDownIcon = (isset($this->items[$i+1]) AND (!empty($this->items[$i+1]->ordering)) AND ($item->ordering >= $this->items[$i+1]->ordering)); 
                ?>
                    <span><?php echo $this->pagination->orderUpIcon($i, $showOrderUpIcon, 'categories.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, $showOrderDownIcon, 'categories.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                <?php } 
            }?>
            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
        </td>
        <td align="center">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, "categories."); ?>
        </td>
        <td align="center">
            <?php echo $item->id;?>
        </td>
	</tr>
<?php } ?>
	  