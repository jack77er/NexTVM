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
?>

<form action="<?php echo JRoute::_('index.php?option=com_tvm&view=tvm_entries&layout=edit&id=' . (int) $this->item->id); ?>"
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
    <input type="hidden" name="task" value="tvm_entry.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>
