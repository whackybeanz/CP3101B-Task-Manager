<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	$reply['form-data'] = array();
	
	$reply['messages'] = array();
	$reply['status-class'] = array();
	$reply['outcome'] = array();
	
	$_SESSION['messages'] = array("", "", "", "", "");
	$_SESSION['status-class'] = array("msg-impt", "msg-impt", "msg-impt", "msg-impt", "msg-impt");
	$_SESSION['outcome'] = array();
	
	$user = $_SESSION['username'];
	$curr_pwd = $_REQUEST["curr_pwd"];
	$new_pwd = $_REQUEST["new_pwd"];
	$conf_new_pwd = $_REQUEST["conf_new_pwd"];
	$bday = $_REQUEST["bday"];
	$email = $_REQUEST["email"];
	
	//Check for empty fields
	$fields_to_check = array($curr_pwd, " ", " ", $bday, $email);
	
	for($i = 0; $i < count($fields_to_check); $i++){
		if($fields_to_check[$i] == ""){
			$_SESSION['status-class'][$i] = "msg-impt";
			$_SESSION['messages'][$i] = "&nbsp;&nbsp;&#171; Please fill in this field";
		}
	}	
	
	//Repopulate any existent form data
	$reply['form-data'] = array("bday" => $bday, "email" => $email);

	//Check for validity of inputs
	$mismatch_old_pwd = is_wrong_old_pwd($user, $curr_pwd);
	$mismatch_new_pwd = is_mismatched_new_pwd($new_pwd, $conf_new_pwd);
	$bad_email = is_bad_new_email($user, $email);
	
	if($mismatch_old_pwd || $mismatch_new_pwd || $bad_email || $bday == ""){
		$reply['status'] = "error";
	}
	
	//If inputs are valid, proceed with account update
	if($reply['status'] == 'ok'){
		$succ_upd = updateAcct($user, $new_pwd, $bday, $email);
		
		if(!$succ_upd){
			$reply['status'] = "error";
		}
	}
	$reply['messages'] = $_SESSION['messages'];
	$reply['status-class'] = $_SESSION['status-class'];
	$reply['outcome'] = $_SESSION['outcome'];
	
	print json_encode($reply);
?>