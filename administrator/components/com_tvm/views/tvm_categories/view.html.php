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
 * HelloWorlds View
 *
 * @since  0.0.1
 */
class TvmViewTvm_categories extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		$app = JFactory::getApplication();
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
	 
		 
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
		// Set the submenu
		TvmHelper::addSubmenu('tvms');
		// Set the toolbar
		$this->addToolBar();
		
		// Display the template
		parent::display($tpl);
		
		$this->setDocument();
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
		$input->set('hidemainmenu', false);

		JToolBarHelper::title(JText::_('COM_TVM_ADMIN_TITLE'), 'tvm');
		JToolBarHelper::addNew('tvm_category.add');
		JToolBarHelper::editList('tvm_category.edit');
		JToolBarHelper::deleteList('','tvm_categories.delete');
		JToolBarHelper::preferences('com_tvm');
	}
	
	  protected function setDocument() {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_TVM_ADMIN_TITLE'));
    }
	
	
}
?>