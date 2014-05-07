<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	
	$reply['outcome'] = array();
	
	$_SESSION['outcome'] = array();
	
	$id = $_REQUEST['id'];
	
	$succ_del = deleteTask($id);
	
	if(!$succ_del){
		$reply['status'] = 'error';
	}
	
	print json_encode($reply);
?>