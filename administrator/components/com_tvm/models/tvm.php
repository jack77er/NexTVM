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
 
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class TvmModelTvm extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Tvm', $prefix = 'TvmTable', $config = array())
	{
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getTemplates(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__tvm_events'));
		$query->where($db->quoteName('template') . ' = '. $db->quote('1'));
		$db->setQuery($query);
		return $db->loadObjectList();		
	}
	
	public function save($data) {
		$dayofweek = '';
		switch($data["periodic"]){
			case 'none':
				break;
			case 'daily':
				break;
			case 'weekly':
				$parsedDate = date_parse($data["date"]);
				$dayofweek = date( "w", strtotime($data["date"]));
				$data["periodic_value"] = $dayofweek;
			case 'monthly':
				break;
			case 'yearly';
				break;
			default:
				break;
		}
		return parent::save($data);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		// Get the form.
		$form = $this->loadForm(
			'com_tvm.tvm',
			'tvm',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);
 
		if (empty($form))
		{
			return false;
		}
 
		return $form;
	}
 
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_tvm.edit.tvm.data',
			array()
		);
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}
	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__tvm_events');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
}