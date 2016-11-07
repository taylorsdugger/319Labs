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

$_SESSION['admin'] = is_admin($_SESSION['username']);

#######################
###  PHP AJAX        ##
#######################

$ajax_action = get_value('ajax_action');

if($ajax_action == 'upload_file'){
	echo 'testing';
	exit;
}// end if ajax_action == upload files

#######################
####  HTML SECTION ####
#######################
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" xmlns:Name="http://www.w3.org/1999/xhtml"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<title>Portal - Welcome</title>
<table width="75%">
<tr>
	<td><h1>Portal</h1></td>
	<td> Welcome <?= $_SESSION['username']?>! &nbsp;&nbsp;&nbsp; <a href="logout.php"><button>Logout</button></a>

</td>	
</tr>
<tr>
	<td id="extra_text">Files you have uploaded are listed below, click 'upload' to upload a new file.</td>
</tr>
<tr>
	<td><div id="uploads_table"></div></td>
</tr>
<tr>
	<td>&nbsp;</td>
<tr>
<td>
	<form id="file-upload" method="POST" enctype="multipart/form-data">
	  <input type="file" id="file-select" name="file"/>
	  <button type="submit" id="upload-button">Upload</button>
	</form>
</td>
</tr>
</table>

<script>

$("#file-upload").submit(function(e){
	e.preventDefault();
	var upload_file = new FormData(this);
	
	$.ajax({
		url: "Portal.php", 		  // Url to which the request is send
		type: "POST",             // Type of request to be send, called as method
		data: {'ajax_action': 'upload_file', 'file': upload_file}, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		contentType: false,       // The content type used when sending data to the server.
		cache: false,             // To unable request pages to be cached
		processData:false,        // To send DOMDocument or non processed data file it is set to false
		success: function(data)   // A function to be called if request succeeds
		{
			alert('test');
			alert(data);
		}
	});
	
	return false;
});

</script>

<?php
#######################
####  PHP FUNCTIONS ###
#######################

function is_admin($username){
	global $conn;
	$admin_query = mysqli_query($conn, "SELECT * FROM users u WHERE u.user_name = '".mysqli_real_escape_string($conn, $username)."' AND u.admin = 1");
	
	if(mysqli_num_rows($admin_query) > 0){
		return true;
	}else{
		return false;
	}// end if they are a librarian
}// end function for determining if this user is a librarian