<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
use Joomla\CMS\Helper\ModuleHelper;
use Seebaren\Component\Tvm\Administrator\Helper\TvmHelper;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/src/Helper/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
// Import CSS
$wa =  $this->document->getWebAssetManager();
$wa->useStyle('com_tvm.admin')
    ->useScript('com_tvm.admin');
$user      = Factory::getApplication()->getIdentity();
#$userId    = $user->get('id');
#$listOrder = $this->state->get('list.ordering');
#$listDirn  = $this->state->get('list.direction');
#$canOrder  = $user->authorise('core.edit.state', 'com_tvm');
#$saveOrder = $listOrder == 'a.ordering';
?>
<form action="index.php?option=com_tvm&view=categories" method="post" id="adminForm" name="adminForm">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th ><?php echo Text::_('COM_TVM_NUM'); ?></th>
			<th >
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_CATEGORY_NAME') ;?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_EVENT_PUBLISHED'); ?>
			</th>
			<th>
				<?php echo Text::_('COM_TVM_EVENT_EDIT'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) : 
					$link = Route::_('index.php?option=com_tvm&task=categorie.edit&id=' . $row->id);				
				?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
							<?php echo $row->name; ?>
						</td>						
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'categories.', true, 'cb'); ?>
						</td>
						<td align="center">
							<a href="<?php echo $link; ?>" title="<?php echo Text::_('COM_TVM_EDIT_EVENT'); ?>"><?php echo Text::_('COM_TVM_EDIT_EVENT'); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>