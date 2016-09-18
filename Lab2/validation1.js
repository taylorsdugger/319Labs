function validation(){

  // get our variable elements from the DOM
  last_name = document.getElementById('last_name').value;
  first_name = document.getElementById('first_name').value;
  gender = document.getElementById('gender').value;
  state = document.getElementById('state').value;
  all_inputs_valid = true;

  if( /[^a-zA-Z0-9]/.test( first_name ) || first_name == '') {
	     // invalid first name
       document.getElementById('first_name_wrong').style.display = "block";
       document.getElementById('first_name_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	     // valid first name
       document.getElementById('first_name_correct').style.display = "block";
       document.getElementById('first_name_wrong').style.display = "none";
  }// end if testing for alphanumeric in first_name

  if( /[^a-zA-Z0-9]/.test( last_name ) || last_name == '') {
	     // invalid last name
       document.getElementById('last_name_wrong').style.display = "block";
       document.getElementById('last_name_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	     // valid last name
       document.getElementById('last_name_correct').style.display = "block";
       document.getElementById('last_name_wrong').style.display = "none";
  }// end if checking for alphanumeric in last_name

  if(gender == '') {
	     // no gender
       document.getElementById('gender_wrong').style.display = "block";
       document.getElementById('gender_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	     // gender
       document.getElementById('gender_correct').style.display = "block";
       document.getElementById('gender_wrong').style.display = "none";
  }// end if they entered a gender

  if(state == '') {
	     // no state
       document.getElementById('state_wrong').style.display = "block";
       document.getElementById('state_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	     // state
       document.getElementById('state_correct').style.display = "block";
       document.getElementById('state_wrong').style.display = "none";
  }// end if they entered a state

  if(all_inputs_valid){
    // all inputs validated as correct, sleep to let user see the 'correct' images
    setTimeout(function(){
         // store all of our data in a cookie
         setCookie("first_name", first_name);
         setCookie("last_name", last_name);
         setCookie("gender", gender);
         setCookie("state", state);

         // finally, redirect to validation2.html
    	   window.location = "validation2.html";
     }, 750);
  }// end if all the inputs have been validated as correct
}// end function for validation in validation1.html

function setCookie(cname, cvalue){
  document.cookie = cname + "=" + cvalue + ";";
}// end function used for setting a cookie

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];

        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }// end while loop removing spaces

        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }// end if we found the cookie, return

    }// end for loop over cookies
    return "";
}// end function used for getting a cookie using the cookies name
