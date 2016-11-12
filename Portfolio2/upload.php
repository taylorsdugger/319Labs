<?php
session_start();
#######################
####### DATABASE ######
#######################
$conn = mysqli_connect("localhost", "root", 'toor', "portfolio");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit;
}// end if we couldnt connect to the database

#######################
####### INCLUDES ######
#######################
include_once('Shared_Functions.php');

if(get_value('username', $_SESSION) == NULL){
	echo 'You do not have access to this page, please <a href="Signup.php">create an account</a> or <a href="Login.php">login</a>.';
	exit;
}// end if the session user is not set, they dont have access

#######################
### PHP AJAX RESPONSE #    
#######################

if(count($_FILES) == 0){
	// user didnt provide any files, print an error and exit
	echo 'Please select a file for upload.';
	exit;
}// end if the user didnt provide any files, send back an error

if(is_admin($_SESSION['username'])){
	// admins have unlimited file storage
	$max_file_size = null;
	$max_file_uploads = null;
}else{
	// users have a set amount of file storage
	$user_query = mysqli_query($conn, "SELECT max_file_upload_size, max_file_uploads FROM users WHERE user_name = '".mysqli_real_escape_string($conn, $_SESSION['username'])."'");
	$user_info = mysqli_fetch_assoc($user_query);

	$max_file_size = $user_info['max_file_upload_size'];
	$max_file_uploads = $user_info['max_file_uploads'];
}// end if the user is an admin

if($max_file_size != null){
	
	if($_FILES['upload']['size'] > $max_file_size){
		echo 'Error: You cannot upload files more than ' . $max_file_size . ' bytes.';
		exit;
	}// the file is too big, print an error
	
}// end if we need to check the max file size

$target_dir = "uploads/" . $_SESSION['username'] . '/';
$target_file = $target_dir . basename($_FILES["upload"]["name"]);

if(!file_exists ($target_dir)){
	// create our file directory if it doesnt exist
	mkdir ($target_dir);
}// end if the target directory doesn't exist, create it now

if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
	
	// check if the file already exists in user_files table
	$file_check_query = mysqli_query($conn, "SELECT * FROM user_files WHERE user_name = '".$_SESSION['username']."' AND file_name = '".$_FILES['upload']['name']."'");
	
	if(mysqli_num_rows($file_check_query) > 0){
		// this file already exists, we are replacing, just update the uploaded time
		mysqli_query($conn, "UPDATE user_files SET uploaded = NOW() WHERE user_name = '".$_SESSION['username']."' AND file_name = '".$_FILES['upload']['name']."'");
	}else{
		 // insert into user_files table
		mysqli_query($conn, "INSERT INTO user_files SET user_name = '".$_SESSION['username']."', file_name = '".$_FILES['upload']['name']."'");	
	}// end if checking if the file already exists
	
} else {
	echo "Sorry, there was an error uploading your file.";
}// end if actually upload the file and check for an error