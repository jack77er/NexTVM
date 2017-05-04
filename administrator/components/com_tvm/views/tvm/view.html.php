<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * HelloWorld View
 *
 * @since  0.0.1
 */
class TvmViewTvm extends JViewLegacy
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;
	protected $templates = null;
 
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
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
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
 
 
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display($tpl);
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
		$input = JFactory::getApplication()->input;
 
		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);
		
		$fromTemplate = $input->get('fromTemplate','0','INT');
		
		$isNew = ($this->item->id == 0);
 
		if ($isNew)
		{
			$title = JText::_('COM_TVM_ADD_TITLE');
		}
		else
		{
			$title = JText::_('COM_TVM_EDIT_TITLE');
		}
		if($fromTemplate == '1') {
			JToolBarHelper::custom('tvm.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		} else {
			JToolBarHelper::save('tvm.save');
		}
		JToolBarHelper::title($title, 'tvm');
		
		
		JToolBarHelper::cancel(
			'tvm.cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
	}
}