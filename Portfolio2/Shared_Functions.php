<?php
#########################
# PHP SHARED  FUNCTIONS # 
#########################

function get_value($index, $arr = NULL){
	// use this function to avoid undefined errors
	
	if($arr === NULL){
		$arr = $_REQUEST;
	}// end if $arr was not set, set it to our $_REQUEST array
		
	if (isset($arr[$index])) {
		return $arr[$index];
	} else {
		return NULL;
	}// end if isset, checking the arrays index
	
}// end get_value function

function is_admin($username){
	global $conn;
	$admin_query = mysqli_query($conn, "SELECT * FROM users u WHERE u.user_name = '".mysqli_real_escape_string($conn, $username)."' AND u.admin = 1");
	
	if(mysqli_num_rows($admin_query) > 0){
		return true;
	}else{
		return false;
	}// end if they are a admin
}// end function for determining if this user is a admin