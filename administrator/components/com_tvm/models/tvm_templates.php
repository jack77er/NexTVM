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
 * HelloWorldList Model
 *
 * @since  0.0.1
 */
class TvmModelTvm_templates extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
 
		// Create the base select statement.
		/*$query->select($db->quoteName(array('b.username','a.id','a.date','a.duration','a.title','a.max_users','a.deadline','a.location','a.comment','a.published')));
        $query->from($db->quoteName('#__tvm_events','a'));
		$query->join('INNER',$db->quoteName('#__users','b') . ' ON (' . $db->quoteName('a.user_id') . ' = ' . $db->quoteName('b.id') . ')');
		return $query;*/
		$query->select('*');
        $query->from($db->quoteName('#__tvm_events'));
		return $query;
	}
}
?>