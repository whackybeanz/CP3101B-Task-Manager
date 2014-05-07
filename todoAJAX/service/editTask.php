<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	
	$reply['status-class'] = array();
	$reply['messages'] = array();
	
	$_SESSION['status-class'] = array("msg-impt", "msg-impt");
	$_SESSION['messages'] = array("", "", "");
	
	$id = $_REQUEST['id'];
	$name = $_REQUEST['name'];
	$hrs = $_REQUEST['hrs'];
	$mins = $_REQUEST['mins'];
	$notes = $_REQUEST['notes'];
	
	//Check for empty fields
	if($name == ""){
		$_SESSION['messages'][0] = "&nbsp;&nbsp;&#171; Please fill in this field";
		$reply['status'] = "error";
	}
	
	//Check for validity of inputs
	$is_valid_duration = check_duration($hrs, $mins);
	
	if(!$is_valid_duration){
		$reply['status'] = 'error';
	}
	
	//If inputs are valid, proceed to edit task
	if($reply['status'] == 'ok'){
		$succ_edit = editTask($id, $name, $hrs, $mins, $notes);
	
		if(!$succ_edit){
			$reply['status'] = 'error';
		}
	}
	$reply['status-class'] = $_SESSION['status-class'];
	$reply['messages'] = $_SESSION['messages'];
	
	print json_encode($reply);
?>