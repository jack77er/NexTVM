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
 * HelloWorld component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
abstract class TvmHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @return Bool
	 */
 
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_TVM_SUBMENU_EVENTS'),
			'index.php?option=com_tvm',
			$submenu == 'entries'
		);
 
		JSubMenuHelper::addEntry(
			JText::_('COM_TVM_SUBMENU_ENTRIES'),
			'index.php?option=com_tvm&view=tvm_entries',
			$submenu == 'entries'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_TVM_SUBMENU_TEMPLATES'),
			'index.php?option=com_tvm&view=tvm_templates',
			$submenu == 'entries'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_TVM_SUBMENU_CATEGORIES'),
			'index.php?option=com_tvm&view=tvm_categories',
			$submenu == 'entries'
		);
 
		// Set some global property
		$document = JFactory::getDocument();
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION_CATEGORIES'));
		}
	}
	
	public static function getUserByEvent($event_id = 0) {
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
 
		$query->select($db->quoteName(array('b.username','a.id','a.user_id','a.state','a.acknowledged','a.comment','a.created','a.updated','a.updated_by','a.published')));
        $query->from($db->quoteName('#__tvm_entry','a'));
		$query->join('INNER',$db->quoteName('#__users','b') . ' ON (' . $db->quoteName('a.user_id') . ' = ' . $db->quoteName('b.id').')');
		
		$query->where($db->quoteName('a.event_id').' = '.$db->quote($event_id));
		$query->order($db->quoteName('a.created') . ' ASC');
		
		$db->setQuery($query);
		
		// Load the row.
		$result = $db->loadObjectList();
		// Return the Hello
		return $result;	
	}
	
	private static function getEventsBefore($d) {
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
 
		$query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__tvm_events'));		
		$query->where($db->quoteName('date').' < '.$db->quote($d).' AND '.$db->quoteName('template').' = '.$db->quote('0').' AND '.$db->quoteName('periodic').' = '.$db->quote('none'));
		$query->order($db->quoteName('date') . ' ASC');
		
		$db->setQuery($query);
		
		// Load the row.
		$result = $db->loadObjectList();
		return $result;	
		
	}
	
	public static function deleteEntriesBeforeDate($d) {
		$events = TvmHelper::getEventsBefore($d);
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
		
		$conditions = array();
		
		if(count($events) == 0) {
			return;
		}
		foreach($events as $event) {
			array_push($conditions, $db->quoteName('event_id') . ' = '. $db->quote($event->id));
		}
		
		var_dump($conditions);
		 
		$query->delete($db->quoteName('#__tvm_entry'));
		$query->where($conditions, ' OR ');
		 
		$db->setQuery($query);
		 
		$result = $db->execute();
	}
	
	public static function getPeriodicName($val = 'none'){
		switch($val) {
			case 'none': return JText::_('COM_TVM_PERIODIC_NONE'); break;
			case 'daily': return JText::_('COM_TVM_PERIODIC_DAILY'); break;
			case 'weekly': return JText::_('COM_TVM_PERIODIC_WEEKLY'); break;
			case 'monthly': return JText::_('COM_TVM_PERIODIC_MONTHLY'); break;
			case 'yearly': return JText::_('COM_TVM_PERIODIC_YEARLY'); break;
		}
	}
}