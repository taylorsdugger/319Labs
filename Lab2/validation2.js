function validation(){

  // get out variables from the DOM
  email = document.getElementById('email').value;
  phone = document.getElementById('phone').value;
  address = document.getElementById('address').value;

  all_inputs_valid = true;
  at_symbol_index = email.indexOf("@");
  period_symbol_index = email.indexOf(".");

  // check validity of the email
  if(at_symbol_index == -1 || (email.length-1) == at_symbol_index || at_symbol_index == 0 || period_symbol_index < at_symbol_index || period_symbol_index == -1){
      // invalid email
      document.getElementById('email_wrong').style.display = "block";
      document.getElementById('email_correct').style.display = "none";
      all_inputs_valid = false;
  }else{
     // valid email
     // remove the '@' symbol, '.' symbol,  and check for alphanumeric
     email = email.removeChar(at_symbol_index);
     period_symbol_index = email.indexOf(".");
     email = email.removeChar(period_symbol_index);

     if( /[^a-zA-Z0-9]/.test( email ) || email == '') {
	       // invalid email
         document.getElementById('email_wrong').style.display = "block";
         document.getElementById('email_correct').style.display = "none";
         all_inputs_valid = false;
      }else{
       	 // valid email
         document.getElementById('email_correct').style.display = "block";
         document.getElementById('email_wrong').style.display = "none";
      }// end if checking for alphanumeric in email
  }// end if we have an invalid email

  // check validity of phone number
  if(phone.length != 10 && phone.length != 12){
      // invalid phone
      document.getElementById('phone_wrong').style.display = "block";
      document.getElementById('phone_correct').style.display = "none";
      all_inputs_valid = false;
  }else{
     // we could potentially have a valid phone #, do more checks
     if(phone.length == 12 && (phone.substring(3,4) != '-' || phone.substring(7,8) != '-')){
       // invalid phone
       document.getElementById('phone_wrong').style.display = "block";
       document.getElementById('phone_correct').style.display = "none";
       return 0;
     }// end if phone contains '-' characters, make sure they are in correct position

     for(i = 0; i < phone.length; i++){
         // delete any characters that aren't numbers from the phone number
         phone = phone.replace(/[^0-9]/, '');
     }// end for loop over all elements in phone string

     if(phone.length != 10){
         // invalid phone
         document.getElementById('phone_wrong').style.display = "block";
         document.getElementById('phone_correct').style.display = "none";
         all_inputs_valid = false;
     }else{
        // valid phone
        document.getElementById('phone_correct').style.display = "block";
        document.getElementById('phone_wrong').style.display = "none";
     }// end if phone.length != 10
  }// end if phone.length != 10 && phone.length != 12

  // check validity of address field
  state = address.substring(address.lastIndexOf(',')+1).trim();
  if(!address.includes(",") || state.length != 2 || address == '' || /[^a-zA-Z0-9]/.test( address.replace(/,/g, "") )){
      // invalid address
      document.getElementById('address_wrong').style.display = "block";
      document.getElementById('address_correct').style.display = "none";
      all_inputs_valid = false;
  }else{
      // valid address
      document.getElementById('address_correct').style.display = "block";
      document.getElementById('address_wrong').style.display = "none";
  }// end if we have a valid address

  if(all_inputs_valid){
    // all inputs have been validated, sleep for a little so user can see images update
    setTimeout(function(){
        deleteAllCookies();
        localStorage.address = address;
        window.location = "map.html";
     }, 750);

  }// end if all the inputs have been validated as correct
}// end of validation function for validation2.html

String.prototype.removeChar=function(index) {
    return this.substr(0, index) + this.substr(index+1);
}// end prototype string function for removing a char at an index

function deleteAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
    	var cookie = cookies[i];
    	var eqPos = cookie.indexOf("=");
    	var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    	document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }// end for loop over all cookies
}// end function for deleting all cookies
