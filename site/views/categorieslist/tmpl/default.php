<?php
/**
 * @package      VipPortfolio
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php if ($this->params->get('show_page_heading', 1)) { ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php } ?>

<?php echo (!empty($this->event->onContentBeforeDisplay) ) ? $this->event->onContentBeforeDisplay : "";?>

<?php if(!empty($this->items)){?>
<ul class="thumbnails">
    
    <?php foreach ( $this->items as $item ) {?>
    <li class="span4">
        <a class="thumbnail js-vpcom-bwimage" href="<?php echo JRoute::_('index.php?option=com_vipportfolio&view=' . $this->projectsView .'&id=' . $item->id. $this->tmpl);?>">
            <?php if ( !empty( $item->image ) ) {?>
                <img src="<?php echo JURI::root().$item->image;?>" alt="<?php echo $this->escape($item->title);?>" />
            <?php } else {?>
                <?php echo $item->name;?>
            <?php }?>
        </a>
    </li> 
    <?php }?>
</ul>
<?php echo (!empty($this->event->onContentAfterDisplay) ) ? $this->event->onContentAfterDisplay : "";?>

<div class="clearfix">&nbsp;</div>
<div class="pagination">

    <?php if ($this->params->def('show_pagination_results', 1)) : ?>
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
    <?php endif; ?>

    <?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php }?>
