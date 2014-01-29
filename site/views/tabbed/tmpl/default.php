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

<?php 
if($this->modal) {
    switch($this->modal) {

        case "duncan":
            echo $this->loadTemplate("duncan");
            break;
            
        case "nivo": // Nivo modal
            echo $this->loadTemplate("nivo");
            break;
            
    }
} else {
    echo $this->loadTemplate("nomodal");
}

echo (!empty($this->event->onContentAfterDisplay) ) ? $this->event->onContentAfterDisplay : "";
?>
<div class="clearfix"></div>
<?php echo $this->version->backlink; ?>
