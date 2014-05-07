<?php
	#Database functions
	function db_connect(){
		include "config.inc";
		$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname='$db_name' user='$db_user' password='$db_password'");
								
		if(!$dbconn){
			echo("Can't connect to the database");	
			exit;
		}
		return $dbconn;
	}
	
	#Get Account Details functions
	function getAccountDetails(){
		$dbconn = db_connect();

		//Populate My Account form details
		$account_details_query = "SELECT USERNAME, BIRTHDAY, EMAIL FROM APPUSER WHERE USERNAME = $1";
		$result = pg_prepare($dbconn, "acct_query", $account_details_query);
		$result = pg_execute($dbconn, "acct_query", array($_SESSION['username']));
		
		if($row = pg_fetch_row($result)){
			for($i = 0; $i < count($row); $i++){
				array_push($_SESSION['form-data'], $row[$i]);
			}
		}
	}
	
	#Login functions
	function login($user, $pwd){
		$dbconn = db_connect();
		$result = pg_prepare($dbconn, "search_query", "SELECT USERNAME, PASSWORD FROM APPUSER WHERE USERNAME = $1 AND PASSWORD = $2");
		$result = pg_execute($dbconn, "search_query", array($user, md5($pwd)));
		
		if($row = pg_fetch_array($result)){
			$_SESSION['username'] = $user;
			$_SESSION['is_logged_in'] = true;
			return true;
		} else {
			$_SESSION['messages'][0] = "&nbsp;&nbsp;&#171; Invalid username or password";
			$_SESSION['messages'][1] = "&nbsp;&nbsp;&#171; Invalid username or password";
			return false;
		}
	}
	
	#Registration functions
	function is_bad_user($reg_name){
		if($reg_name == ""){
			return true;
		}
	
		$dbconn = db_connect();
		$check_duplicate_user_query = "SELECT USERNAME FROM APPUSER WHERE USERNAME = $1";
		$result = pg_prepare($dbconn, "check_query", $check_duplicate_user_query);
		$result = pg_execute($dbconn, "check_query", array($reg_name));
		
		if($row = pg_fetch_array($result)){
			$_SESSION['messages'][0] = "&nbsp;&nbsp;&#171; Username " . htmlspecialchars($reg_name) . " has been taken";
			return true;
		} else {
			$_SESSION['status-class'][0] = "msg-ok";
			$_SESSION['messages'][0] = "&nbsp;&nbsp;&#171; Username " . htmlspecialchars($reg_name) . " is available!";
			return false;
		}
	}
	
	function is_mismatched_pwd($reg_pwd, $reg_conf_pwd){
		if($reg_pwd == "" || $reg_conf_pwd == ""){
			return true;
		}
	
		if($reg_pwd != $reg_conf_pwd){
			$_SESSION['messages'][1] = "&nbsp;&nbsp;&#171; Mismatched passwords";
			$_SESSION['messages'][2] = "&nbsp;&nbsp;&#171; Mismatched passwords";
			return true;
		} else {
			$_SESSION['status-class'][1] = "msg-ok";
			$_SESSION['messages'][1] = "&nbsp;&nbsp;&#171; Passwords match!";
			$_SESSION['status-class'][2] = "msg-ok";
			$_SESSION['messages'][2] = "&nbsp;&nbsp;&#171; Passwords match!";
			return false;
		}
	}
	
	function is_bad_email($email){
		if($email == ""){
			return true;
		}
		else{
			if(strpos($email, " ") !== false || strpos($email, ".") == false || strpos($email, "@") == false){
				$_SESSION['messages'][4] = "&nbsp;&nbsp;&#171; Invalid email format";
				return true;
			}
			$dbconn = db_connect();
			$check_duplicate_email_query = "SELECT EMAIL FROM APPUSER WHERE EMAIL = $1";
			$result = pg_prepare($dbconn, "email_query", $check_duplicate_email_query);
			$result = pg_execute($dbconn, "email_query", array($email));
			
			if($row = pg_fetch_array($result)){
				$_SESSION['messages'][4] = "&nbsp;&nbsp;&#171; " . htmlspecialchars($email) . " is already registered in our database";
				return true;
			} else {
				$_SESSION['status-class'][4] = "msg-ok";
				$_SESSION['messages'][4] = "&nbsp;&nbsp;&#171; " . htmlspecialchars($email) . " is available for use!";
			}
			return false;
		}
	}
	
	function register($uname, $pwd, $bday, $email){
		$dbconn = db_connect();
		$insert_appuser_query = "INSERT INTO APPUSER (USERNAME, PASSWORD, BIRTHDAY, EMAIL, LAST_TASK_ID) VALUES ($1, $2, $3, $4, $5)";
		$result = pg_prepare($dbconn, "insert_query", $insert_appuser_query);
		$result = pg_execute($dbconn, "insert_query", array($uname, md5($pwd), $bday, $email, 1));
		
		if($result != NULL){
			array_push($_SESSION['outcome'], "Success!");
			array_push($_SESSION['outcome'], "Registration for " . htmlspecialchars($uname) . " was successful.");
			array_push($_SESSION['outcome'], "Please log in with your credentials.");
			return true;
		} else {
			array_push($_SESSION['outcome'], "Oops!");
			array_push($_SESSION['outcome'], "Could not perform registration. Please try again. Error: " . pg_last_error());
			return false;
		}
	}
	
	#Update Task related functions
	function deleteTask($task_id){	
		$dbconn = db_connect();	
		$delete_task_query = "DELETE FROM TASKS WHERE USERNAME=$1 AND ID=$2";
		$result = pg_prepare($dbconn, "delete_task_query", $delete_task_query);
		$result = pg_execute($dbconn, "delete_task_query", array($_SESSION['username'], $task_id));
		
		if(!$result){
			return false;
		} else {
			return true;
		}
	}
	
	function updateUnits($units_done, $task_id){
		$dbconn = db_connect();
		$get_total_units_query = "SELECT UNITS FROM TASKS WHERE USERNAME=$1 AND ID=$2";
		$result = pg_prepare($dbconn, "get_total_units_query", $get_total_units_query);
		$result = pg_execute($dbconn, "get_total_units_query", array($_SESSION['username'], $task_id));
		$row = pg_fetch_row($result);
		$total_units = $row[0];
		
		$done = 'f';
		
		if($units_done == $total_units){
			$done = 't';
		}
		
		$upd_units_query = "UPDATE TASKS SET UNITS_DONE=$1, DONE=$2 WHERE USERNAME=$3 AND ID=$4";
		$result = pg_prepare($dbconn, "upd_units_query", $upd_units_query);
		$result = pg_execute($dbconn, "upd_units_query", array($units_done, $done, $_SESSION['username'], $task_id));
	}
	
	function editTask($id, $name, $hrs, $mins, $notes){
		$dbconn = db_connect();
		$check_time_query = "SELECT HOURS, MINS FROM TASKS WHERE USERNAME=$1 AND ID=$2";
		$result = pg_prepare($dbconn, "check_time_query", $check_time_query);
		$result = pg_execute($dbconn, "check_time_query", array($_SESSION['username'], $id));
		$row = pg_fetch_row($result);
		
		$units = $hrs * 2 + $mins / 30;
		
		if($hrs == $row[0] && $mins == $row[1]){
			$update_task_query = "UPDATE TASKS SET TASK_NAME=$1, NOTES=$2 WHERE USERNAME=$3 AND ID=$4";
			$result = pg_prepare($dbconn, "update_task_query", $update_task_query);
			$result = pg_execute($dbconn, "update_task_query", array($name, $notes, $_SESSION['username'], $id));
		} else {
			$update_task_query = "UPDATE TASKS SET TASK_NAME=$1, HOURS=$2, MINS=$3, UNITS=$4, UNITS_DONE=$5, DONE=$6, NOTES=$7 WHERE USERNAME=$8 AND ID=$9";
			$result = pg_prepare($dbconn, "update_task_query", $update_task_query);
			$result = pg_execute($dbconn, "update_task_query", array($name, $hrs, $mins, $units, 0, 'FALSE', $notes, $_SESSION['username'], $id));
		}
		
		if(!$result){
			$_SESSION['messages'][2] = "An error occurred while updating the database. Error: " . pg_last_error();
			return false;
		} else {
			return true;
		}
	}
	
	#Add Task functions
	function check_duration($hours, $mins){
		if($hours == 0 && $mins == 0 ){
			$_SESSION['messages'][1] = "&nbsp;&nbsp;&#171; Please specify a duration larger than 0";
			return false;
		} elseif ($hours == 24 && $mins == 30){
			$_SESSION['messages'][1] = "&nbsp;&nbsp;&#171; Please ensure duration is within 24hrs 00mins";
			return false;
		} else {
			return true;
		}
	}
	
	function add_task($task_name, $hours, $mins, $notes){
		$num_units = $hours * 2 + $mins / 30;
		
		$dbconn = db_connect();
		$task_id_query = "SELECT LAST_TASK_ID FROM APPUSER WHERE USERNAME = $1";
		$result = pg_prepare($dbconn, "task_query", $task_id_query);
		$result = pg_execute($dbconn, "task_query", array($_SESSION['username']));
		$row = pg_fetch_row($result);
		$curr_task_id = $row[0];
		
		$add_task_query = "INSERT INTO TASKS (ID, USERNAME, TASK_NAME, HOURS, MINS, UNITS, UNITS_DONE, NOTES) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
		$result = pg_prepare($dbconn, "add_query", $add_task_query);
		$result = pg_execute($dbconn, "add_query", array($curr_task_id, $_SESSION['username'], $task_name, $hours, $mins, $num_units, 0, $notes));
		
		if(!$result){
			array_push($_SESSION['outcome'], "Oops!");
			array_push($_SESSION['outcome'], "An error occurred while updating the database. Please try again.");
			return false;
		}
		
		$curr_task_id += 1;
		$upd_task_id_query = "UPDATE APPUSER SET LAST_TASK_ID=$1 WHERE USERNAME=$2";
		$result = pg_prepare($dbconn, "upd_task_id_query", $upd_task_id_query);
		$result = pg_execute($dbconn, "upd_task_id_query", array($curr_task_id, $_SESSION['username']));
		
		if(!$result){
			array_push($_SESSION['outcome'], "Oops!");
			array_push($_SESSION['outcome'], "An error occurred while updating the database. Please try again.");
			return false;
		} else {
			array_push($_SESSION['outcome'], "Success!");
			array_push($_SESSION['outcome'], "Task: " . htmlspecialchars($task_name) . " has been added.");
			return true;
		}
	}
	
	#Update Account functions
	function is_wrong_old_pwd($user, $old_pwd){
		if($old_pwd == ""){
			return true;
		}
	
		$dbconn = db_connect();
		$pwd_query = "SELECT PASSWORD FROM APPUSER WHERE USERNAME = $1";
		$result = pg_prepare($dbconn, "pwd_query", $pwd_query);
		$result = pg_execute($dbconn, "pwd_query", array($user));
		$row = pg_fetch_row($result);
		
		if(($pwd = $row[0]) && ($pwd != md5($old_pwd))){
			$_SESSION['messages'][0] = "&nbsp;&nbsp;&#171; Incorrect current password";
			return true;
		}
		return false;
	}
	
	function is_mismatched_new_pwd($pwd, $conf_pwd){	
		if($pwd == "" && $conf_pwd == ""){
			return false;
		}
	
		if($pwd != $conf_pwd){
			$_SESSION['messages'][1] = "&nbsp;&nbsp;&#171; Mismatched passwords";
			$_SESSION['messages'][2] = "&nbsp;&nbsp;&#171; Mismatched passwords";
			return true;
		}
		return false;
	}
	
	function is_bad_new_email($user, $email){
		if($email == ""){
			return true;
		}
	
		if(strpos($email, " ") !== false || strpos($email, ".") == false || strpos($email, "@") == false){
			$_SESSION['messages'][4] = "&nbsp;&nbsp;&#171; Invalid email format";
			return true;
		}
		
		$dbconn = db_connect();
		$check_existing_email_query = "SELECT EMAIL FROM APPUSER WHERE USERNAME = $1";
		$result = pg_prepare($dbconn, "exist_email_query", $check_existing_email_query);
		$result = pg_execute($dbconn, "exist_email_query", array($user));
		$row = pg_fetch_row($result);
		
		if(($curr_email = $row[0]) && ($email == $curr_email)){
			return false;
		}
		else{
			$check_existing_email_query = "SELECT EMAIL FROM APPUSER WHERE EMAIL = $1";
			$result = pg_prepare($dbconn, "dup_email_query", $check_existing_email_query);
			$result = pg_execute($dbconn, "dup_email_query", array($email));
			
			if($row = pg_fetch_array($result)){
				$_SESSION['messages'][4] = "&nbsp;&nbsp;&#171; " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . " is already registered in our database";
				return true;
			} else {
				$_SESSION['status-class'][4] = "msg-ok";
				$_SESSION['messages'][4] = "&nbsp;&nbsp;&#171; " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . " is available for use!";
				return false;
			}
		}
	}
	
	function updateAcct($user, $new_pwd, $bday, $email){
		$dbconn = db_connect();
		
		if($new_pwd != null){
			$update_query = "UPDATE APPUSER SET PASSWORD=$1, BIRTHDAY=$2, EMAIL=$3 WHERE USERNAME=$4";
			$result = pg_prepare($dbconn, "update_w_pwd_query", $update_query);
			$result = pg_execute($dbconn, "update_w_pwd_query", array(md5($new_pwd), $bday, $email, $user));
		} else {
			$update_query = "UPDATE APPUSER SET BIRTHDAY=$1, EMAIL=$2 WHERE USERNAME=$3";
			$result = pg_prepare($dbconn, "update_no_pwd_query", $update_query);
			$result = pg_execute($dbconn, "update_no_pwd_query", array($bday, $email, $user));
		}

		if($result != NULL){
			array_push($_SESSION['outcome'], "Success!");
			array_push($_SESSION['outcome'], "Account update successful.");
			return true;
		} else {
			array_push($_SESSION['outcome'], "Oops!");
			array_push($_SESSION['outcome'], "Could not perform account update. Please try again. Error: " . pg_last_error());
			return false;
		}
	}
?>