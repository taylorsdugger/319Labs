// Calculator.js

var Calc = {
// need to make sure variables are not global
operand : 0,
answer : 0,
memory : 0, 
operator : "",
hit_equals : false,
binary : false,

View : {
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
  button_mem_show : {id: "button_mem_show", type: "button", value: "MR", onclick:"Calc.hit_equals = true; Calc.putValue(Calc.memory);"},
  button_mem_sub : {id: "button_mem_sub", type: "button", value: "M-", onclick:"Calc.memory -= parseFloat(document.getElementById('textRow').value);"},
  button_mem_add : {id: "button_mem_add", type: "button", value: "M+", onclick:"Calc.memory += parseFloat(document.getElementById('textRow').value);"},
  button_mem_clear : {id: "button_mem_clear", type: "button", value: "MC", onclick:"Calc.memory = 0;"},
  button_not : {id: "button_not", type: "button", value: "~", onclick:"Calc.operation('~');"},
  button_mod : {id: "button_mod", type: "button", value: "%", onclick:""},
  button_rshift : {id: "button_rshift", type: "button", value: ">>", onclick:""},
  button_lshift : {id: "button_lshift", type: "button", value: "<<", onclick:""},
  button_and : {id: "button_and", type: "button", value: "&", onclick:""},
  button_or : {id: "button_or", type: "button", value: "|", onclick:""}
},// end of view 

displayElement : function (element) {
  var colspan = "";
  if(element.id == 'textRow'){
      colspan = "colspan=5";
  }

  var s = "<td " + colspan + " ><center><input ";
  s += " id=\"" + element.id + "\"";
  s += " type=\"" + element.type + "\"";
  s += " value= \"" + element.value + "\"";
  s += " onclick= \"" + element.onclick + "\"";
  s += "></td>";
  return s;
},// end of displayElement function

display_decimal : function() {
  var s;
  s = "<table id=\"myTable\" border=2>"
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
  return s;
},// end of display_decimal function

display_binary : function() {
  var s;
  s = "<table id=\"myTable\" border=2>"
  s += "<tr>" + Calc.displayElement(Calc.View.textRow) + "</tr>";
  
  s += "<tr>";
  s += Calc.displayElement(Calc.View.button1);
  s += Calc.displayElement(Calc.View.button0);
  s += Calc.displayElement(Calc.View.button_not);
  s += "</tr>";
  s += "<tr>";
  s += Calc.displayElement(Calc.View.button_add);
  s += Calc.displayElement(Calc.View.button_mod);
  s += Calc.displayElement(Calc.View.button_lshift);
  s += "</tr>";
  s += "<tr>";
  s += Calc.displayElement(Calc.View.button_rshift);
  s += Calc.displayElement(Calc.View.button_sub);
  s += Calc.displayElement(Calc.View.button_and);
  s += "</tr>";
  s += "<tr>";
  s += Calc.displayElement(Calc.View.button_or);
  s += Calc.displayElement(Calc.View.button_mul);
  s += Calc.displayElement(Calc.View.button_div);
  s += "</tr>";
  s += "<tr>";
  s += Calc.displayElement(Calc.View.button_mem_show);
  s += Calc.displayElement(Calc.View.button_mem_sub);
  s += Calc.displayElement(Calc.View.button_mem_add);
  s += "</tr>";
  s += "<tr>";
  s += Calc.displayElement(Calc.View.button_clear);
  s += Calc.displayElement(Calc.View.button_mem_clear);
  s += Calc.displayElement(Calc.View.button_eq);
  s += "</tr>";
  
  s += "</table>";
  Calc.binary = true;
  return s;
},

putValue : function(n){
	if(this.hit_equals){
		document.getElementById('textRow').value = n;
		Calc.hit_equals = false;
	}else{
		document.getElementById('textRow').value = document.getElementById('textRow').value + n;
	}
},// end of putValue function for putting values in the text box

clear : function(){
	document.getElementById('textRow').value = "";
},// end of clear function

operation : function(op){
	Calc.answer = parseFloat(document.getElementById('textRow').value);
	Calc.clear();
	Calc.operator = op;
	Calc.hit_equals = false;
},// end of operation function

equals : function(){
	var op = parseFloat(document.getElementById('textRow').value); // second operand in calculation (answer <operator> op)
	
	if(Calc.hit_equals){
		// theyve hit equals before, perform operation on stored operand
		op = this.operand;
	}else{
		// they havent hit equals, store the operand for operation later
		Calc.operand = op;
	}// end if hitting equals more than once, perform same operation on same operand

	Calc.answer = document.getElementById('textRow').value = eval(Calc.answer + Calc.operator + op);
	Calc.hit_equals = true;
}// end of equals function

} // end of Calc;