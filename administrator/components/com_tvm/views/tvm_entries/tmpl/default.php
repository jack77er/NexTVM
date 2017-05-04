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

$input = JFactory::getApplication()->input;
$selection = $input->get('com_tvm_events_select','0','INT');
	
?>
<form action="index.php?option=com_tvm&view=tvm_entries" method="post" id="PreAdminForm" name="PreAdminForm">
<select name="com_tvm_events_select" width="45"  onchange="this.form.submit()">
	<option><?php echo JText::_('COM_TVM_EVENTS_PLEASE_CHOOSE'); ?></option>
	<?php if (!empty($this->items)) : ?>
		<?php foreach ($this->items as $i => $row) : ?>
			<option value="<?php echo $row->id; ?>" <?php echo ($selection == $row->id ? 'selected' : '')?>><?php echo $row->title.' '.$row->date.' '.$row->starttime; ?></option>
						
		<?php endforeach; ?>
	<?php endif; ?>
</select>
</form>
<?php
	
	/* user input was send using HTTP POST */
	if($selection > 0) :
	
	$users = TvmHelper::getUserByEvent($selection);
?>


<form action="index.php?option=com_tvm&view=tvm_entries" method="post" id="adminForm" name="adminForm">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th ><?php echo JText::_('COM_TVM_NUM'); ?></th>
			<th >
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_ENTRY_USER_NAME') ;?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_ENTRY_STATE'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_ENTRY_ACKNOWLEDGED'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_ENTRY_COMMENT'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_TVM_ENTRY_CREATED'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_TVM_ENTRY_EDITED'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_TVM_ENTRY_EDITED_BY'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_ENTRY_PUBLISHED'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_TVM_EVENT_EDIT'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($users as $i => $user) : 
				$link = JRoute::_('index.php?option=com_tvm&view=tvm_entry&task=tvm_entry.edit&id=' . $user->id);
				?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $user->id); ?>
						</td>
						<td>
							<?php echo $user->username; ?>
						</td>
						<td align="center">
							<?php 
								switch($user->state){
									case '1':
										echo JText::_('COM_TVM_GENERAL_YES');
										break;
									case '2':
										echo JText::_('COM_TVM_GENERAL_MAYBE');
										break;
									case '3':
										echo JText::_('COM_TVM_GENERAL_NO');
										break;
									case '4':
										echo JText::_('COM_TVM_GENERAL_FORCED_NO');
										break;
									default:
										echo '-';
										break;
								}
							?>
						</td>
						<td align="center">
							<?php echo ($user->acknowledged == '1' ? JText::_('COM_TVM_GENERAL_YES') : JText::_('COM_TVM_GENERAL_NO')); ?> 
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
								$db    = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->select($db->quoteName('username'));
								$query->from($db->quoteName('#__users'));
								$query->where($db->quoteName('id').'='.$user->updated_by);
								$db->setQuery($query);
								$username = $db->loadResult();
								echo $username; 
							} else {
								echo '-';
							}
							?>
						</td>
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $user->published, $i, 'Tvm_entries.', true, 'cb'); ?>
						</td>
						<td align="center">
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_TVM_ENTRY_EDIT'); ?>"><?php echo JText::_('COM_TVM_ENTRY_EDIT'); ?></a>
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

<?php endif; // selection = '0' ? ?> 
