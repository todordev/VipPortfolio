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
<div id="itp-vp-box">
<?php
jimport ('joomla.html.pane');

if ($this->params->get("ctabsDisplayTips")){
    // Loads the behaviors
    JHTML::_('behavior.framework');
    JHTML::_('behavior.tooltip');
}

$pane =& JPane::getInstance('Tabs');
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
	            
	            <?php // Start modal window 
	               if ($this->params->get("ctabModal")) { ?>
                    <a href="<?php echo JURI::root(); ?>media/vipportfolio/<?php echo $project['image'];?>" <?php echo sprintf($this->modalParams, "-item".$item->id);?> >
                <?php } else {?>
                    <a href="<?php echo JURI::root(); ?>media/vipportfolio/<?php echo $project['image'];?>" >
                <?php }?>
                   
	            <img src="<?php echo JURI::root(); ?>media/vipportfolio/<?php echo $project['thumb'];?>" alt="<?php echo $project['title'];?>"
	            <?php if($this->params->get("ctabsDisplayTips")){?> 
	            class="hasTip" 
	            title="<?php echo strip_tags($project['title']);?> :: <?php echo strip_tags($project['description']);?>"
	            <?php }?>
	            width="<?php echo $this->params->get("ctabsThumbWidth");?>" 
                height="<?php echo $this->params->get("ctabsThumbHeight");?>" 
	            />
	            
                </a>
                
	            <?php if($this->params->get("ctabsDisplayTitle")) {?>
	            <h3>
	             <?php if( $this->params->get("ctabsTitleLinkable") AND $project['url'] ) { ?>
			         <a href="<?php echo $project['url'];?>"><?php echo $project['title'];?></a>
			     <?php }else{?>
			         <?php echo $project['title'];?>
			     <?php }?>
	            </h3>
	            <?php }?>
	            
	            <?php if($this->params->get("ctabsDisplayInner")) {?></a>
	            <div>
	            <?php if($this->params->get("ctabsDisplayInnerTitle")){?>
	            <h3>
    	            <?php if($this->params->get("ctabsInnerURL")){?>
    	            <a href="<?php echo $project['url'];?>" >
    	            <?php }?>
        	            <?php  
        	            $length = $this->params->get("ctabsInnerTitleMaxChars");
        	            if($length < JString::strlen($project['title'])) {
        	               echo JString::substr($project['title'],0, $length)."...";
        	            } else {
        	               echo $project['title'];
        	            }
        	            ?>
    	            <?php if($this->params->get("ctabsInnerURL")){?></a><?php }?>
    	            </h3>
    	            <?php }?>
    	            <p><?php
    	            $desc = strip_tags($project['description']); 
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
<?php echo $this->version->url;?>