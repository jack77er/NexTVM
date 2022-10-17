<?php
namespace Seebaren\Component\Tvm\Administrator\View\Tvm;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ModuleHelper;
#use Seebaeren\Component\Tvm\Administrator\Helper\TvmHelper;
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2020 John Smith. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */
/**
 * Main "Hello World" Admin View
 */
class HtmlView extends BaseHtmlView {
	protected $form = null;
	protected $templates = null;
	protected $state;
	protected $item;
	
	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->templates = $this->get('Templates');
		
		$form = $this->get('Form');
		$item = $this->get('Item');		
		$templates = $this->get('Templates');
		
		$this->form->setValue('template', NULL, '0');
		
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            //throw new GenericDataException(implode("\n", $errors), 500);
        }
        $this->addToolbar();
		
		$this->loadHelper();
        return parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$input = Factory::getApplication()->input;
 
		// Hide Joomla Administrator Main menu
		#$input->set('hidemainmenu', true);
		
		$fromTemplate = $input->get('fromTemplate','0','INT');
		
		$isNew = ($this->item->id == 0);
 
		if ($isNew)
		{
			$title = Text::_('COM_TVM_ADD_TITLE');
		}
		else
		{
			$title = Text::_('COM_TVM_EDIT_TITLE');
		}
		if($fromTemplate == '1') {
			ToolBarHelper::custom('tvm.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		} else {
			ToolBarHelper::save('tvm.save');
		}
		ToolBarHelper::title($title, 'tvm');
		
		
		ToolBarHelper::cancel(
			'tvm.cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
	}
}