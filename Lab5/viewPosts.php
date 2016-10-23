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
<table width="45%">
	<tr>
	<td>
		<h1>Dashboard &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="logout.php"><button>Logout</button></a>&nbsp;&nbsp;<input type="button" id="inbox" onclick="viewInbox(); return false;" value="Inbox"></h1> 
		
		<?php
		if($username != 'admin'){	
?>		
			
<?php
   }// end if username == 'admin'
?>  
	</td>
	</tr>
	<tr>
		<td>
			Welcome <?=$username?>!
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
			<button id="post" type="button">Create a Post</button>
<?php
	if($username != 'admin'){
?>			
			<button id="message" type="button">Send a Message</button>
			
<?php
	}// end if username != admin
?>	
		</td>
	</tr>	
</table>

<div id="dialog-form">
    <form onsubmit="return false;">
		<label id="title" for="post_title">Title:</label>
		<br>
		<input size="30" type="text" id="post_title" name="post_title" class="text ui-widget-content ui-corner-all">
		<div style="display:none" id="users_selection"> 
			<?php get_users_html(); ?>
		</div>
		<br id="extra_spc">
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
					
					if($('#ok').button('option', 'label') == 'Post' || $('#ok').button('option', 'label') == 'Save'){  
						
						if($('#post_title').val() == ''){
							alert('Title cannot be blank.');
						}else if($('#post_content').val() == ''){
							alert('Content cannot be blank');
						}else{
							updatePosts('update');
							$(this).dialog("close");
						}
						
					}else{
						if($('#users_selection_box').val() == ''){
							alert('Receiver cannot be blank.');
						}else if($('#post_content').val() == ''){
							alert('Content cannot be blank');
						}else{
							updatePosts('');
							$(this).dialog("close");
						}
					}
                  }
              },
			  {
                  text: "Cancel",
                  click: function() {
					$("#dialog-form").data('action', '');
                    $(this).dialog("close");
                  }
              }              
           ]
	});
	
	updatePosts('');
	document.getElementById('message').style.display = 'none';
});

$('#post').click(function() {
   $('#dialog-form').dialog('option', 'title', 'Create a Post');
   $('#title').html('Title:');
   $('#content').html('Content:');
   document.getElementById('post_title').style.display = '';
   document.getElementById('users_selection').style.display = 'none';
   document.getElementById('extra_spc').style.display = '';
   $('#ok').button('option', 'label', 'Post');
   
   $('#post_title').val('');
   $('#post_content').val('');
   $("#dialog-form").data('action', 'post').dialog("open");
});

$('#message').click(function() {
   $('#dialog-form').dialog('option', 'title', 'Send a Message');
   $('#title').html('Receiver:');
   document.getElementById('post_title').style.display = 'none';
   document.getElementById('extra_spc').style.display = 'none';
   document.getElementById('users_selection').style.display = '';
   $('#content').html('Body:');
   $('#ok').button('option', 'label', 'Send');
   
   $('#users_selection_box').val('');
   $('#post_content').val('');
   $("#dialog-form").data('action', 'message').dialog("open");
});

function updatePosts(ajx_action){
	var username = "<?=$username?>";
	var action = $("#dialog-form").data('action');
	
	if(username != 'admin'){
		var inbox_button = document.getElementById('inbox');
		
		if(inbox_button.value == 'View Posts' && action != 'message'){
			// the request came from the inbox, switch to posts page
			inbox_button.value = 'Inbox';
			inbox_button.onclick = function(){ viewInbox(); } ;
			document.getElementById('post').style.display = '';
			document.getElementById('message').style.display = 'none';
			$("#dialog-form").data('action', '');
		}
	}
	
	if(ajx_action == 'delete' || action == 'edit'){
		// action is a delete or an edit, use the id from the clicked cell
		var post_id = this.id
	}
	
	if(action == 'post' || action == 'edit'){
		var post_title = $('#post_title').val();
	}else{
		var post_title = $('#users_selection_box').val();
	}
	
	var post_content = $('#post_content').val();	
	
	var today = new Date();
	today = Date.parse(today) / 1000;
	
	if(action == 'message'){
		// perform the ajax call for sending a message
		$.get( "sendmessage.php", { ajax_action: 'send', reciever: post_title, sender: username, body: post_content}, function(data){
			// this function is called after a successfull ajax request
			if(data == -1){
				alert("Message failed. Receiver is not a valid user.");
				$("#dialog-form").data('action', 'message').dialog("open");
				$("#dialog-form").data('action', '');
			}else{				
				alert('Message has been sent.');
				viewInbox();
				$("#dialog-form").data('action', '');
			}
		});
	}else{
		// perform the ajax call for updating a post
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
	}	
				
}// end function for updating posts

function viewInbox(){
	var inbox_button = document.getElementById('inbox');
    $("#dialog-form").data('action', '');
	
	if(inbox_button.value == 'Inbox'){
		inbox_button.value = 'View Posts';
		inbox_button.onclick = function(){ updatePosts(''); } ;
		document.getElementById('post').style.display = 'none';
		document.getElementById('message').style.display = '';
	}
	
	var username = "<?=$username?>";
	// perform the ajax call for updating a post
	$.get( "inbox.php", { ajax_action: 'view', user: username}, function(data){
		// this function is called after a successfull ajax request
		// in this case `data` is our html table after updating the posts file

		if(data == -1){
			document.getElementById('posts_table').innerHTML = "You currently have no messages.";
		}else{
			document.getElementById('posts_table').innerHTML = data;
		}
	});
	
}// end function for viewing the inbox

</script>
<?php
#######################
####  PHP FUNCTIONS  ##
#######################

function get_users_html(){
	// create a selection box for the users in our users.txt file, this is used for sending messages
	$users_name = 'users.txt';
	
	if(file_exists($users_name)){
		$users_text = file_get_contents($users_name);
	}else{
		$users_text = '';
	}
	
	$users_data = explode("\n", $users_text);
?>
	<select name="users" id="users_selection_box">
	<option value=""></option>
<?php	
	foreach($users_data as $user){
		$user = json_decode($user, true);
		
		if(isset($user['username']) && $user['username'] != 'admin'){
?>
		<option value="<?= $user['username']?>"><?= $user['username']?></option>
<?php	
		}// end if we have a user
	}// end foreach loop over all user data
?>	
	</select>
<?php	
}// end function for users selection box