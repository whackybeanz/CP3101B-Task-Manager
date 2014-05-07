<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	
	$task_id = $_REQUEST['task_id'];
	$units_done = $_REQUEST['num_units'];
	
	updateUnits($units_done, $task_id);
	
	print json_encode($reply);
?>