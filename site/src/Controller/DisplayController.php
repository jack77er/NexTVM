<?php
/**
 * @version    CVS: 4.0.0
 * @package    Com_Tvm
 * @author     Jacob Maxa <jacob.maxa@gmail.com>
 * @copyright  Jacob Maxa
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

namespace Seebaren\Component\Tvm\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Input;

/**
 * Display Component Controller
 *
 * @since  4.0.0
 */
class DisplayController extends BaseController
{
	
	protected $default_view = 'Tvm';
	
	
	/**
	 * Constructor.
	 *
	 * @param  array                $config   An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 * @param  MVCFactoryInterface  $factory  The factory.
	 * @param  CMSApplication       $app      The JApplication for the dispatcher
	 * @param  Input              $input    Input
	 *
	 * @since  4.0.0
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  \Joomla\CMS\MVC\Controller\BaseController  This object to support chaining.
	 *
	 * @since   4.0.0
	 */
	public function display($cachable = false, $urlparams = false)
	{

		$view = $this->input->getCmd('view', 'tvms');
		$view = $view == "featured" ? 'tvms' : $view;
		$this->input->set('view', $view);
		

		parent::display($cachable, $urlparams);
		return $this;
	}
	
	
	public function checkUserAccess() {
		$input = Factory::getApplication()->input;
		$username = $input->post->get('name', '', 'USERNAME');
		$password = $input->post->get('pass', '', 'STRING');
		#$post = $input->post->getArray();
		#var_dump($post);
		#var_dump($username);
		#var_dump($password);
		#die();	
		if (($username == NULL) OR ($password == NULL)) {
			return $this;
		}
		
		DisplayController::checkAndUpdatePeriodicEvents();
		// Obtain a database connection
		$db = Factory::getDbo();
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
		$userObj = Factory::getUser($userID);
		
		$passwordMatch = UserHelper::verifyPassword($password, $userObj->password, $userObj->id);
		if($passwordMatch) {
			$userObj->lastvisitDate = strtotime($userObj->lastvisitDate);
			echo json_encode($userObj);
			
		} else {
			echo '0';
			return $this;
		}
		//var_dump($passwordMatch);
		//echo json_encode($userObj);
		return $this;
	}
	
	private static function updateEventDate($id = 0, $newDate, $isPeriodic = false ){
		// Obtain a database connection
		$db = Factory::getDbo();
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
		$db = Factory::getDbo();
		// Retrieve the shout
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__tvm_entry'));
		$query->where($db->quoteName('event_id').' = '.$db->quote($id));
		$db->setQuery($query);
		
		$result = $db->execute();
	}
	
	
	
	public function updateEventRegistrationJSON() {
		
		// read POST input values
		$jinput = Factory::getApplication()->input;
		$userID = $jinput->post->get('id', '0', 'INTEGER');
		$eventID = $jinput->post->get('event', '0', 'INTEGER');
		$eventUserID = $jinput->post->get('userid', '0', 'INTEGER');
		$userState = $jinput->post->get('state', '0', 'INTEGER');
		$userPassword = $jinput->post->get('pass', '', 'STRING');
		$userComment= $jinput->post->get('comment', '', 'STRING');
		// return variable
		#$retVal = new stdClass();
		$retVal =(object)[];
		
		if($eventUserID == "0") {
			$eventUserID = $userID;
		}
		
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
			return $this;
		}		
		
	
		if( Factory::getUser($userID)->password != $userPassword ) {
			echo "Access denied";
			return $this;
		}
		
		
		if (!is_numeric($eventID) || (intval($userState) > 5)) {
			$retVal->updateState = -3; 
			echo json_encode($retVal);
			return $this;
		}
		// first check if event is open for changes
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
	    $query->from($db->quoteName('#__tvm_events'));
		$query->where($db->quoteName('id').' = '.$db->quote($eventID).' AND '.$db->quoteName('closed').' = '.$db->quote(1));
		$db->setQuery($query);
		//Load the row.
		$DBresult = $db->loadResult();
		if($DBresult > 0) {
			if($eventUserID == $userID) {
				// event with ID is closed --> no status changes allowed
				$retVal->updateState = -1; 
				$retVal->userState = 4; // forced NO
				echo json_encode($retVal);
				return $this;
			}
		}
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// check, if user is already registered
		$query->select(array($db->quoteName('id'),$db->quoteName('state'),$db->quoteName('acknowledged')));
	    $query->from($db->quoteName('#__tvm_entry'));
		$query->where($db->quoteName('event_id').' = '.$db->quote($eventID).' AND '.$db->quoteName('user_id').' = '.$db->quote($eventUserID));
		$db->setQuery($query);
		//Load the row.
		$DBresult = $db->loadAssoc();
		if ($DBresult == NULL) {
			// nicht gefunden --> neu anlegen
			$ret='nicht gefunden';
			// Insert columns.
			$columns = array('user_id', 'event_id', 'state', 'acknowledged', 'comment','published');
			// Insert values.
			$values = array( $db->quote($eventUserID),  $db->quote($eventID),  $db->quote($userState), 0, $db->quote($userComment), 1);
			// Retrieve the shout
			$db = Factory::getDbo();
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
			
		} else if(intval($DBresult['id']) > 0){
			// not found --> new
			$oldAcked = $DBresult['acknowledged'];
			$oldState = $DBresult['state'];
			// gefunden --> update
			$acked = '0';
			// no change in user state, copy old ack state to update
			if($oldState == $userState) {
				$acked = $oldAcked;
			}
			
			$retVal->acknowledged = $acked;
			
			$ret = 'gefunden';
			$db = Factory::getDbo();
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
				$db->quoteName('user_id') . ' = '.$db->quote($eventUserID), 
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
		 }
		 
