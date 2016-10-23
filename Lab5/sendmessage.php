<?php
session_start();
#######################
###  	   PHP      ###
#######################
include_once('Shared_Functions.php');

$messages_name = 'messages.txt';
$users_name = 'users.txt';
$username = get_value('user', $_SESSION);

if($username === NULL){
	echo "You must first be logged in to access this page. <a href='login.html'>Click here</a> to go to the login page.";
	exit;
}// end if username is null, they havent logged in

$ajax_action = get_value('ajax_action');

if($ajax_action == 'send'){
	// ajax request for sending a message
	$data['sender'] = $sender = get_value('sender');
	$data['reciever'] = $reciever = get_value('reciever');
	$body = get_value('body');
	
	$public_key = get_key($reciever, 'pub_key');
	
	if(!isset($public_key) || $public_key === NULL){
		// we didnt find a user matching our reciever, return an error
		echo -1;
		exit;
	}// end if we dont have a public key, exit now 
	
	// encrypt the body with our public key
	$data['body'] = rsa_encrypt($body, $public_key);
	
	if(file_exists($messages_name)){
		// file exists grab the contents
		$file_contents = file_get_contents($messages_name);
	}else{
		// file doesnt exist, start a new file
		$file_contents = '';	
	}// end if our posts file exists
	
	// serialize instead of using json because this data contains encrypted/unsupported characters
	// weird ass encryption = weird ass delimiter between objects
	$file_contents .= serialize($data) . "???????????\n";
	
	file_put_contents($messages_name, $file_contents);
}// end if ajax action == send