<?php

#########################
# PHP SHARED  FUNCTIONS # 
#########################
$path = 'phpseclib';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
include_once('Crypt/RSA.php');

function get_value($index, $arr = NULL){
	// use this function to avoid undefined errors
	
	if($arr === NULL){
		$arr = $_REQUEST;
	}// end if $arr was not set, set it to our $_REQUEST array
		
	if (isset($arr[$index])) { 
		return $arr[$index];
	} else {
		return NULL;
	}// end if isset, checking the arrays index
	
}// end get_value function

//Function for encrypting with RSA
function rsa_encrypt($string, $public_key)
{
    //Create an instance of the RSA cypher and load the key into it
    $cipher = new Crypt_RSA();
    $cipher->loadKey($public_key);
    //Set the encryption mode
    $cipher->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    //Return the encrypted version
    return $cipher->encrypt($string);
}

//Function for decrypting with RSA 
function rsa_decrypt($string, $private_key)
{
    //Create an instance of the RSA cypher and load the key into it
    $cipher = new Crypt_RSA();
    $cipher->loadKey($private_key);
    //Set the encryption mode
    $cipher->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    //Return the decrypted version
    return $cipher->decrypt($string);
}

function get_key($username, $type){
	// function for getting the public or private key for the $username
	$users_name = 'users.txt';
	$users_text = file_get_contents($users_name);
	$users_data = explode("\n", $users_text);
	$key = NULL;

	foreach($users_data as $user){
		$user = json_decode($user, true);

		if($user['username'] == $username){
			$key = $user[$type];
		}// end if we found the user in the file, grab the public key for encrypt
		
	}// end foreach loop over user data
	
	return $key;
}// end function for getting a public/private key for a user