		 echo json_encode($retVal);
		 return $this;
	}
	
	public function getTVMEventsJSON() {
		date_default_timezone_set("Europe/Berlin"); 
		
		$jinput = Factory::getApplication()->input;
		$userid = $jinput->post->get('id', '', 'INTEGER');
		$password = $jinput->post->get('pass', '', 'STRING');
		$category = $jinput->post->get('category', '1', 'INTEGER');
		if($userid == NULL || $userid == '0' ) {
			echo "Access denied";
			return $this;
		}
		if( Factory::getUser($userid)->password != $password ) {
			echo "Access denied";
			return $this;
		}
		
		// Obtain a database connection
		$db = Factory::getDbo();
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
			$eventusers = DisplayController::getTvmUserByEvent($event->id);
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
		return $this;
	}
	
	public function getTVMCategoriesJSON() {		
		$jinput = Factory::getApplication()->input;
		$userid = $jinput->post->get('id', '', 'INTEGER');
		$password = $jinput->post->get('pass', '', 'STRING');
		#echo $password."\r\n";
		#echo Factory::getUser($userid)->password."\r\n" ;
		if($userid == NULL || $userid == '0' ) {
			echo "Access denied";
			return $this;
		}
		if( Factory::getUser($userid)->password != $password ) {
			echo "Access denied";
			return $this;
		}
		
		// Obtain a database connection
		$db = Factory::getDbo();
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
		return  $this;
	}
	
	public static function getTvmUserByEvent($event_id = 0) {
		// Obtain a database connection
		$db = Factory::getDbo();
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
		$db = Factory::getDbo();
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
	
	public function setEventUserAck(){
		$jinput = Factory::getApplication()->input;
		//$input = Factory::getApplication()->input;
		//$formData  = $input->get('data', array(), 'array');
		
		$event_id = $jinput->post->get('event_id', '', 'INTEGER');
		$password = $jinput->post->get('pass', '', 'STRING');
		$user_id = $jinput->post->get('user_id', '', 'INTEGER');
		$ack = $jinput->post->get('ack', '', 'INTEGER');
		$editor_id = $jinput->post->get('editor_id', '', 'INTEGER');
		
		if($editor_id == NULL || $editor_id == '0' ) {
			echo "Access denied";
			return $this;
		}
		if( Factory::getUser($editor_id)->password != urldecode($password) ) {
			echo "Access denied";
			return $this;
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// Fields to update.
		$fields = array(
				$db->quoteName('acknowledged') . ' = ' . $db->quote($ack),
				$db->quoteName('state') . ' = 1',
				$db->quoteName('updated') . ' = NOW()',
				$db->quoteName('updated_by') . ' = '. $db->quote($editor_id),
		);
		// Conditions for which records should be updated.
		$conditions = array(
				$db->quoteName('user_id') . ' = '.$db->quote($user_id),
				$db->quoteName('event_id') . ' = ' . $db->quote($event_id)
		);
		$query->update($db->quoteName('#__tvm_entry'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$entryCount = $db->execute();
		echo $entryCount;
		return $this;
        }
	
	public function getTVMBoardPostsJSON() {
		$jinput = Factory::getApplication()->input;
		$userid = $jinput->post->get('id', '', 'INTEGER');
		$password = $jinput->post->get('pass', '', 'STRING');
		#echo $password."\r\n";
		#echo Factory::getUser($userid)->password."\r\n" ;
		if($userid == NULL || $userid == '0' ) {
			echo "Access denied";
			return;
		}
		if( Factory::getUser($userid)->password != $password ) {
			echo "Access denied";
			return;
		}
		// Obtain a database connection
		$db = Factory::getDbo();
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
		return $this;
	}
	
	public static function checkAndUpdatePeriodicEvents(){
		
		date_default_timezone_set("Europe/Berlin"); 
		// Obtain a database connection
		$db = Factory::getDbo();
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
						DisplayController::deleteEntriesFromEvent($row->id);
						DisplayController::updateEventDate($row->id, $newDate, true);
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
						DisplayController::deleteEntriesFromEvent($row->id);
						DisplayController::updateEventDate($row->id, $newDate, true);
					}
					break;
			}
		}
	}
}
