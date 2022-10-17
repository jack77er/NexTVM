<?php
/**
 * @package     Ffmap.Administrator
 * @subpackage  com_ffmap
 *
 * @copyright   (C) 2021 Clifford E Ford
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Seebaren\Component\Tvm\Administrator\Table;
\defined('_JEXEC') or die;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
/**
 * Featured Table class.
 *
 * @since  1.6
 */
class CategoriesTable extends Table
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__tvm_categories', 'id', $db);
	}
}