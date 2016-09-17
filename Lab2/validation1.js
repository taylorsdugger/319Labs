function validation(){
  
  last_name = document.getElementById('last_name').value;
  first_name = document.getElementById('first_name').value;
  gender = document.getElementById('gender').value;
  state = document.getElementById('state').value;
  all_inputs_valid = true;

  if( /[^a-zA-Z0-9]/.test( first_name ) || first_name == '') {
	// first name is not alphanumeric
       document.getElementById('first_name_wrong').style.display = "block";
       document.getElementById('first_name_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	// first name is alphanumeric
       document.getElementById('first_name_correct').style.display = "block";
       document.getElementById('first_name_wrong').style.display = "none"; 
  }

  if( /[^a-zA-Z0-9]/.test( last_name ) || last_name == '') {
	// last name is not alphanumeric
       document.getElementById('last_name_wrong').style.display = "block";
       document.getElementById('last_name_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	// last name is alphanumeric
       document.getElementById('last_name_correct').style.display = "block";
       document.getElementById('last_name_wrong').style.display = "none"; 
  }

  if(gender == '') {
	// no gender
       document.getElementById('gender_wrong').style.display = "block";
       document.getElementById('gender_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	// gender
       document.getElementById('gender_correct').style.display = "block";
       document.getElementById('gender_wrong').style.display = "none"; 
  }

  if(state == '') {
	// no state
       document.getElementById('state_wrong').style.display = "block";
       document.getElementById('state_correct').style.display = "none";
       all_inputs_valid = false;
  }else{
	// state
       document.getElementById('state_correct').style.display = "block";
       document.getElementById('state_wrong').style.display = "none"; 
  }

  if(all_inputs_valid){
    setTimeout(function(){
         setCookie("first_name", first_name);
         setCookie("last_name", last_name);
         setCookie("gender", gender);
         setCookie("state", state);
       	 
    	 window.location = "validation2.html";
     }, 750);
  }
}

function setCookie(cname, cvalue){
  document.cookie = cname + "=" + cvalue + ";";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
