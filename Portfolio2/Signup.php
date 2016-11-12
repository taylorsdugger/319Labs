<?php
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

$default_file_upload_size = 50 * pow(1024,2); // 50 MB
$default_file_uploads = 20;
$ajax_action = get_value('ajax_action');

if($ajax_action == 'add_user'){
	// user submitted the form, begin validating the post data
	$required = array('username', 'password', 'confirm_password', 'email_1', 'email_2', 'email_3', 'first_name', 'last_name');
	$alphanumeric = array('username', 'email_1', 'email_2', 'email_3', 'first_name', 'last_name');			
	
	foreach($required as $require){
		// begin loop over all our required fields
		if(get_value($require) == '' || get_value($require) == NULL){
			$error = translate($require) . ' is a required field.';
			break;
		}// end if we found a required value in the post that wasnt filled out
		
	}// end foreach loop over our required array
	
	if(!isset($error) || $error == NULL){
		// made it past required field checks, continue custom validation
				
		if(get_value('password') != get_value('confirm_password')){
			$error = 'Password and confirmation password must match.';
		}// end if password is not equal to confirm password
		
		foreach($alphanumeric as $alpha){
			// begin loop over fields that need to be alphanumeric
			if(!ctype_alnum(get_value($alpha))){
				$error = translate($alpha) . ' must only contain alphanumeric characters.';
				break;
			}// end if we found a required value in the post that wasnt filled out
		
		}// end foreach over alphanumeric fields
		
	}// end if we don't have an error, continue the custom validation
	
	if(!isset($error) || $error == NULL){
		$email = get_value('email_1') . '@' . get_value('email_2') . '.' . get_value('email_3');
		// we made it past all validation, insert this user into the database
				
		if(get_value('admin') == 0){
			$admin_sql = '';
			$upload_sql = "max_file_upload_size = '".mysqli_real_escape_string($conn, $default_file_upload_size)."',
						   max_file_uploads = '".mysqli_real_escape_string($conn, $default_file_uploads)."',";
		}else{
			$admin_sql = "admin = '".mysqli_real_escape_string($conn, get_value('admin'))."',";
			$upload_sql = '';
		}// end if user is an admin
		
		mysqli_query($conn, 
		"INSERT INTO users SET 
				user_name = '".mysqli_real_escape_string($conn, get_value('username'))."',
				password = '".mysqli_real_escape_string($conn, md5(get_value('password')))."',
				email = '".mysqli_real_escape_string($conn, $email)."'," . 
				$admin_sql . 
				$upload_sql . 
				"first_name = '".mysqli_real_escape_string($conn, get_value('first_name'))."',
				last_name = '".mysqli_real_escape_string($conn, get_value('last_name'))."'");
				
				echo mysqli_error($conn);
	}else{
		echo $error;
	}// end if we still dont have an error
	
	exit;
}// end if ajax_action == 'add_user'

#######################
####  HTML SECTION ####
#######################
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" xmlns:Name="http://www.w3.org/1999/xhtml"></script>

<title>Signup - Welcome</title>
<h1>File Storage Registration Form</h1>

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
	<td><label for="confirm_password">Confirm Password:</label></td>
	<td><input id="confirm_password" type="password" name="confirm_password"></td>
</tr>
<tr>
	<td><label for="email">Email:</label></td>
	<td>
		<input id="email_1" type="text" name="email_1" size="10"> @ 
		<input id="email_2" type="text" name="email_2" size="6"> .
		<input id="email_3" type="text" name="email_3" size="3">
	</td>
</tr>
<tr>
	<td><label for="admin">Admin:</label></td>
	<td><input id="admin" type="checkbox" name="admin" value="1" <?= (get_value('admin') != NULL) ? 'checked' : '' ?>> &nbsp;&nbsp; (Admins have unlimited storage)</td>
	
</tr>
<tr>
	<td><label for="first_name">First Name:</label></td>
	<td><input id="first_name" type="text" name="first_name"></td>
</tr>
<tr>
	<td><label for="last_name">Last Name:</label></td>
	<td><input id="last_name" type="text" name="last_name"></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><input type="submit" onclick="signup(); return false;" name="submit" value="Sign Up"></td>
</tr>
</table>

</form>
<br>
<br>
Already signed up? <a href="Login.php">Click here</a> to login.

<!-- JAVASCRIPT -->
<script>
function signup() {
	
	if(document.getElementById('admin').checked){
		var admin_checked = 1;
	}else{
		var admin_checked = 0;
	}
	
	$.post( "Signup.php", { ajax_action: 'add_user', 
							username: $("#username").val(), 
							password: $("#password").val(),
							confirm_password: $("#confirm_password").val(),
							email_1: $("#email_1").val(),
							email_2: $("#email_2").val(),
							email_3: $("#email_3").val(),
							admin: admin_checked, 
							first_name: $("#first_name").val(),
							last_name: $("#last_name").val()
						  }, 
		function(data){
			alert(data);
			// ajax success function, attempted to sign a user up
			if(data == ''){
				
				window.location = "login.php";
				
			}else{
				
				document.getElementById('error').innerHTML = data;
				
			}// end if we have data
	});// end of ajax function
}
</script>
<?php
#######################
####  PHP FUCNTIONS ###
#######################

function translate($field){
	
	if($field == 'username'){
		return 'Username';
	}else if($field == 'password'){
		return 'Password';
	}else if($field == 'confirm_password'){
		return 'Confirm Password';
	}else if($field == 'email'){
		return 'Email';
	}else if($field == 'first_name'){
		return 'First Name';
	}else if($field == 'last_name'){
		return 'Last Name';
	}else if($field == 'email_1' || $field == 'email_2' || $field == 'email_3'){
		return 'Email';
	}
	
}// end function for converting field names to human readable