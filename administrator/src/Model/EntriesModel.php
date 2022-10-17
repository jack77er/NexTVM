<?php
/**
 * @version    CVS: 4.0.0
 * @package    Com_Tvm
 * @author     Jacob Maxa <jacob.maxa@gmail.com>
 * @copyright  Jacob Maxa
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

namespace Seebaren\Component\Tvm\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Associations;

use Seebaren\Component\Tvm\Administrator\Helper\TvmHelper;

/**
 * Methods supporting a list of Templates records.
 *
 * @since  4.0.0
 */
class EntriesModel extends ListModel {
		/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JControllerLegacy
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'event',
				'id','a.id',
				'event_id','a.event_id',
				'user_id','a.user_id',
				'state','a.state',
				'published','a.published',
				'comment','a.comment',
				'created','a.created',
				'update','a.update',
			];

			#$assoc = Associations::isEnabled();

			#if ($assoc) {
			#	$config['filter_fields'][] = 'association';
			#}
		}
		#var_dump($config);
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information., Order by "id" "ascending"
		parent::populateState("id", "ASC");
		
		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);
		
	    $app = Factory::getApplication();

	    $value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
	    $this->setState('list.limit', $value);

	    $value = $app->input->get('limitstart', 0, 'uint');
	    $this->setState('list.start', $value);
		
		// Split context into component and optional section
		#$parts = FieldsHelper::extract($context);

		#if ($parts)
		#{
		#	$this->setState('filter.component', $parts[0]);
		#	$this->setState('filter.section', $parts[1]);
		#}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   4.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		
		return parent::getStoreId($id);
		
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   4.0.0
	 */
	protected function getListQuery()
	{
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.*')
			->from($db->quoteName('#__tvm_entry','a'));
			// Join with event titles
		$query->select($db->quoteName('e.title', 'event_title'))
			->join('LEFT', $db->quoteName('#__tvm_events', 'e') . ' ON e.id = a.event_id');
			// Join with users table to get the username of the author
		$query->select($db->quoteName('u.name', 'user_name'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = a.user_id');
		$query->select($db->quoteName('u2.name', 'updated_by_name'))
			->join('LEFT', $db->quoteName('#__users', 'u2') . ' ON u2.id = a.updated_by');	
		$event_id = $this->getState('filter.event');
		if (is_numeric($event_id)) {
			$query->where('a.event_id = '.(int) $event_id);
        }		
		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		

		return $items;
	}
}
