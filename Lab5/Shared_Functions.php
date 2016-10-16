<?php

#########################
# PHP SHARED  FUNCTIONS # 
#########################

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