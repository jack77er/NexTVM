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
 jimport('joomla.log.log');
 jimport( 'joomla.application.component.controllerform' );
/**
 * HelloWorld Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 * @since       0.0.9
 */
class TvmControllerTvm extends JControllerForm
{
	public function add() {
		JLog::add(JText::_('JTEXT_ERROR_MESSAGE ADD'), JLog::WARNING, 'jerror add');
		echo "add";
		parent::add();
	}

	
	public function edit($key = NULL, $urlVar = NULL) {
		JLog::add(JText::_('JTEXT_ERROR_MESSAGE EDIT'), JLog::WARNING, 'jerror EDIT');
		echo "edit";
		parent::edit($key, $urlVar);
	}
	
	public function save($key = null, $urlVar = null){
		
		// Finally, save the processed form data
		return parent::save($key, $urlVar);
}
	
}