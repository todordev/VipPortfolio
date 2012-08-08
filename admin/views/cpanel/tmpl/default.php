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
<div id="itp-cpanel">
    <div class="itp-cpitem">
        <a rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}" href="<?php echo JRoute::_("index.php?option=com_config&amp;view=component&amp;component=com_vipportfolio&amp;path=&amp;tmpl=component");?>" class="modal">
            <img src="../media/com_vipportfolio/images/settings_48.png" alt="<?php echo JText::_("COM_VIPPORTFOLIO_SETTINGS");?>" />
            <span><?php echo JText::_("COM_VIPPORTFOLIO_SETTINGS")?></span> 
        </a>
    </div>
    <div class="itp-cpitem">
        <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&amp;view=categories");?>" >
        <img src="../media/com_vipportfolio/images/folder_48.png" alt="<?php echo JText::_("COM_VIPPORTFOLIO_CATEGORIES");?>" />
            <span><?php echo JText::_("COM_VIPPORTFOLIO_CATEGORIES")?></span> 
        </a>
    </div>
    <div class="itp-cpitem">
        <a href="<?php echo JRoute::_("index.php?option=com_vipportfolio&amp;view=projects");?>" >
        <img src="../media/com_vipportfolio/images/image_48.png" alt="<?php echo JText::_("COM_VIPPORTFOLIO_PROJECTS");?>" />
            <span><?php echo JText::_("COM_VIPPORTFOLIO_PROJECTS")?></span> 
        </a>
    </div>
</div>
<div id="itp-itprism">
    <a href="http://itprism.com/free-joomla-extensions/others/portfolio-presentation-gallery" title="<?php echo JText::_("COM_VIPPORTFOLIO");?>" target="_blank"><img src="../media/com_vipportfolio/images/vip_portfolio.png" alt="<?php echo JText::_("COM_VIPPORTFOLIO");?>" /></a>
    <a href="http://itprism.com" title="A Product of ITPrism.com"><img src="../media/com_vipportfolio/images/product_of_itprism.png" alt="A Product of ITPrism.com" /></a>
    <p><?php echo JText::_("COM_VIPPORTFOLIO_YOUR_VOTE"); ?></p>
    <p><?php echo JText::_("COM_VIPPORTFOLIO_SPONSORSHIP"); ?></p>
    <p><?php echo JText::_("COM_VIPPORTFOLIO_SUBSCRIPTION"); ?></p>
    
    <table class="table table-striped">
        <tbody>
            <tr>
                <td><?php echo JText::_("COM_VIPPORTFOLIO_INSTALLED_VERSION");?></td>
                <td><?php echo $this->version->getMediumVersion();?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_VIPPORTFOLIO_RELEASE_DATE");?></td>
                <td><?php echo $this->version->releaseDate?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_VIPPORTFOLIO_COPYRIGHT");?></td>
                <td><?php echo $this->version->copyright;?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_VIPPORTFOLIO_LICENSE");?></td>
                <td><?php echo $this->version->license;?></td>
            </tr>
        </tbody>
    </table>
    
</div>