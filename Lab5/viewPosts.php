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

$posts_data = explode("\n", $posts_text);
$id = 0;

foreach($posts_data as $post){
	$post = json_decode($post, true);
	
	if($post['id'] > $id){
		$id = $post['id'] + 1;
	}// end if find the highest id in the file
	
}// end foreach loop for finding the highest id in the file

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
<div id="posts_table"></div>
<br>
<br>
<button id="button" type="button">Make a Post</button>
<br>
<br>
<br>
<a href="logout.php">Logout</a>

<div id="dialog-form">
    <form onsubmit="return false;">
		<input type="hidden" id="post_id" value="<?= $id ?>">
		<label for="post_title">Title:</label>
		<br>
		<input size="30" type="text" id="post_title" name="post_title" class="text ui-widget-content ui-corner-all"></textarea>
		<br>
		<br>
        <label for="name">Content:</label>
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
		buttons: {
			"Post": function() {

				if($('#post_title').val() == ''){
					alert('Post title cannot be blank.');
				}else if($('#post_content').val() == ''){
					alert('Post content cannot be blank');
				}else{
					updatePosts('update');
					$(this).dialog("close");
				}
			},
			"Cancel": function() {
				$(this).dialog("close");
			}
		}
	});
	
	updatePosts('');
	eval(document.getElementById('ajax_js').innerHTML);
});

$('#button').click(function() {
   $('#dialog-form').dialog('option', 'title', 'Create a Post');
   $('#post_title').val('');
   $('#post_content').val('');
   $( "#dialog-form" ).dialog( "open" );
});

function updatePosts(action){
	
	if(action == 'delete'){
		// action is a delete
		var post_id = this.id
	}else if(typeof this.id != 'undefined'){
		// action is an edit
		var post_id = this.id;
	}else{
		// action is a create
	    var post_id = parseInt(document.getElementById('post_id').value);
	}
	
	var post_title = $('#post_title').val();
	var post_content = $('#post_content').val();	
	
	var username = "<?=$username?>";
	var today = new Date();
	today = Date.parse(today) / 1000;
	
	// perform the actual ajax call
	$.get( "updatePosts.php", { ajax_action: action, title: post_title, content: post_content, user: username, date: today, id: post_id }, function(data){
		// this function is called after a successfull ajax request
		// in this case `data` is our html table after updating the posts file

		if(data == -1){
			document.getElementById('posts_table').innerHTML = "There are currently no posts.";
		}else{
			document.getElementById('posts_table').innerHTML = data;
		}
		eval(document.getElementById('ajax_js').innerHTML);
	});
		
	// reset the forms message
	$('#post_title').val('');
	$('#post_content').val('');
	
	if(action != 'delete' && typeof this.id == 'undefined' && action != ''){
		// action is a create, increment post id
		$('#post_id').val(post_id + 1);
	}
	
}// end function for updating posts

</script>