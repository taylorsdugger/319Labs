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

if(get_value('id', $_SESSION) === NULL){
	$_SESSION['id'] = 0;
}// end if session id is not set, set it now

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
<table id="main_table" width="45%">
<tr>
	<td>
		<h1>View Posts</h1> 
	</td>
	<td>
		<center><a href="logout.php"><button>Logout</button></a></center>
	</td>
	<tr>
		<td>
			Welcome <?=$username?>!
		</td>
	</tr>
	<tr>
		<td>
			Note: HTML is allowed when creating posts.
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><div id="posts_table"></div></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<button id="post" type="button">Create a Post</button>&nbsp;&nbsp;
			<button id="message" type="button">Send a Message</button>
		</td>
		<td>
	</tr>	
</table>

<div id="dialog-form">
    <form onsubmit="return false;">
		
		<label id="title" for="post_title">Title:</label>
		<br>
		<input size="30" type="text" id="post_title" name="post_title" class="text ui-widget-content ui-corner-all"></textarea>
		<br>
		<br>
        <label id="content" for="name">Content:</label>
		<br>
		<textarea rows="7" cols="45" id="post_content" name="post_content" class="text ui-widget-content ui-corner-all"></textarea>
    </form>
</div>
<?	
#######################
####  JAVASCRIPT   ####
#######################
?>
<script>
$( document ).ready(function() {
	//Initialize dialog
	$("#dialog-form").dialog({
		title: "Create a Post",
		autoOpen: false,
		resizable: false,
		modal: true,
		width: 400,
		height: 360,
		buttons:
            [
              {
                  text: "Post",
				  id: "ok",
                  click: function() {
					  
                    if($('#post_title').val() == ''){
						if($('#ok').button('option', 'label') == 'Post'){
							alert('Title cannot be blank.');
						}else{
							alert('Reciever cannot be blank.');
						}	
					}else if($('#post_content').val() == ''){
						alert('Content cannot be blank');
					}else{
						updatePosts('update');
						$(this).dialog("close");
					}
                  }
              },
			  {
                  text: "Cancel",
                  click: function() {
                    $(this).dialog("close");
                  }
              }              
           ]
	});
	
	updatePosts('');
	eval(document.getElementById('ajax_js').innerHTML);
});

$('#post').click(function() {
   $('#dialog-form').dialog('option', 'title', 'Create a Post');
   $('#title').html('Title:');
   $('#content').html('Content:');
   $('#ok').button('option', 'label', 'Post');
   
   $('#post_title').val('');
   $('#post_content').val('');
   $("#dialog-form").data('action', 'post').dialog("open");
});

$('#message').click(function() {
   $('#dialog-form').dialog('option', 'title', 'Send a Message');
   $('#title').html('Receiver:');
   $('#content').html('Body:');
   $('#ok').button('option', 'label', 'Send');
   
   $('#post_title').val('');
   $('#post_content').val('');
   $("#dialog-form").data('action', 'message').dialog("open");
});

function updatePosts(ajx_action){
	var action = $("#dialog-form").data('action');
	
	if(ajx_action == 'delete' || action == 'edit'){
		// action is a delete or an edit, use the id from the clicked cell
		var post_id = this.id
	}
	
	var post_title = $('#post_title').val();
	var post_content = $('#post_content').val();	
	
	var username = "<?=$username?>";
	var today = new Date();
	today = Date.parse(today) / 1000;
	
	// perform the actual ajax call
	$.get( "updatePosts.php", { ajax_action: ajx_action, title: post_title, content: post_content, user: username, date: today, id: post_id }, function(data){
		// this function is called after a successfull ajax request
		// in this case `data` is our html table after updating the posts file

		if(data == -1){
			document.getElementById('posts_table').innerHTML = "There are currently no posts.";
		}else{
			document.getElementById('posts_table').innerHTML = data;
		}
		eval(document.getElementById('ajax_js').innerHTML);
	});
				
}// end function for updating posts

</script>