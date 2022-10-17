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
use Joomla\CMS\Factory;
use Seebaren\Component\Tvm\Administrator\Helper\TvmHelper;
use Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;
#JHtml::_('behavior.tooltip');
#JHtml::_('behavior.formvalidation'); 
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
	
	
$input = Factory::getApplication()->input;
$selection = $input->get('id','0','INT');
?>
<form action="<?php echo Route::_('index.php?option=com_tvm&view=entrie&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend>
			<?php 
				if((int) $this->item->id > 0){
					echo Text::_('COM_TVM_EDIT_TITLE').': '. (int) $this->item->id; 
				} else {
					echo Text::_('COM_TVM_ADD_TITLE'); 
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
    <input type="hidden" name="task" value="entrie.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>