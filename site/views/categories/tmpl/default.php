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
defined('_JEXEC') or die;?>
<div id="itp-vp<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <div id="itp-vp-box">
        <?php foreach ( $this->items as $item ) {?>
            <a href="<?php echo JRoute::_('index.php?option=com_vipportfolio&amp;view=projects&amp;layout=' . $this->projectLayout .'&amp;catid=' . $item->id);?>">
            <?php if ( !empty( $item->image ) ) {?>
                <img 
                width="<?php echo $this->params->get("clistThumbWidth", 350); ?>" 
                height="<?php echo $this->params->get("clistThumbHeight", 100); ?>" src="<?php echo JURI::root() . "media/vipportfolio/" . $item->image;?>" alt="<?php echo $item->name;?>" title="<?php echo $item->name;?>" />
            <?php } else {?>
                <?php echo $item->name;?>
            <?php }?>
            </a>
        <?php } ?>
    </div>
    <div class="clear">&nbsp;</div>
    <div class="pagination">
    
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>
    
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
</div>
<?php echo $this->version->url;?>