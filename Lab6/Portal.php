<?php
session_start();
#######################
####### DATABASE ######
#######################
$conn = mysqli_connect("localhost", "root", "toor", "lab6");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
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
###  PHP AJAX        ##
#######################

$ajax_action = get_value('ajax_action');

#######################
####  HTML SECTION ####
#######################
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" xmlns:Name="http://www.w3.org/1999/xhtml"></script>

<title>Portal - Welcome</title>
<table width="75%">
<tr>
	<td><h1>Portal</h1></td>
	<td> Welcome <?= $_SESSION['username']?>! &nbsp;&nbsp;&nbsp; <a href="logout.php"><button>Logout</button></td>
</tr>	
</table>