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
    
<div class="itp-vp-box">
<?php
jimport ('joomla.html.pane');

if ($this->params->get("ctabsDisplayTips")){
    // Loads the behaviors
    JHTML::_('behavior.framework');
    JHTML::_('behavior.tooltip');
}

$pane = JPane::getInstance('Tabs');
echo $pane->startPane('optionsPane');
{

?>
<?php foreach ( $this->items as $item ) {?>
    <?php 
    $id = JString::str_ireplace(" ", "_",JString::strtolower($item->name)).rand(1,500);
    
    echo $pane->startPanel($item->name, $id);?>
    
    <?php
    if (isset($this->projects[$item->id])) {
        foreach ($this->projects[$item->id] as $project) {?>
           <?php if($project['thumb']) {?> 
            <div class="vp-ti">
            <?php 
            // Initialize modal dialog
            if(!$this->modal) { ?>
            	<a href="<?php echo $this->params->get("images_directory", "images/vipportfolio") . "/".$project['image'];?>" >
            <?php }else{
                
                if("slimbox" == $this->modal ) { // Slimbox
                    echo '<a href="'.  $this->params->get("images_directory", "images/vipportfolio") . "/".$project['image'].'" rel="lightbox-item'.$item->id.'" >';
                }else { // Native
                    echo '<a href="'.  $this->params->get("images_directory", "images/vipportfolio") . "/".$project['image'].'" class="vip-modal" rel="{handler: \'image\'}" >';
                }
            }
            ?>
            <img src="<?php echo $this->params->get("images_directory", "images/vipportfolio") . "/".$project['thumb'];?>" alt="<?php echo $this->escape($project['title']);?>"
	            <?php if($this->params->get("ctabsDisplayTips")){?> 
	            class="hasTip" 
	            title="<?php echo $this->escape(strip_tags($project['title']));?> :: <?php echo $this->escape(strip_tags($project['description']));?>"
	            <?php }?>
	            width="<?php echo $this->params->get("ctabs_thumb_width");?>" 
                height="<?php echo $this->params->get("ctabs_thumb_height");?>" 
	            />
            </a>
            
            <?php if($this->params->get("ctabsDisplayTitle")) {?>
            <h3>
             <?php if( $this->params->get("ctabsTitleLinkable") AND $project['url'] ) { ?>
		         <a href="<?php echo $project['url'];?>"><?php echo $this->escape($project['title']);?></a>
		     <?php }else{?>
		         <?php echo $this->escape($project['title']);?>
		     <?php }?>
            </h3>
            <?php }?>
            
            <?php if($this->params->get("ctabsDisplayInner")) {?>
            <div>
            <?php if($this->params->get("ctabsDisplayInnerTitle")){?>
            <h3>
	            <?php if($this->params->get("ctabsInnerURL")){?>
	            <a href="<?php echo $project['url'];?>" >
	            <?php }?>
    	            <?php  
    	            $length = $this->params->get("ctabsInnerTitleMaxChars");
    	            if($length < JString::strlen($project['title'])) {
    	               echo $this->escape(JString::substr($project['title'],0, $length))."...";
    	            } else {
    	               echo $this->escape($project['title']);
    	            }
    	            ?>
	            <?php if($this->params->get("ctabsInnerURL")){?></a><?php }?>
	            </h3>
	            <?php }?>
	            <p><?php
	            $desc = $this->escape(strip_tags($project['description'])); 
	            $length = $this->params->get("ctabsInnerMaxChars");
                echo ($length < JString::strlen($desc)) ? JString::substr($desc,0, $length)."..." : $desc;
	            ?></p>
            </div>
            <?php }?>
            </div>
            <?php }?>
    <?php }
    }?>
    
	<?php echo $pane->endPanel(); ?>
<?php } ?>
    
<?php 
}
echo $pane->endPane();?>
</div>
    
<?php echo $this->version->backlink;?>