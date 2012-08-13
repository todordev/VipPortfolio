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
<?php if ($this->params->get('show_page_heading', 1)) { ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php } ?>

<div id="itp-vp-image-menu">
    <ul>
    <?php foreach ( $this->items as $item ) {?>
        <li class="item<?php echo $item->id;?>">
            <a href="<?php echo JRoute::_('index.php?option=com_vipportfolio&view=projects&layout=' . $this->projectLayout .'&catid=' . $item->id);?>">
            <?php echo $this->escape($item->name);?>
            </a>
        </li>
    <?php }?>
    </ul>
</div>
<div class="clr">&nbsp;</div>
    
<?php echo $this->version->backlink;?>