<?php
session_start();
include_once('Shared_Functions.php');

$ajax_action = get_value('ajax_action');
$file_name = "users.txt";

if($ajax_action == 'check'){
	// we recieved an ajax request for checking login, grab our request variables
	$username = get_value('username');
	$password = get_value('password');
	$has_access = false;
	
	if(file_exists($file_name)){
		$users_file = fopen($file_name, "r");
	}else{
		$users_file = false;
	}// end if file exists
	
	// begin reading from users file
	if ($users_file) {
		// we have a users file
		while (($line = fgets($users_file)) !== false) {
			// loop over all users in users.txt
			$line = json_decode($line, true);
			
			if($line['username'] == $username && $line['password'] == $password){
				$has_access = true;
			}// end if they should be granted access
			
		}// end while loop over lines in the file

		fclose($users_file);
	}// end if we have a users file
	
	// use php array because we need to return a json object
	$data = array();
	
	if($has_access){
		// set our session variable
		$_SESSION['user'] = $username;
		$data['access'] = 'Y';
	}else{
		$data['access'] = 'N';
	}// end if set the access if we need in array, 
	
	echo json_encode($data);
	exit;
}// end if we recieved an ajax request to login.php, return some data