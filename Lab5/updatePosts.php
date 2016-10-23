<?php
session_start();
#######################
###   PHP/AJAX      ###
#######################
date_default_timezone_set('America/Chicago');
include_once('Shared_Functions.php');
date_default_timezone_set(date_default_timezone_get());
ini_set('date.timezone', date_default_timezone_get());

$ajax_action = get_value('ajax_action');
$file_name = "posts.txt";
$new_post_data['username'] = $username = get_value('user');

if($ajax_action == 'delete'){
	// we recieved an ajax request for deleting a post
	$id = get_value('id');
	$contents = file_get_contents($file_name);
	$index = -1;
	
	// transform file data into array
	$file_data = explode("\n", $contents);
	
	foreach($file_data as $key => $data){
		$data = json_decode($data, true);
		
		if($data['id'] == $id){
			$index = $key;
		}// end if we found the id to delete
		
	}// end foreach loop over all file data
	
	unset($file_data[$index]);
	$contents = implode("\n", $file_data);
	file_put_contents($file_name, $contents);
	// update our $_SESSION id after the deletion
	$_SESSION['id'] = get_max_id($file_name);

}else if($ajax_action == 'update'){
	// we recieved an ajax request for updating posts, grab our request variables
	$new_post_data['title'] = $title = get_value('title');
	// replace any new lines in the content with <br/>, this is so we can use the new line
	// character as a delimiter when reading from our file
	$new_post_data['content'] = $content = strip_tags(str_replace("\n", "<br>", get_value('content')), '<br>');
	$new_post_data['date'] = $date = get_value('date');
	
	if(get_value('id') === NULL){
		// this is a create, update our session id (auto-incrementing key)
		$_SESSION['id'] = get_max_id($file_name);
		$new_post_data['id'] = $id = $_SESSION['id'];
	}else{
		// this is an edit, use the request id
		$new_post_data['id'] = $id = get_value('id');
	}// end if id is null
		
	$update = false;
	$file_contents = '';
	$posts_file = false;
	
	// check if file exists and open it
	if(file_exists($file_name)){
		$posts_file = fopen($file_name, "r");
	}// end if the posts file exists
	
	// begin reading from posts file
	if ($posts_file) {
		// we have a users file
		while (($line = fgets($posts_file)) !== false) {
			// loop over all users in posts.txt
			$post_data = json_decode($line, true);
			
			if($post_data['id'] == $id){
				// we found a post already posted by this user, modify it
				$update = true;
				$file_contents .= json_encode($new_post_data) . "\n";
			}else{
				// we didnt find our username, continue adding posts to the file contents
				$file_contents .= $line;
			}// end if they should be granted access
			
		}// end while loop over lines in the file
		
		if(!$update){
			// if there wasnt an update we need to insert a new line
			$file_contents .= json_encode($new_post_data) . "\n";
		}// end if there wasnt an update
		
		file_put_contents ($file_name, $file_contents);
		fclose($posts_file);
	}else{
		// creating a new file
		file_put_contents($file_name, json_encode($new_post_data) . "\n");
	}// end if we have a users file
	
}// end if ajax_action == 'update'

if(!isset($file_contents) || $file_contents == ''){
	
	if(file_exists($file_name)){
		// file exists grab the contents
		$file_contents = file_get_contents($file_name);
		
		if($file_contents == ''){
			echo -1;
			exit;
		}// end if file exists but no contents
	}else{
		echo -1;
		exit;	
	}// end if our posts file exists
	
}// end if the file_contents are not set, grab them now

$h = "5";
$hm = $h * 60; 
$ms = $hm * 60;	

$file_data = explode("\n", $file_contents);
$posts = array();

foreach($file_data as $post){
	// set up posts array
	$post = json_decode($post, true);
	
	if(!isset($post['id'])){
		break;
	}// end if we've reached the end of the file
	
	$posts[] = $post;
}// end foreach loop over all our file data, setting in posts array

// sort our array by most recent date
usort($posts, 'date_cmp');

#######################
####  HTML SECTION ####
#######################
foreach($posts as $post){
	// begin foreach loop over all our posts
?>
	<table width="100%" border="1">
	<thead>
		<th id="<?= $post['id']?>_title"><?= $post['title'] ?></th>
		<th id="<?= $post['id']?>_username">Posted by: <?= $post['username'] ?></th>
		<th>Updated: <?= gmdate('m/d/y', $post['date']-$ms) ?> at <?= gmdate('g:i A', $post['date']-$ms) ?></th>
<?php
	if($username == 'admin'){
?>
		<th class="delete" id="<?= $post['id'] ?>">Delete</th> 
<?php
	}else{
?>
		<th class="edit" id="<?= $post['id'] ?>">Edit</th> 
<?php
	}// end if username == 'admin'
?>
	</thead>
	<tr>
		<td id="<?= $post['id']?>_content" colspan="4"><?= $post['content'] ?> </td>
	</tr>
		
	</table>
	<br/>
<?php		
}// end foreach loop over all our posts creating the table

#######################
##   PHP FUNCTIONS   ## 
#######################
function date_cmp($a, $b){
	// function for sorting by most recent date
	if ($a['date'] == $b['date']) {
        return 0;
    }
    return ($a['date'] > $b['date']) ? -1 : 1;
}// end function for comparing date values

function get_max_id($file_name){
	// function for getting the max id from the file, auto-increments so we don't
	// create duplicate id records
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
		
		if(isset($post['id']) && $post['id'] >= $id){
			$id = $post['id'] + 1;
		}// end if find the highest id in the file
	
	}// end foreach loop for finding the highest id in the file
	
	return $id;
}// end function for getting the max id from the posts array

#######################
####  JAVASCRIPT   ####
#######################
?>
<script id="ajax_js" type="text/javascript">
    $('.delete').click(function() {
		
		if(confirm("Are you sure you want to delete this post?")){
			id = this.id;
			updatePosts('delete');
		}
    });
	
	$('.edit').click(function() {
		id = this.id;
		
		var title = document.getElementById(id + '_title').innerHTML;
		var content = document.getElementById(id + '_content').innerHTML;
		var username = document.getElementById(id + '_username').innerHTML.substring(11);
		
		if(username == "<?= $_SESSION['user'] ?>"){
			$('#post_title').val(title);
			$('#post_content').val(content.substring(0, content.length-1));
			$('#dialog-form').dialog('option', 'title', 'Edit a Post');
			document.getElementById('post_title').style.display = '';
			document.getElementById('users_selection').style.display = 'none';
			document.getElementById('extra_spc').style.display = '';
			
			$('#title').html('Title:');
			$('#content').html('Content:');
			
			 $('#ok').button('option', 'label', 'Save');
			$("#dialog-form").data('action', 'edit').dialog("open");
		}else{
			alert("You cannot edit someone else's post.");
		}	
    });
</script>
<style>
table th {
	font-weight:bold;
}
</style>