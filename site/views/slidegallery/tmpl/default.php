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
<?php if ($this->params->get('show_page_heading', 1)) { ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php } ?>

<?php if($this->params->get("catDesc")) {?>
<?php   if(!empty($this->category)) { echo $this->category->desc; }; ?>
<?php }?>

<?php if(!empty($this->items)) {?>
<div id="slideshow">    
      <span id="loading"><?php echo JText::_("COM_VIPPORTFOLIO_LOADING");?></span>
    
      <ul id="pictures">
        <?php foreach ( $this->items as $item ) {?>
        <li><img src="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$item->image;?>" alt="<?php echo $item->title;?>" title="<?php echo $item->title;?>" /></li>
        <?php }?> 
      </ul>
      
      <ul id="menu">
      	<?php foreach ( $this->items as $item ) {?>
        <li><a href="<?php echo JURI::root().$this->params->get("images_directory", "images/vipportfolio") . "/".$item->image;?>"><?php echo $item->title;?></a></li>
        <?php }?>
      </ul>
</div>
<?php }?>
<?php echo $this->version->backlink; ?>