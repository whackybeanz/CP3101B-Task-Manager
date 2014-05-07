<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	$reply['form-data'] = array();
	
	$reply['messages'] = array("", "");
	$reply['status-class'] = array();
	$reply['outcome'] = array();
	
	$_SESSION['messages'] = array("", "");
	$_SESSION['status-class'] = array("msg-impt", "msg-impt");
	$_SESSION['outcome'] = array();
	
	$user = $_REQUEST["username"];
	$pwd = $_REQUEST["password"];
	
	//Check for empty fields
	$fields_to_check = array($user, $pwd);
	
	for($i = 0; $i < count($fields_to_check); $i++){
		if($fields_to_check[$i] == ""){
			$_SESSION['messages'][$i] = "&nbsp;&nbsp;&#171; Please fill in this field";
			$reply['status'] = "error";
		}
	}	
	
	//Repopulate any existent form data
	$reply['form-data'] = array("user" => $user);
	
	//Proceed with login
	if($reply['status'] == 'ok'){
		$succ_login = login($user, $pwd);
		
		if(!$succ_login){
			$reply['status'] = "error";
		}
	}
	$reply['messages'] = $_SESSION['messages'];
	$reply['status-class'] = $_SESSION['status-class'];
	$reply['outcome'] = $_SESSION['outcome'];
	
	print json_encode($reply);
?>