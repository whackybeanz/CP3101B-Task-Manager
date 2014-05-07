<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	$reply['form-data'] = array();

	$_SESSION['form-data'] = array();
	
	if(!isset($_SESSION['is_logged_in'])){
		$reply['status'] = 'error';
	}
	
	if($reply['status'] == 'ok'){	
		getAccountDetails();
		$reply['form-data'] = $_SESSION['form-data'];
	}
	
	print json_encode($reply);
?>