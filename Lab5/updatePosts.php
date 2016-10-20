<?php
include_once('Shared_Functions.php');

$ajax_action = get_value('ajax_action');
$file_name = "posts.txt";

if($ajax_action == 'update'){
	// we recieved an ajax request for updating posts, grab our request variables
	$new_post_data[] = $content = get_value('content');
	$new_post_data[] = $username = get_value('user');
	$new_post_data[] = $date = get_value('date');
		
	// begin reading from posts file
	$posts_file = fopen($file_name, "r");
	$update = false;
	$file_contents = '';
	
	if ($posts_file) {
		// we have a users file
		while (($line = fgets($posts_file)) !== false) {
			// loop over all users in posts.txt
			$post_data = json_decode($line);
			
			if($post_data[1] == $username){
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
		fclose($users_file);
	}else{
		file_put_contents($file_name, json_encode($new_post_data));
	}// end if we have a users file

	exit;
}// end if ajax_action == 'update'