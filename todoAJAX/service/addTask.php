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
	
	$_SESSION['messages'] = array("", "");
	$_SESSION['status-class'] = array("msg-impt", "msg-impt");
	$_SESSION['outcome'] = array();
	
	$name = $_REQUEST["name"];
	$hrs = $_REQUEST["hrs"];
	$mins = $_REQUEST["mins"];
	$notes = $_REQUEST["notes"];

	//Check for empty fields
	if($name == ""){
		$_SESSION['messages'][0] = "&nbsp;&nbsp;&#171; Please fill in this field";
		$reply['status'] = "error";
	}
	
	//Repopulate any existent form data
	$reply['form-data'] = array("name" => $name, "hrs" => $hrs, "mins" => $mins, "notes" => $notes);
	
	//Check for validity of inputs
	$is_valid_duration = check_duration($hrs, $mins);
	
	if(!$is_valid_duration){
		$reply['status'] = 'error';
	}
	
	//If inputs are valid, proceed to add task
	if($reply['status'] == 'ok'){
		$succ_add_task = add_task($name, $hrs, $mins, $notes);
		
		if(!$succ_add_task){
			$reply['status'] = 'error';
		}
	}
	$reply['messages'] = $_SESSION['messages'];
	$reply['status-class'] = $_SESSION['status-class'];
	$reply['outcome'] = $_SESSION['outcome'];
	
	print json_encode($reply);
?>