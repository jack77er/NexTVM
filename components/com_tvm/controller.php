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
 * Hello World Component Controller
 *
 * @since  0.0.1
 */
class tvmController extends JControllerLegacy
{

	public static function insert(){
		echo "test";
	}
	
	public static function checkUserAccess() {
		//echo "CheckUserAccess\r\n";
		//$username = strtolower(JRequest::getVar('user'));
		$jinput = JFactory::getApplication()->input;
		$username = $jinput->post->get('name', '', 'USERNAME');
		$password = $jinput->post->get('pass', '', 'STRING');
		
		tvmController::checkAndUpdatePeriodicEvents();
		
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true); 
		$query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('username').' = '.$db->quote($username).' OR '.$db->quoteName('email').' = '.$db->quote($username));
		//$query->setLimit('1');
		$db->setQuery($query);
		
		// Load the row.
		$userID = $db->loadAssoc()['id'];
		
		//echo $username." ID: ".$userID."\r\n";
		$userObj = JFactory::getUser($userID);
		
		$passwordMatch = JUserHelper::verifyPassword($password, $userObj->password, $userObj->id);
		if($passwordMatch) {
			$userObj->lastvisitDate = strtotime($userObj->lastvisitDate);
			echo json_encode($userObj);
		} else {
			echo '0';
		}
		//var_dump($passwordMatch);
		//echo json_encode($userObj);
		return $passwordMatch;
	}
	
	private static function updateEventDate($id = 0, $newDate, $isPeriodic = false ){
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__tvm_events'));
		if($isPeriodic) {
			$query->set(
			array(
				$db->quoteName('date'). ' = '. $db->quote($newDate),
				$db->quoteName('closed'). ' = '. $db->quote('0'))
			);
		} else {
			$query->set($db->quoteName('date'). ' = '. $db->quote($newDate));
		}
		$query->set($db->quoteName('date'). ' = '. $db->quote($newDate));
		
		$query->where($db->quoteName('id').' = '.$db->quote($id));
		$db->setQuery($query);
		$result = $db->execute();
		
	}
	
	private static function deleteEntriesFromEvent($id) {
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__tvm_entry'));
		$query->where($db->quoteName('event_id').' = '.$db->quote($id));
		$db->setQuery($query);
		
		$result = $db->execute();
	}
	
	
	
	public static function updateEventRegistrationJSON() {
		
		// read POST input values
		$jinput = JFactory::getApplication()->input;
		$userID = $jinput->post->get('id', '0', 'INTEGER');
		$eventID = $jinput->post->get('event', '0', 'INTEGER');
		$userState = $jinput->post->get('state', '0', 'INTEGER');
		$userPassword = $jinput->post->get('pass', '', 'STRING');
		$userComment= $jinput->post->get('comment', '', 'STRING');
		// return variable
		$retVal = new stdClass();
		
		$retVal->userState = $userState;
		$retVal->userComment = $userComment;
		$retVal->acknowledged = '0';
		/*
			$retVal->updateState :
			1   - new entry created
			0	- everything was fine
			-1	- event closed
			-2  - event not available
			-3  - invalid input values
		*/
		$retVal->updateState = 0;
		
		
		if($userID == NULL || $userID == '0' ) {
			echo "Access denied";
			return;
		}		
		
	
		if( JFactory::getUser($userid)->password != $password ) {
			echo "Access denied";
			return;
		}
		
		
		if (!is_numeric($eventID) || (intval($userState) > 5)) {
			$retVal->updateState = -3; 
			echo json_encode($retVal);
			return $retVal;
		}
		// first check if event is open for changes
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
	    $query->from($db->quoteName('#__tvm_events'));
		$query->where($db->quoteName('id').' = '.$db->quote($eventID).' AND '.$db->quoteName('closed').' = '.$db->quote(1));
		$db->setQuery($query);
		//Load the row.
		$DBresult = $db->loadResult();
		if($DBresult > 0) {
			// event with ID is closed --> no status changes allowed
			$retVal->updateState = -1; 
			$retVal->userState = 4; // forced NO
			echo json_encode($retVal);
			return $retVal;
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// check, if user is already registered
		$query->select(array($db->quoteName('id'),$db->quoteName('state'),$db->quoteName('acknowledged')));
	    $query->from($db->quoteName('#__tvm_entry'));
		$query->where($db->quoteName('event_id').' = '.$db->quote($eventID).' AND '.$db->quoteName('user_id').' = '.$db->quote($userID));
		$db->setQuery($query);
		//Load the row.
		$DBresult = $db->loadAssoc();
		
		$oldAcked = $DBresult['acknowledged'];
		$oldState = $DBresult['state'];
		
		//var_dump($oldAcked);
		//var_dump($oldState);
		
		if(intval($DBresult['id']) > 0){
			// gefunden --> update
			$acked = '0';
			// no change in user state, copy old ack state to update
			if($oldState == $userState) {
				$acked = $oldAcked;
			}
			
			$retVal->acknowledged = $acked;
			
			$ret = 'gefunden';
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			// Fields to update.
			$fields = array(
				$db->quoteName('state') . ' = ' . $db->quote($userState),
				$db->quoteName('comment') . ' = ' . $db->quote($userComment),
				$db->quoteName('updated') . ' = NOW()',
				$db->quoteName('updated_by') . ' = '. $db->quote($userID),
				$db->quoteName('acknowledged') . ' = '. $db->quote($acked),
			);
			 
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('user_id') . ' = '.$db->quote($userID), 
				$db->quoteName('event_id') . ' = ' . $db->quote($eventID)
			);
			$query->update($db->quoteName('#__tvm_entry'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$entryCount = $db->execute();
			
			if($entryCount == 1) {
				// update successfull
				$retVal->updateState = 0;				
			} else {
				// update failed
				$retVal->updateState = -3;				
			}
		 } else {
			// nicht gefunden --> neu anlegen
			$ret='nicht gefunden';
			// Insert columns.
			$columns = array('user_id', 'event_id', 'state', 'acknowledged', 'comment','published');
			// Insert values.
			$values = array( $db->quote($userID),  $db->quote($eventID),  $db->quote($userState), 0, $db->quote($userComment), 1);
			// Retrieve the shout
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__tvm_entry'));
			$query->columns($db->quoteName($columns));
			$query->values(implode(',', $values));
			 
			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			$entryCount = $db->execute();
			
			if($entryCount == 1) {
				// update successfull
				$retVal->updateState = 1;				
			} else {
				// update failed
				$retVal->updateState = -3;				
			}
		 }
		 
		 echo json_encode($retVal);
		 return $retVal;
	}
	
	public static function getTVMEventsJSON() {
		date_default_timezone_set("Europe/Berlin"); 
		
		$jinput = JFactory::getApplication()->input;
		$userid = $jinput->post->get('id', '', 'INTEGER');
		$password = $jinput->post->get('pass', '', 'STRING');
		$category = $jinput->post->get('category', '1', 'INTEGER');
		#echo $password."\r\n";
		#echo JFactory::getUser($userid)->password."\r\n" ;
		if($userid == NULL || $userid == '0' ) {
			echo "Access denied";
			return;
		}

		if( JFactory::getUser($userid)->password != $password ) {
			echo "Access denied";
			return;
		}
		
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
		
		// Prepare the query

		$query->select($db->quoteName(array('b.username','a.id','a.date','a.starttime','a.duration','a.title','a.max_users','a.deadline','a.location','a.event_comment','a.published', 'a.periodic','a.periodic_value', 'a.closed')));
        $query->from($db->quoteName('#__tvm_events','a'));
		$query->join('INNER',$db->quoteName('#__users','b') . ' ON (' . $db->quoteName('a.user_id') . ' = ' . $db->quoteName('b.id').')');
		#$query->where($db->quoteName('a.published').' = '.$db->quote('1').' AND '.$db->quoteName('a.date').' >= '. $db->quote(date("Y-m-d")).' AND '.$db->quoteName('a.date').' <= '. $db->quote(date("Y-m-d", time()+ (7 * 24 * 60 * 60))));
		$query->where($db->quoteName('a.published').' = '.$db->quote('1').' AND '.$db->quoteName('a.date').' >= '. $db->quote(date("Y-m-d")).' AND '.$db->quoteName('category').' = '.$db->quote($category));
		$query->order($db->quoteName('a.date') . ','. $db->quoteName('a.starttime') .' ASC');
		// 		$query->where($db->quoteName('a.published').' = '.$db->quote('1').' AND ('.$db->quoteName('a.date').' >= '. $db->quote(date("Y-m-d")).' AND '.$db->quoteName('a.date').' <= '. $db->quote(date("Y-m-d", time()+ (7 * 24 * 60 * 60))).') OR ('.$db->quoteName('a.periodic').' != '.$db->quote('none').' AND '.$db->quoteName('a.periodic_value').' != '.date('w').' )');
		$db->setQuery($query);
		
		// Load the row.
		$events = $db->loadObjectList();
		
		foreach ($events as $event) {
			$eventusers = tvmController::getTvmUserByEvent($event->id);
			$event->user_state = "0";
			$event->user_state_ack = "0";
			$event->user_comment = "";
			//echo $userid;
			//var_dump($eventusers);
			foreach ($eventusers as $euser) {
				if($euser->user_id == $userid) {
					$event->user_state = $euser->state;
					$event->user_state_ack = $euser->acknowledged;
					$event->user_comment = $euser->comment;
					//echo "TREFFER!!!!\r\n";
					break;
				}
			}
			$event->users = $eventusers;
		}
		
		
		echo json_encode($events);
		// Return the Hello
		return $events;
	}
	
	public static function getTVMCategoriesJSON() {		
		$jinput = JFactory::getApplication()->input;
		$userid = $jinput->post->get('id', '', 'INTEGER');
		$password = $jinput->post->get('pass', '', 'STRING');
		#echo $password."\r\n";
		#echo JFactory::getUser($userid)->password."\r\n" ;
		if($userid == NULL || $userid == '0' ) {
			echo "Access denied";
			return;
		}

		if( JFactory::getUser($userid)->password != $password ) {
			echo "Access denied";
			return;
		}
		
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
		
		// Prepare the query

		$query->select('*');
        $query->from($db->quoteName('#__tvm_categories'));		
		$query->where($db->quoteName('published').' = '.$db->quote('1'));
		$db->setQuery($query);		
		// Load the row.
		$events = $db->loadObjectList();				
		echo json_encode($events);
		// Return the Hello
		return $events;
	}
	
	public static function getTvmUserByEvent($event_id = 0) {
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
 
		$query->select($db->quoteName(array('b.username','a.user_id','a.state','a.acknowledged','a.comment')));
        $query->from($db->quoteName('#__tvm_entry','a'));
		$query->join('INNER',$db->quoteName('#__users','b') . ' ON (' . $db->quoteName('a.user_id') . ' = ' . $db->quoteName('b.id').')');
		$query->where($db->quoteName('a.event_id').' = '.$db->quote($event_id).' AND '.$db->quoteName('a.published').' = 1');
		$query->order($db->quoteName('a.state') . ' ASC');
		
		$db->setQuery($query);
		
		// Load the row.
		$result = $db->loadObjectList();
		// Return the Hello
		return $result;
	}
	
	public static function getTvmEventIDsOfUser($user_id = 0) {
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
 
		$query->select($db->quoteName(array('a.event_id','a.state','a.acknowledged','a.comment')));
        $query->from($db->quoteName('#__tvm_entry','a'));
		$query->where($db->quoteName('user_id').' = '.$db->quote($user_id).' AND '.$db->quoteName('a.published').' = 1');
		$db->setQuery($query);
		// Load the row.
		$result = $db->loadObjectList();
		// Return the Hello
		return $result;
	}
	
	public static function getTVMBoardPostsJSON() {
		$jinput = JFactory::getApplication()->input;
		$userid = $jinput->post->get('id', '', 'INTEGER');
		$password = $jinput->post->get('pass', '', 'STRING');
		#echo $password."\r\n";
		#echo JFactory::getUser($userid)->password."\r\n" ;
		if($userid == NULL || $userid == '0' ) {
			echo "Access denied";
			return;
		}

		if( JFactory::getUser($userid)->password != $password ) {
			echo "Access denied";
			return;
		}
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
		//SELECT subject, last_post_guest_name, last_post_message FROM `y84ah_kunena_topics` ORDER BY last_post_time DESC LIMIT 5; 
		$query->select($db->quoteName(array('id','subject', 'last_post_guest_name', 'last_post_message', 'last_post_time')));
        $query->from($db->quoteName('#__kunena_topics'));
		$query->order($db->quoteName('last_post_time'). ' DESC');
		$query->setLimit('5');
		$db->setQuery($query);
		$posts = $db->loadObjectList();				
		echo json_encode($posts);
		// Return the Hello
		return $posts;
	}
	
	public static function checkAndUpdatePeriodicEvents(){
		
		date_default_timezone_set("Europe/Berlin"); 
		// Obtain a database connection
		$db = JFactory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
 
		$query->select($db->quoteName(array('id', 'date', 'periodic','periodic_value', 'starttime','duration')));
        $query->from($db->quoteName('#__tvm_events'));		
		$query->where($db->quoteName('periodic').' != '.$db->quote('none'));
		$query->order($db->quoteName('date') . ' ASC');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		foreach($result as $row) {
			switch($row->periodic) {
				case 'weekly':
					//var_dump(date("Y-m-d"));
					//var_dump(date("Y-m-d",strtotime($row->date)));
					// try to find difference in days
					//if(date("Y-m-d") > date("Y-m-d",strtotime($row->date))) {
					if(date("Y-m-d") > $row->date) {
						// Entry is older than today, we should update
						$newDate = date("Y-m-d",strtotime($row->date)+(60*60*24*7));
						tvmController::deleteEntriesFromEvent($row->id);
						tvmController::updateEventDate($row->id, $newDate, true);
					}
					
					//$eventClock = explode(":",$row->starttime);
					$endTime = strtotime($row->date .' '.$row->starttime) + $row->duration*60;
					//var_dump($endTime);
					//var_dump(time());
					//print_r(date("H").' ');
					//print_r(date("i").' ');
					//print_r(date("s").' ');
					//die();
					//date("H:i:s")
					if(time() >= $endTime ) {
						//die();
						// Entry is older than today, we should update
						$newDate = date("Y-m-d",strtotime($row->date)+(60*60*24*7));
						tvmController::deleteEntriesFromEvent($row->id);
						tvmController::updateEventDate($row->id, $newDate, true);
					}
					break;
			}
		}
	}
}
?>