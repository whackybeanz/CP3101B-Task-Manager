<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	
	if(!isset($_SESSION['is_logged_in'])){
		$reply['status'] = 'error';
	}
	
	print json_encode($reply);
?>