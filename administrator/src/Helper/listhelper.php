<?php
/**
 * @version    CVS: 4.0.0
 * @package    Com_Tvm
 * @author     Jacob Maxa <jacob.maxa@gmail.com>
 * @copyright  Jacob Maxa
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Language\Text;

/**
 * Tvm Listhelper.
 *
 * @since  4.0.0
 */
abstract class JHtmlListhelper
{
	static function toggle($value = 0, $view, $field, $i)
	{
		$states = array(
			0 => array('icon-unpublish', Text::_('Toggle'), ''),
			1 => array('icon-publish', Text::_('Toggle'), '')
		);

		$state  = ArrayHelper::getValue($states, (int) $value, $states[0]);
		$text   = '<span aria-hidden="true" class="' . $state[0] . '"></span>';
		$html   = '<a href="javascript:void(0);" class="tbody-icon ' . $state[2] . '"';
		$html  .= 'onclick="return Joomla.toggleField(\'cb'.$i.'\',\'' . $view . '.toggle\',\'' . $field . '\')" title="' . Text::_($state[1]) . '">' . $text . '</a>';

		return $html;
	}
}
