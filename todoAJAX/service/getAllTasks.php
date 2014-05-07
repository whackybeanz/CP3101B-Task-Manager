<?php
	include "config.inc";
	include "functions.php";
	session_save_path("sessions");
	session_start();
	header('Content-Type: application/json');
	
	$reply = array();
	$reply['status'] = 'ok';
	$reply['task-details'] = array();

	if($reply['status'] == 'ok'){
		$dbconn = db_connect();
		
		$tasks_query = "SELECT A.USERNAME, T.*
						FROM APPUSER A, TASKS T
						WHERE A.USERNAME = $1
						AND T.USERNAME = $1
						ORDER BY ID ASC";
		$tasks = pg_prepare($dbconn, "tasks_query", $tasks_query);
		$tasks = pg_execute($dbconn, "tasks_query", array($_SESSION['username']));
		
		$num_tasks_query = "SELECT COUNT(*)
							FROM TASKS
							WHERE USERNAME = $1";
		$result = pg_prepare($dbconn, "num_tasks_query", $num_tasks_query);
		$result = pg_execute($dbconn, "num_tasks_query", array($_SESSION['username']));
		$row = pg_fetch_row($result);
		$num_tasks = $row[0];
		
		$task_name = "";
		$total_duration = "";
		$duration_left = "";
		$done = false;
		$notes = "";
		
		for($count = 0; $count < $num_tasks; $count++){
			$row = pg_fetch_array($tasks);
			$id = $row['id'];
			$task_name = $row['task_name'];
			$total_hours = $row['hours'];
			$total_mins = $row['mins'];
			$total_units = $row['units'];
			$units_done = $row['units_done'];
			$done = $row['done'];
			$notes = $row['notes'];
			
			array_push($reply['task-details'], array("id" => $id, "task-name" => $task_name, "total-hours" => $total_hours,
													"total-mins" => $total_mins, "total-units" => $total_units,
													"units-done" => $units_done, "done" => $done, "notes" => $notes));
		}
	}
	print json_encode($reply);
?>