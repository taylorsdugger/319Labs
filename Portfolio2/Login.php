<?php
session_start();
#######################
####### DATABASE ######
#######################
$conn = mysqli_connect("localhost", "root", 'toor', "portfolio");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}// end if we couldnt connect to the database

#######################
####### INCLUDES ######
#######################
include_once('Shared_Functions.php');

#######################
###  PHP AJAX        ##
#######################

$ajax_action = get_value('ajax_action');

if($ajax_action == 'check_login'){
	
	$username = get_value('username');
	$password = md5(get_value('password'));
	
	$query = mysqli_query($conn, "SELECT * FROM users WHERE user_name = '".mysqli_real_escape_string($conn, $username)."' AND password = '".mysqli_real_escape_string($conn, $password)."'");
	
	if(mysqli_num_rows($query) > 0){
		// they have access
		$_SESSION['username'] = $username;
	}else{
		// they dont have access
		echo '-1';
	}// end if checking our query to see if the user should be granted access
	
	exit;
}// end if ajax_action == 'check_login'

#######################
####  HTML SECTION ####
#######################
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" xmlns:Name="http://www.w3.org/1999/xhtml"></script>

<title>Login - Welcome</title>
<h1>File Storage Login</h1>

<strong><p id="error"></p></strong>

<form method="POST">
  <table width="100%" cellspacing="10">
  <col width="130">
  <tr>
	<td><label for="username">Username:</label></td>
	<td><input id="username" type="text" name="username"></td>
 </tr>	
<tr>
	<td><label for="password">Password:</label></td>
	<td><input id="password" type="password" name="password"></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><input type="submit" onclick="checklogin(); return false;" name="login" value="Login"></td>
</tr>
</table>
</form>
<br>
<br>
Need an account? <a href="Signup.php">Click here</a> to signup.

<!-- JAVASCRIPT -->
<script>
function checklogin(){
	
	$.post( "Login.php", { ajax_action: 'check_login', 
						   username: $("#username").val(), 
						   password: $("#password").val()
						 },
		 
		function(data){
			// ajax success function, attempted to sign a user up
			if(data == ''){
				window.location = "portal.php";
			}else{
				document.getElementById('error').innerHTML = "The username and password don't match a user in our database.";
			}
		}
	);
}
</script>