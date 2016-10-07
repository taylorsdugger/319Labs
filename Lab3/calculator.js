// Calculator.js

var Calc = {

operand : 0,
answer : 0,
memory : 0, 
operator : "",
hit_equals : false,
base: 10,

View : {
  // define all of our calculator elements for decimal and binary
  textRow : {id: "textRow", type: "text", value: "", onclick:""},
  button7 : {id: "button7", type: "button", value: 7, onclick:"Calc.putValue(7);"},
  button8 : {id: "button8", type: "button", value: 8, onclick:"Calc.putValue(8);"},
  button9 : {id: "button9", type: "button", value: 9, onclick:"Calc.putValue(9);"},
  button_add : {id: "button_add", type: "button", value: "+", onclick:"Calc.operation('+');"},
  button4 : {id: "button4", type: "button", value: 4, onclick:"Calc.putValue(4);"},
  button5 : {id: "button5", type: "button", value: 5, onclick:"Calc.putValue(5);"},
  button6 : {id: "button6", type: "button", value: 6, onclick:"Calc.putValue(6);"},
  button_sub : {id: "button_sub", type: "button", value: "-", onclick:"Calc.operation('-');"},
  button1 : {id: "button1", type: "button", value: 1, onclick:"Calc.putValue(1);"},
  button2 : {id: "button2", type: "button", value: 2, onclick:"Calc.putValue(2);"},
  button3 : {id: "button3", type: "button", value: 3, onclick:"Calc.putValue(3);"},
  button_mul : {id: "button_mul", type: "button", value: "*", onclick:"Calc.operation('*');"},
  button0 : {id: "button0", type: "button", value: 0, onclick:"Calc.putValue(0);"},
  button_dec : {id: "button_dec", type: "button", value: ".", onclick:"Calc.putValue('.');"},
  button_eq : {id: "button_eq", type: "button", value: "=", onclick:"Calc.equals();"},
  button_div : {id: "button_div", type: "button", value: "/", onclick:"Calc.operation('/');"},
  button_clear : {id: "button_clear", type: "button", value: "C", onclick:"Calc.clear();"},
  button_mem_show : {id: "button_mem_show", type: "button", value: "MR", onclick:"Calc.hit_equals = true; Calc.base == 10 ? Calc.putValue(Calc.memory) : Calc.putValue(dec_to_binary(Calc.memory));"},
  button_mem_sub : {id: "button_mem_sub", type: "button", value: "M-", onclick:"Calc.memory_operation('-');"},
  button_mem_add : {id: "button_mem_add", type: "button", value: "M+", onclick:"Calc.memory_operation('+');"},
  button_mem_clear : {id: "button_mem_clear", type: "button", value: "MC", onclick:"Calc.memory = 0;"},
  button_not : {id: "button_not", type: "button", value: "~", onclick:"Calc.operation('~');"},
  button_rshift : {id: "button_rshift", type: "button", value: ">>", onclick:"Calc.operation('>>');"},
  button_lshift : {id: "button_lshift", type: "button", value: "<<", onclick:"Calc.operation('<<');"},
  button_and : {id: "button_and", type: "button", value: "&", onclick:"Calc.operation('&');"},
  button_or : {id: "button_or", type: "button", value: "|", onclick:"Calc.operation('|');"}
},// end of view 

displayElement : function (element) {
  // display an element based on its properties definied in View
  var colspan = "";
  
  if(element.id == 'textRow'){
      colspan = "colspan=5";
  }// end if our element is 'textRow' add a column span

  var s = "<td " + colspan + " ><center><input ";
  s += " id=\"" + element.id + "\"";
  s += " type=\"" + element.type + "\"";
  s += " value= \"" + element.value + "\"";
  s += " onclick= \"" + element.onclick + "\"";
  s += "></td>";
  return s;
},// end of displayElement function

display : function(type) {
  // display all the elements, seperate displays for binary and decimal calculator
  var s;
  s = "<table id=\"myTable\" border=2>";
  
  if(type == 'decimal'){
	  // display the decimal calc
	  s += "<tr>" + Calc.displayElement(Calc.View.textRow) + "</tr>";
	  
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button7);
	  s += Calc.displayElement(Calc.View.button8);
	  s += Calc.displayElement(Calc.View.button9);
	  s += Calc.displayElement(Calc.View.button_add);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button4);
	  s += Calc.displayElement(Calc.View.button5);
	  s += Calc.displayElement(Calc.View.button6);
	  s += Calc.displayElement(Calc.View.button_sub);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button1);
	  s += Calc.displayElement(Calc.View.button2);
	  s += Calc.displayElement(Calc.View.button3);
	  s += Calc.displayElement(Calc.View.button_mul);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button0);
	  s += Calc.displayElement(Calc.View.button_dec);
	  s += Calc.displayElement(Calc.View.button_eq);
	  s += Calc.displayElement(Calc.View.button_div);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button_clear);
	  s += Calc.displayElement(Calc.View.button_mem_show);
	  s += Calc.displayElement(Calc.View.button_mem_sub);
	  s += Calc.displayElement(Calc.View.button_mem_add);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button_mem_clear);
	  s += "</tr>";

	  s += "</table>";
	  // set the base to 10
	  Calc.base = 10;
  }else if(type == 'binary'){
	  // display the binary calc
	  s += "<tr>" + Calc.displayElement(Calc.View.textRow) + "</tr>";
	  
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button1);
	  s += Calc.displayElement(Calc.View.button0);
	  s += Calc.displayElement(Calc.View.button_not);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button_add);
	  s += Calc.displayElement(Calc.View.button_lshift);
	  s += Calc.displayElement(Calc.View.button_rshift);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button_sub);
	  s += Calc.displayElement(Calc.View.button_and);
	  s += Calc.displayElement(Calc.View.button_or);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button_mem_show);
	  s += Calc.displayElement(Calc.View.button_mul);
	  s += Calc.displayElement(Calc.View.button_div);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button_clear);
	  s += Calc.displayElement(Calc.View.button_mem_sub);
	  s += Calc.displayElement(Calc.View.button_mem_add);
	  s += "</tr>";
	  s += "<tr>";
	  s += Calc.displayElement(Calc.View.button_mem_clear);
	  s += Calc.displayElement(Calc.View.button_eq);
	  s += "</tr>";
	  
	  s += "</table>";
	  // set the base to 2
	  Calc.base = 2;
  }// end if display decimal or binary calc depending on type  
  return s;
},// end of display function for displaying html tables

