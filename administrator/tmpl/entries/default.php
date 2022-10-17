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
use Joomla\CMS\Language\Associations;
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/src/Helper/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
// Import CSS
$wa =  $this->document->getWebAssetManager();
$wa->useStyle('com_tvm.admin')
    ->useScript('com_tvm.admin');
$wa->useScript('table.columns');	

#$assoc = Associations::isEnabled();

#$user      = Factory::getApplication()->getIdentity();
#$userId    = $user->get('id');
#$listOrder = $this->state->get('list.ordering');
#$listDirn  = $this->state->get('list.direction');
#$canOrder  = $user->authorise('core.edit.state', 'com_tvm');
#$saveOrder = $listOrder == 'a.ordering';

$input = Factory::getApplication()->input;
#$selection = $input->get('com_tvm_events_select','0','INT');


?>

<form action="index.php?option=com_tvm&view=entries" method="post" id="adminForm" name="adminForm">
<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th ><?php echo Text::_('COM_TVM_NUM'); ?></th>
			<th >
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_ENTRY_EVENT') ;?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_ENTRY_USER_NAME') ;?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_ENTRY_STATE'); ?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_ENTRY_ACKNOWLEDGED'); ?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_ENTRY_COMMENT'); ?>
			</th>
			<th>
				<?php echo Text::_('COM_TVM_ENTRY_CREATED'); ?>
			</th>
			<th>
				<?php echo Text::_('COM_TVM_ENTRY_EDITED'); ?>
			</th>
			<th>
				<?php echo Text::_('COM_TVM_ENTRY_EDITED_BY'); ?>
			</th>
			<th >
				<?php echo Text::_('COM_TVM_ENTRY_PUBLISHED'); ?>
			</th>
			<th>
				<?php echo Text::_('COM_TVM_EVENT_EDIT'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $user) : 
				$link = Route::_('index.php?option=com_tvm&view=entrie&task=entrie.edit&id=' . $user->id);
				?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $user->id); ?>
						</td>
						<td align="center">
							<?php 
								if ($user->event_title == null) {
									echo Text::_('COM_TVM_ENTRY_UNKNOWN');
								} else {
									echo $user->event_title;
									#echo "Test";
								}
							?> 
						</td>
						<td>
							<?php echo $user->user_name; ?>
						</td>
						<td align="center">
							<?php 
								switch($user->state){
									case '1':
										echo Text::_('COM_TVM_GENERAL_YES');
										break;
									case '2':
										echo Text::_('COM_TVM_GENERAL_MAYBE');
										break;
									case '3':
										echo Text::_('COM_TVM_GENERAL_NO');
										break;
									case '4':
										echo Text::_('COM_TVM_GENERAL_FORCED_NO');
										break;
									default:
										echo '-';
										break;
								}
							?>
						</td>
						<td align="center">
							<?php echo ($user->acknowledged == '1' ? Text::_('COM_TVM_GENERAL_YES') : Text::_('COM_TVM_GENERAL_NO')); ?> 
						</td>
						<td align="center">
							<?php echo $user->comment; ?> 
						</td>
						<td align="center">
							<?php echo $user->created; ?>
						</td>
						<td align="center">
							<?php echo $user->updated; ?>
						</td>
						<td align="center">
							<?php 
							if($user->updated_by != NULL) {
								echo $user->updated_by_name; 
							} else {
								echo '-';
							}
							?>
						</td>
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $user->published, $i, 'Entries.', true, 'cb'); ?>
						</td>
						<td align="center">
							<a href="<?php echo $link; ?>" title="<?php echo Text::_('COM_TVM_ENTRY_EDIT'); ?>"><?php echo Text::_('COM_TVM_ENTRY_EDIT'); ?></a>
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
