<?php
#######################
####### INCLUDES ######
#######################
include_once('Shared_Functions.php');

#######################
###  PHP POST-FORM   ##
#######################

$file_name = 'users.txt';
$action = get_value('action');
$success = false;

if($action == 'add_user'){
	// user submitted the form, sign up by adding to our text file
	$username =  get_value('username');
	$password = get_value('password');
	
	// configure the 16-bit RSA encryption and grab our keys
	$rsa = new Crypt_RSA();
	$rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
	$rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
	extract($rsa->createKey(512));
	
	if(file_exists($file_name)){
		// file exists, grab contents so we can rewrite
		$user_text = file_get_contents($file_name);
	} else {
		// file doesnt exist, begin by creating a new file
		$user_text = '';
	}// end if the file exists
	
	// store all of our data in a php array
	$user_data = array();
	$user_data['username'] = $username;
	$user_data['password'] = $password;
	$user_data['pub_key'] = $publickey;
	$user_data['priv_key'] = $privatekey;
	
	// json encode the data before we store in our .txt file
	$user_text .= json_encode($user_data) . "\n";
	
	// write the user to our user file
	$users_file = fopen($file_name, "w");
	fwrite($users_file, $user_text);
	fclose($users_file);
	
	// redirect to the login page
	header('Location: login.html');
}// end if $action == 'data'

#######################
####  HTML SECTION ####
#######################
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" xmlns:Name="http://www.w3.org/1999/xhtml"></script>

<title>Signup - Welcome</title>
<h1>Registration Form</h1>

<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input  type="hidden" name="action" value="add_user">
  <table width="15%">
  <tr>
	<td><label for="username">Username:</label></td>
	<td><input id="username" type="text" name="username" required></td>
 </tr>	
<tr>
	<td><label for="password">Password:</label></td>
	<td><input id="password" type="password" name="password" required></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><input type="submit" name="submit" value="Sign Up"></td>
</tr>
</table>
</form>
<br>
<br>
Already signed up? <a href="login.html">Click here</a> to login.