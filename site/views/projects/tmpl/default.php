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

<?php if($this->params->get("catDesc")) {?>
<?php   if(!empty($this->category)) { echo $this->category->desc; }; ?>
<?php }?>

<?php 

if($this->modal) {
    switch($this->modal) {
        case "slimbox":
            echo $this->loadTemplate("slimbox");
            break;
            
        case "native": // Native modal
            echo $this->loadTemplate("nativemodal");
            break;
    }
} else {
    echo $this->loadTemplate("nomodal");
}

?>
<div class="pagination">

    <?php if ($this->params->def('show_pagination_results', 1)) : ?>
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
    <?php endif; ?>

    <?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php echo $this->version->backlink;?>