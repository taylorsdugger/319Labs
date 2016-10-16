<?php
#######################
####### INCLUDES ######
#######################

include_once('Shared_Functions.php');
$path = 'phpseclib';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
include_once('Crypt/RSA.php');

#######################
####### POST-FORM #####
#######################

$file_name = 'users.txt';
$action = get_value('action');
$success = false;

if($action == 'add_user'){
	// user submitted the form, sign up by adding to our text file
	$username =  get_value('username');
	$password = get_value('password');
	
	// configure the 16-bit RSA encryption
	$rsa = new Crypt_RSA();
	$rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
	$rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
	extract($rsa->createKey(16));
	
	// remove first and last lines from private key
	$private_key = explode("\n", $privatekey);
	array_shift($private_key);
	array_pop($private_key);
	$private_key = implode("\n", $private_key);
	
	// remove first and last lines from public key
	$public_key = explode("\n", $publickey);
	array_shift($public_key);
	array_pop($public_key);
	$public_key = implode("\n", $public_key);

	if(file_exists($file_name)){
		// file exists, grab contents so we can rewrite
		$user_text = file_get_contents($file_name);
	} else {
		// file doesnt exist, begin by creating a new file
		$user_text = '';
	}// end if the file exists
	
	// write the user to our user file
	$users_file = fopen($file_name, "w");
	$user_text .= $username . ':' . $password . ':' . $public_key . ':' . $private_key . "\n";
	fwrite($users_file, $user_text);
	$success = fclose($users_file);
}// end if $action == 'data'

#######################
####  HTML SECTION ####
#######################
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" xmlns:Name="http://www.w3.org/1999/xhtml"></script>

<title>Signup - Welcome</title>
<h1>Registration Form</h1>

<?php
	if($success){
		echo "<strong>User " . $username . " successfully created, you may now login.</strong><br><br>";
	}// end if we had success, show the success message
?>

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
</form>