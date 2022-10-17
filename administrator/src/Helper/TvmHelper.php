<?php
/**
 * @version    CVS: 4.0.0
 * @package    Com_Tvm
 * @author     Jacob Maxa <jacob.maxa@gmail.com>
 * @copyright  Jacob Maxa
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

namespace Seebaren\Component\Tvm\Administrator\Helper;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Object\CMSObject;

/**
 * Tvm helper.
 *
 * @since  4.0.0
 */
class TvmHelper
{
	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  CMSObject
	 *
	 * @since   4.0.0
	 */
	public static function getActions()
	{
		$user = Factory::getApplication()->getIdentity();
		$result = new CMSObject;

		$assetName = 'com_tvm';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
	
	
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
		$document = Factory::getDocument();
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION_CATEGORIES'));
		}
	}
	
	public static function getUserByEvent($event_id = 0) {
		// Obtain a database connection
		$db = Factory::getDbo();
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
		$db = Factory::getDbo();
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
		$db = Factory::getDbo();
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
			case 'none': return Text::_('COM_TVM_PERIODIC_NONE'); break;
			case 'daily': return Text::_('COM_TVM_PERIODIC_DAILY'); break;
			case 'weekly': return Text::_('COM_TVM_PERIODIC_WEEKLY'); break;
			case 'monthly': return Text::_('COM_TVM_PERIODIC_MONTHLY'); break;
			case 'yearly': return Text::_('COM_TVM_PERIODIC_YEARLY'); break;
		}
	}
}

