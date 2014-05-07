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
	
	$uname = $_REQUEST["username"];
	$pwd = $_REQUEST["password"];
	$conf_pwd = $_REQUEST["conf_pwd"];
	$bday = $_REQUEST["bday"];
	$email = $_REQUEST["email"];
	
	//Check for empty fields
	$fields_to_check = array($uname, $pwd, $conf_pwd, $bday, $email);
	
	for($i = 0; $i < count($fields_to_check); $i++){
		if($fields_to_check[$i] == ""){
			$_SESSION['messages'][$i] = "&nbsp;&nbsp;&#171; Please fill in this field";
			$reply['status'] = "error";
		}
	}	
	
	//Repopulate any existent form data
	$reply['form-data'] = array("user" => $uname, "bday" => $bday, "email" => $email);
	
	//Check for validity of inputs
	$bad_user = is_bad_user($uname);
	$mismatch_pwd = is_mismatched_pwd($pwd, $conf_pwd);
	$bad_email = is_bad_email($email);
	
	if($bad_user || $mismatch_pwd || $bad_email || $bday == ""){
		$reply['status'] = "error";
	}
	
	//If inputs are valid, proceed to registration
	if($reply['status'] == "ok"){	
		$succ_reg = register($uname, $pwd, $bday, $email);
		
		if(!$succ_reg){
			$reply['status'] = "error";
		}
	}
	$reply['messages'] = $_SESSION['messages'];
	$reply['status-class'] = $_SESSION['status-class'];
	$reply['outcome'] = $_SESSION['outcome'];
	
	print json_encode($reply);
?>