putValue : function(n){
	// place a value into the calculators textbox, first determine if equals button was hit beforehand
	if(this.hit_equals){
		document.getElementById('textRow').value = n;
		Calc.hit_equals = false;
	}else{
		document.getElementById('textRow').value = document.getElementById('textRow').value + n;
	}// end if they hit equals previously, begin entering a new value instead of concat
},// end of putValue function for putting values in the text box

clear : function(){
	// clear the calculators textbox
	document.getElementById('textRow').value = "";
},// end of clear function

operation : function(op){
	// set the operator variable so when equals is hit, the correct operation is performed
	var bits = document.getElementById('textRow').value.length;
	
	if(Calc.base == 10){
		Calc.answer = parseFloat(document.getElementById('textRow').value);
	}else{
		Calc.answer = parseInt(document.getElementById('textRow').value, Calc.base);
	}// end if we are in base 10 or binary
	
	if(op == '~'){
		Calc.answer = eval(op + Calc.answer);
		var bit_string = dec_to_binary(Calc.answer);
		bit_string = bit_string.substring(bit_string.length - bits);
		
		Calc.operator = op;
		document.getElementById('textRow').value = bit_string;
		return;
	}// end if operator == '~'
	
	Calc.clear();
	Calc.operator = op;
	Calc.hit_equals = false;
},// end of operation function

memory_operation : function(op){
	// directly perform an operation to calculators memory 
	var val;
	
	if(Calc.base == 10){
		val = parseFloat(document.getElementById('textRow').value);
	}else{
		val = parseInt(document.getElementById('textRow').value, Calc.base);
	}// end if we are in base 10 or binary
	
	Calc.memory = eval(Calc.memory + op + val);
}, // end function memory_operation

equals : function(){
	// perform operation stored in Calc.operator, Answer = <answer> <op> <operand>
	if(Calc.base == 10){
		var op = parseFloat(document.getElementById('textRow').value);
	}else{
		var op = parseInt(document.getElementById('textRow').value, Calc.base);
	}// end if we are in base 10 or binary
	
	if(Calc.hit_equals){
		// theyve hit equals before, perform operation on stored operand
		op = Calc.operand;
	}else{
		// they havent hit equals, store the operand for operation later
		Calc.operand = op;
	}// end if hitting equals more than once, perform same operation on same operand
	
	operand_length = dec_to_binary(op).length;
	Calc.answer = eval(Calc.answer + Calc.operator + op);
	
	if(Calc.base == 10){
		document.getElementById('textRow').value = Calc.answer;
	}else{
		to_display = dec_to_binary(Calc.answer);
		to_display = pad(to_display, operand_length);
		document.getElementById('textRow').value = to_display;
	}// end if we are in base 10 or base 2
	
	Calc.hit_equals = true;
}// end of equals function

} // end of Calc;

function dec_to_binary(dec){
	// convert decimal to binary
    return (dec >>> 0).toString(2);
}// end function for converting decimals to binary strings

function pad(n, width, z) {
  // pad binary strings with zeros if neccessary
  z = z || '0';
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}// end function for padding zeros back onto binary strings