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

<?php 
$classes   = array("pull-center");
if($this->params->get("tabbed_display_tip", 0)) {
    $classes[] = "hasTooltip";
}

echo JHtml::_('bootstrap.startTabSet', 'js-vpcom', array('active' => $this->activeTab));

$i = 1;
foreach ( $this->items as $item ) {
    
    echo JHtml::_('bootstrap.addTab', "js-vpcom", $item->alias, $item->title); 
    
    if (isset($this->projects[$item->id])) {?>

       <ul class="thumbnails">
        
       <?php foreach ($this->projects[$item->id] as $project) {
           $projectDescriptionClean = JString::trim(strip_tags($project['description']));
           if(!empty($projectDescriptionClean) AND $this->params->get("tabbed_desc_max_charts", 0)) {
                $projectDescriptionClean = JHtmlString::truncate($projectDescriptionClean, $this->params->get("tabbed_desc_max_charts"));
           }
           
           if($this->params->get("tabbed_title_max_charts", 0)) {
               $project['title'] = JHtmlString::truncate($project['title'], $this->params->get("tabbed_title_max_charts"));
           }
       ?>
       
           <?php if($project['thumb']) {?> 
            <li class="span4">
            <div class="thumbnail">
            	<a href="<?php echo $this->imagesUrl.$project['image'];?>" <?php echo $this->openLink;?> class="<?php echo $this->modalClass; ?>" data-lightbox-gallery="com-list-nivo-gallery<?php echo $item->id;?>">
                    <img src="<?php echo $this->imagesUrl.$project['thumb'];?>" alt="<?php echo $this->escape($project['title']);?>"
    	            <?php if($this->params->get("tabbed_display_tip", 0)){?> 
    	            title="<?php echo JHtml::tooltipText($this->escape($project['title']) ."::". $this->escape($projectDescriptionClean)); ?>"
    	            <?php } ?>
    	            class="<?php echo implode(" ", $classes);?>"
    	            />
                </a>
                
                 <?php if($this->params->get("tabbed_dislpay_title", 0)) {?>
                 <h3>
                     <?php if( $this->params->get("tabbed_title_linkable") AND !empty($project['url']) ) { ?>
    		         <a href="<?php echo $project['url'];?>" <?php echo $this->openLink;?>><?php echo $this->escape($project['title']);?></a>
    		         <?php }else{?>
    		         <?php echo $this->escape($project['title']);?>
    		         <?php }?>
                 </h3>
                 <?php }?>
            
                <?php if($this->params->get("tabbed_dislpay_description", 0) AND !empty($projectDescriptionClean)) {?>
                    <p><?php echo $this->escape($projectDescriptionClean);?></p>
                <?php }?>
                
                <?php if($this->params->get("tabbed_dislpay_url", 0) AND !empty($project['url'])) {?>
                    <a href="<?php echo $project['url'];?>" <?php echo $this->openLink;?>><?php echo $project['url'];?></a>
                <?php }?>
            
            </div>
            
          </li>
    <?php }  // if($project['thumb']) { ?>
    
    <?php } // foreach ($this->projects[$item->id] ... ?>
    
    </ul>
    <?php echo JHtml::_('bootstrap.endTab');?> 
    <div class="clearfix"></div>
	<?php } // if (isset($this->projects[$item->id])) { ?>

<?php } ?>
<?php echo JHtml::_('bootstrap.endTabSet');?>
