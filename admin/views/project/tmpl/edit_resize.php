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
$resize = $this->form->getGroup('resize');
?>
<?php foreach ($this->form->getGroup('resize') as $field) : ?>
	<div class="control-group">
		<?php if (!$field->hidden) : ?>
			<div class="control-label">
				<?php echo $field->label; ?>
			</div>
		<?php endif; ?>
		<div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
<?php endforeach; ?>
