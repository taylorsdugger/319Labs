$(document).ready(function(){
	$('#login-form').attr('style','');
	$('#change_user').attr('style','display:none');
});

var lib = new Library();
var all_books = [];		// use this array so we can search through id's and find our book object
lib.createBooks();
var username;
var view;
var login = true;

function Library() {
	// private variables (shelves)
	var lit = new Shelf("Literature");
	var science = new Shelf("Science");
	var sport = new Shelf("Sport");
	var art = new Shelf("Art");
	
	// public method to get a shelf
	this.getShelf = function(shelf){

		if(shelf == 'Literature'){
			return lit;
		}else if(shelf == 'Science'){
			return science;
		}else if(shelf == 'Sport'){
			return sport;
		}else if(shelf == 'Art'){
			return art;
		}
	}// end getShelf function
	
	this.createBooks = function(){
		// create our books
		for(var i = 1; i <= 5; i++){
			var b = new Book(i, 'R');
			b.setCategory();
		}// end for loop creating reference books
		
		for(var i = 1; i <= 20; i++){
			var b = new Book(i, 'B');
			b.setCategory();
		}// end for loop creating ordinary books
	}// end function for creating books
}// end library object

function Shelf(category) {
	// private variable
	var books = [];
	
	// public variables
	this.addBook = function(book){
		books.push(book);
		all_books.push(book);
	}// end addBook function
	
	this.getBooks = function(){
		return books;
	}// end getBooks function
}// end shelf object

function Book(ID, book_type, category) {
	// private variables (var xxx)
	var BookID = ID;
	var type = book_type;
	var bCategory = category;
	var borrowedBy = null;
	var presence = 1;
	var borrowedBooks = 0;
	
	// public functions (this.xxx) (can access private variables)
	this.setCategory = function(){
		
		if (BookID % 4 == 0) {
			bCategory = "Art";
		} else if (BookID % 4 == 1) {
			bCategory = "Science";
		} else if (BookID % 4 == 2) {
			bCategory = "Sport";
		} else if (BookID % 4 == 3) {
			bCategory = "Literature";
		}
		
		shelf = lib.getShelf(bCategory);
		shelf.addBook(this);
	}// end set category function
	
	this.togglePresence = function(){
		
		if(view == 'student'){
			if(presence == 1){

				if(checkBooksBorrowed(username)){
					// borrowing a book, set borrowed by
					borrowedBy = username;
					presence = 0;
				}else{
					alert("You can only borrow 2 books at a time.");
				}	
			}else{
				if(username == borrowedBy){
					// returning a book, set borrowed by to null
					borrowedBy = null;
					presence = 1;
				}else{
					alert("No copies of " + type + BookID + " are available, it is borrowed by " + borrowedBy);
				}
			}// end if presence == 1
		}else if(view == 'admin'){
			var alert_string = type + BookID + " is on shelf " + bCategory
			
			if(borrowedBy != null){
				alert_string += " and is borrowed by " + borrowedBy;
			}
			alert_string += ".";
			
			alert(alert_string);
		}// end if view == student
		
		displayTable();
	}// end togglePresence function
	
	this.getCategory = function(){
		return bCategory;
	}// end get category function
	
	this.getBookID = function(){
		return BookID;
	}// end getBookID function
	
	this.getType = function(){
		return type;
	}// end getType function
	
	this.getPresence = function(){
		return presence;
	}// end getPresence function
	
	this.getBorrowedBy = function(){
		return borrowedBy;
	}// end getBorrowedBy function
	
}// end book object
	
