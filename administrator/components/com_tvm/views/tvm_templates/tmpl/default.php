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
?>
<form action="index.php?option=com_tvm&view=tvm_templates" method="post" id="adminForm" name="adminForm">

	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th ><?php echo JText::_('COM_TVM_NUM'); ?></th>
			<th >
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_EVENT_NAME') ;?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_EVENT_DATE'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_EVENT_START'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_EVENT_DURATION'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_TVM_EVENT_LOCATION'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_TVM_EVENT_TRAINER'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_TVM_EVENT_COMMENT'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_TVM_EVENT_PUBLISHED'); ?>
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
				<?php foreach ($this->items as $i => $row) : 
				$link = JRoute::_('index.php?option=com_tvm&task=tvm.edit&id=' . $row->id);
				if($row->template == '0') continue;
				?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
							<?php echo $row->title; ?>
						</td>

						<td align="center">
							<?php echo $row->date; ?>
						</td>
						<td align="center">
							<?php echo $row->starttime; ?> Uhr
						</td>
						<td align="center">
							<?php echo $row->duration; ?> Min
						</td>
						<td align="center">
							<?php echo $row->location; ?>
						</td>
						<td align="center">
							<?php 
								$db    = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->select($db->quoteName('username'));
								$query->from($db->quoteName('#__users'));
								$query->where($db->quoteName('id').'='.$row->user_id);
								$db->setQuery($query);
								$username = $db->loadResult();
								echo $username; 
							?>
						</td>
						<td align="center">
							<?php echo $row->event_comment; ?>
						</td>
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'Tvms.', true, 'cb'); ?>
						</td>
						<td align="center">
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_TVM_EDIT_EVENT'); ?>"><?php echo JText::_('COM_TVM_EDIT_EVENT'); ?></a>
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