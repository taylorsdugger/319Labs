<?php
#######################
####### INCLUDES ######
#######################
include_once('Shared_Functions.php');

$file_name = 'messages.txt';
$ajax_action = get_value('ajax_action');
$username = get_value('user');

if($ajax_action == 'view'){
	
	if(file_exists($file_name)){
		$messages_text = file_get_contents($file_name);
	}else{
		$messages_text = '';
	}
	
	
	$messages_data = explode("\n", $messages_text);
	$messages = array();
	
	foreach($messages_data as $message){
		// unserialize the data before we can do anything with it
		$message = unserialize($message);
		
		if(!isset($message['sender'])){
			break;
		}// end if we've reached the end of the file data
		
		if($message['reciever'] == $username){
			$messages[] = $message;
		}// end if this message is for the person that is signed in
		
	}// end foreach loop over all our messages
	
	if(count($messages) == 0){
		// we dont have messages, return an error
		echo -1;
		exit;
	}else{
		// we have messages, first get the users private key
		$private_key = get_key($username, 'priv_key');
		
		foreach($messages as $message){
			$content = rsa_decrypt($message['body'], $private_key);
?>			
		<table border="1" width="45%">
		<tr>
			<td>Sent by: <?= $message['sender'] ?></td>
		<tr>
		<tr>
			<td><?= $content ?></td>
		</tr>
		</table>
		<br>
<?php			
		}// end foreach loop over all messages we have
	}// end if we dont have any messages
	
}// end if ajax_action == 'view'