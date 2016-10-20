<?php
session_start();
#######################
###  	   PHP      ###
#######################
include_once('Shared_Functions.php');

$file_name = 'posts.txt';
$username = get_value('user', $_SESSION);

if($username === NULL){
	echo "You must first be logged in to access this page. <a href='login.html'>Click here</a> to go to the login page.";
	exit;
}// end if username is null, they havent logged in

if(file_exists($file_name)){
	// file exists, grab contents so we can rewrite
	$posts_text = file_get_contents($file_name);
} else {
	// file doesnt exist, begin by creating a new file
	$posts_text = '';
}// end if the file exists

file_put_contents($file_name, $posts_text);

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

<title>View Posts</title>
<h1>Posts</h1>
<p>Currently logged in as: <?=$username?></p>

<button id="button" type="button">Make a Post</button>
<br>
<br>
<br>
<a href="logout.php">Logout</a>

<div id="dialog-form">
    <form onsubmit="return false;">
        <label for="name">Enter Message:</label>
		<textarea rows="3" cols="25" id="post_content" name="post_content" class="text ui-widget-content ui-corner-all"></textarea>
    </form>
</div>	

<script>
$( document ).ready(function() {
	//Initialize dialog
	$("#dialog-form").dialog({
		autoOpen: false,
		modal: true,
		maxWidth: 300,
		maxHeight: 225,
		width: 300,
		height: 225,
		buttons: {
			"Ok": function() {
				updatePosts();
				$(this).dialog("close");
			},
			"Cancel": function() {
				$(this).dialog("close");
			}
		}
	});
});

$('#button').click(function() {
   $( "#dialog-form" ).dialog( "open" );
});

function updatePosts(){
	var post_content = $('#post_content').val();
	var username = "<?=$username?>";
	var today = new Date();
	
	// perform the actual ajax call
	$.post( "updatePosts.php", { ajax_action: 'update', content: post_content, user: username, date: today });
	
	// reset the forms message
	$('#post_content').val('');
}// end function for updating posts

</script>