function displayTable() {
	// begin the table string, create the header
	var table_string = '<table border="1" style = "width:20%" >' +
	'<thead>' + 
       '<th bgcolor="#086603">Shelf Literature</th>' + 
        '<th bgcolor="#086603">Shelf Science</th>' + 
        '<th bgcolor="#086603">Shelf Sport</th>' + 
        '<th bgcolor="#086603">Shelf Art</th>' + 
    '</thead>';
	
	if($('#username').val() == 'admin' && $('#password').val() == 'admin'){
		view = 'admin';
	}else if($('#username').val().charAt(0) == 'U'){
		view = 'student';
	}else{
		alert("Incorrect username and password.");
		return;
	}
	
	username = $('#username').val();
	login = false;

	if(!login){		
		$('#login-form').attr('style','display: none');
		$('#change_user').attr('style','');

		// to create our table, loop over all our sheleves
		var shelf_categories = ["Literature", "Science", "Sport", "Art"];
		
		var lit_cells = [];
		var sci_cells = [];
		var sprt_cells = [];
		var art_cells = [];
		
		for(i = 0; i < shelf_categories.length; i++){
			// begin loop over all shelf categories
			var shelf_category = shelf_categories[i];

			var shelf = lib.getShelf(shelf_category);
			var books = shelf.getBooks();
			
			for(j = 0; j < books.length; j++){
				// begin loop over books in this shelf	
				
				if(books[j].getPresence() == 1){
					var color = '#ffffff';
				}else{
					var color = '#ff4d4d';
				}
				
				var display = [books[j].getType() + books[j].getBookID(), color];
				
				if(shelf_category == 'Literature'){
					lit_cells.push(display);
				}else if(shelf_category == 'Science'){
					sci_cells.push(display);
				}else if(shelf_category == 'Sport'){
					sprt_cells.push(display);
				}else if(shelf_category == 'Art'){
					art_cells.push(display);
				}
			}// end for loop over books in the shelf
			
		}// end for loop over shelf categories
		
		var length = getMax(lit_cells.length, sci_cells.length, sprt_cells.length, art_cells.length);
		
		//now all of our table cells are in the cell arrays
		for(i = 0; i < length; i++){
			// art cells will always contain max number of books in all the sheleves
			var row = '<tr>';
			
			if (typeof lit_cells[i] !== 'undefined') {
				var lit = lit_cells[i];
				var lit_cell = "<td onclick='togglePresence(this);' id='" + lit[0] + "' bgcolor='" + lit[1] + "'>" + lit[0] + "</td>";
			}else{
				lit_cell = "<td>&nbsp;</td>"
			}
			
			if (typeof sci_cells[i] !== 'undefined') {
				var sci = sci_cells[i];
				var sci_cell = "<td onclick='togglePresence(this);' id='" + sci[0] + "' bgcolor='" + sci[1] + "'>" + sci[0] + "</td>";
			}else{
				sci_cell = "<td>&nbsp;</td>"
			}
			
			if (typeof sprt_cells[i] !== 'undefined') {
				var sprt = sprt_cells[i];
				var sprt_cell = "<td onclick='togglePresence(this);' id='" + sprt[0] + "' bgcolor='" + sprt[1] + "'>" + sprt[0] + "</td>";
			}else{
				sprt_cell = "<td>&nbsp;</td>"
			}
			
			if (typeof art_cells[i] !== 'undefined') {
				var art = art_cells[i];
				var art_cell = "<td onclick='togglePresence(this);' id='" + art[0] + "' bgcolor='" + art[1] + "'>" + art[0] + "</td>";
			}else{
				art_cell = "<td>&nbsp;</td>"
			}
			
			row += lit_cell + sci_cell + sprt_cell + art_cell + '</tr>';
			table_string += row;
		}// end for loop creating our html table
		
		if(view == 'admin'){
			row = '<tr>';
			var add_lit_cell = "<td onclick=\"displayPrompt('Literature');\">Add</td>";
			var add_sci_cell = "<td onclick=\"displayPrompt('Science');\">Add</td>";
			var add_sprt_cell = "<td onclick=\"displayPrompt('Sport');\">Add</td>";
			var add_art_cell = "<td onclick=\"displayPrompt('Art');\">Add</td>";
			row += add_lit_cell + add_sci_cell + add_sprt_cell + add_art_cell + "</tr>";
			table_string += row;
		}// end if view is admin	

		table_string += "</table>";
		document.getElementById('table').innerHTML = table_string;
	}// end if we are not showing login form	
}// end function for creating html table

function togglePresence(cell){
	var book_id = cell.id;
	var b = getBookByID(book_id);
	b.togglePresence();
}// end function for toggling presence on the clicked cell

function getBookByID(book_id) {

  for (var i=0; i < all_books.length; i++) {

    if ((all_books[i].getType() + all_books[i].getBookID()) == book_id){
		return all_books[i];
	}// end if we found the book 
		
  }// end for loop over all books
  return false;
}// end function for finding book by id

function checkBooksBorrowed(user){
  var borrows = 0; 
  
  for (var i=0; i < all_books.length; i++) {

    if (all_books[i].getBorrowedBy() == user){
		borrows++;
	}// end if this book is borrowed by user 
		
  }// end for loop over all books
	
  if(borrows >= 2){
	  return false;
  }else{
	  return true;
  }// end if 
}// end function for checking books borrowed

function changeUser(){
	$('#login-form').attr('style','');
	$('#change_user').attr('style','display:none');
	document.getElementById('table').innerHTML = '';
	login = true;
}// end changeUser function

function displayPrompt(category){
	var id = '';
	
	while(id == ''){
		id = prompt('Enter book id: ', 'BookID');
		
		if(id == null){
			return;
		}
	}
	
	if(getBookByID('B' + id) == false){
		var b = new Book(id, 'B', category);
		lib.getShelf(category).addBook(b);
	}else{
		alert("A book with this id alert exists in the library.");
	}// end if a book is already in the shelves
	
	displayTable();
}// end displayPrompt function

function getMax(x, y, z, k){
	var max = x;

	if(y > max){
		max = y;
	}
	if(z > max){
		max = z;
	}
	if(k > max){
		max = k;
	}
	return max;
}// end getMax of 4 numbers function