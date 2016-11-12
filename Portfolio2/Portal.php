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
####### AJAX     ######
#######################

$ajax_action = get_value('ajax_action');

if($ajax_action == 'delete'){
	$file_id = get_value('file_id');

	$file_query = mysqli_query($conn, "SELECT * FROM user_files WHERE id = '".$file_id."'");
	$file_data = mysqli_fetch_assoc($file_query);
	$file_path = getcwd() . '\uploads\\' . $file_data['user_name'] . '\\' . $file_data['file_name'];

	// actually delete the file
	unlink($file_path);
	// delete the data from our user_files table
	mysqli_query($conn, "DELETE FROM user_files WHERE id = '".$file_id."'");

	$ajax_action = 'get_uploads';
}// end if ajax_action == delete

if($ajax_action == 'get_uploads'){
	// function for creating an uploads table
	
	$uploads_query = mysqli_query($conn, "SELECT * FROM user_files WHERE user_name = '".$_SESSION['username']."' ORDER BY uploaded DESC");
	
	if(mysqli_num_rows($uploads_query) > 0){
?>
		<br>
		<table border="2" width="60%">
			<thead>
				<th>File Name</th>
				<th>Uploaded on</th>
				<th>Options</th>
			</thead>
<?php			
		while($upload = mysqli_fetch_assoc($uploads_query)){
?>			
			<tr>
				<td><center><a href="/Portfolio2/uploads/<?= $_SESSION['username'] . '/' . $upload['file_name'] ?>"><?= $upload['file_name'] ?></center></a></td>
				<td><center><?= $upload['uploaded'] ?></center></td>
				<td><center><a href="#" onclick="delete_file(<?= $upload['id'] ?>); return false;">Delete</center></a>
			</tr>
<?php	
		}// end while loop over all our file uploads
?>
		</table>
<?php		
	}else{
		echo '<br>You currently do not have any uploads at this time.';
	}// end if we have rows
	
	exit;
}// end if ajax_action == get_uploads

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
	<td><h1>File Portal</h1></td>
	<td> Welcome <?= $_SESSION['username']?>! &nbsp;&nbsp;&nbsp; <a href="logout.php"><button>Logout</button></a></td>	
</tr>
<tr>
	<td>Files you have uploaded are listed below.</td>
</tr>
<tr>
	<td>NOTE: If you upload two files with the same name, the second will replace the first.</td>
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
	  <button type="submit" id="upload-button" onclick="upload(); return false;">Upload</button>
	</form>
</td>
</tr>
</table>

<script>
$( document ).ready(function() {
    get_uploads();
});

var xhr = new XMLHttpRequest();

function upload(){
	  // function for uploading a file using ajax
	  var file = document.getElementById("file-select");
     
      // create our form data instance
      var formData = new FormData();
       
	  // add the file to our form data
      formData.append("upload", file.files[0]);
	
      // open connection to upload.php
	  xhr.open('POST', 'upload.php', true);

	  // send the file to the server
      xhr.send(formData);  
}// end function for uploading a file with ajax

/* Check the response status */  
xhr.onreadystatechange = function(){
  
  if (xhr.readyState == 4 && xhr.status == 200){
	  get_uploads();
  }
}// end readystate function

function get_uploads(){
	$.post( "Portal.php", { ajax_action: 'get_uploads' },
		 
		function(data){
			// ajax success function
			document.getElementById('uploads_table').innerHTML = data;
		}
	);
}// end ajax call for getting the uploads

function delete_file(fileId){
	$.post( "Portal.php", { ajax_action: 'delete', file_id: fileId},
		 
		function(data){
			// ajax success function
			document.getElementById('uploads_table').innerHTML = data;
		}
	);
}// end ajax call for deleting a file
</script>