<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 
 
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation'); 
$input = JFactory::getApplication()->input;
$selection = $input->get('id','0','INT');
?>
<form action="index.php?option=com_tvm&view=tvm&layout=edit" method="post" id="TemplateSelectForm" name="TemplateSelectForm">
	<input type="hidden" name="fromTemplate" value="1" />
 <div class="form-horizontal">
        <fieldset class="adminform">
            <legend>
				<?php echo JText::_('COM_TVM_ENTRY_EDIT_FIELD_FROM_TEMPLATE'); ?>
			</legend>
            <div class="row-fluid">
                <div class="span6">
                        <div class="control-group">
                            <div class="control-label"><?php echo JText::_('COM_TVM_ADD_FIELD_TEMPLATE'); ?></div>
                            <div class="controls">
								<select name="id" width="45"  onchange="this.form.submit()">
									<option><?php echo JText::_('COM_TVM_EVENTS_PLEASE_CHOOSE'); ?></option>
									<?php if (!empty($this->templates)) : ?>
										<?php foreach ($this->templates as $i => $template) : ?>
											<option value="<?php echo $template->id; ?>" <?php echo ($selection == $template->id ? 'selected' : '')?>><?php echo $template->title.' - '.$template->location; ?></option>
														
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
                        </div>
                </div>
            </div>
        </fieldset>
    </div>
</form>

<form action="<?php echo JRoute::_('index.php?option=com_tvm&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend>
			<?php 
				if((int) $this->item->id > 0){
					echo JText::_('COM_TVM_EDIT_TITLE').': '. (int) $this->item->id; 
				} else {
					echo JText::_('COM_TVM_ADD_TITLE'); 
				}
			?>
			</legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php foreach ($this->form->getFieldset() as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls">
								<?php 
									echo $field->input;
								?>
							</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="tvm.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>
