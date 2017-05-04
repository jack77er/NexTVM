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
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 */
class tvmViewtvm  extends JViewLegacy
{
	/**
	 * Display the TVM View
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		/* check user rights */
		/* get access for the currently logged in user */
		$user = JFactory::getUser();
		/* check access rights from com_tvm component if the current user is a member of the trainer group */
		$this->isTrainer = false;
		if(array_search(JComponentHelper::getParams('com_tvm')->get('tvm_trainer_group'),$user->groups)){
			$this->isTrainer = true;
		}
		if($this->isTrainer){
			$this->msg = $this->get('Authorized');
		} else {
			$this->msg = $this->get('Unauthorized');
		}
			
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}
 
		// Display the view
		parent::display($tpl);
	}
}
?>