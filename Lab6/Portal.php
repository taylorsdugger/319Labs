<?php
session_start();
#######################
####### DATABASE ######
#######################
$conn = mysqli_connect("mysql.cs.iastate.edu", "dbu319t34", '6$KeyEqe', "db319t34");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit;
}// end if we couldnt connect to the database

#######################
####### INCLUDES ######
#######################
include_once('Shared_Functions.php');

if(get_value('username', $_SESSION) == NULL){
	echo 'You do not have access to this page, please <a href="Signup.php">create an account</a> or <a href="Login.php">login</a>.';
	exit;
}// end if the session user is not set, they dont have access

$_SESSION['librarian'] = is_librarian($_SESSION['username']);

#######################
###  PHP AJAX        ##
#######################

$ajax_action = get_value('ajax_action');

if(strpos($ajax_action, 'add_book') !== FALSE){
	// adding a book
	$shelf_id = preg_replace("/[^0-9]/", "", $ajax_action);
	$book_title = get_value('book_title');
	$book_author = get_value('book_author');
	
	// insert into books table
	mysqli_query($conn, "INSERT INTO books SET BookTitle='".mysqli_real_escape_string($conn, $book_title)."', Author = '".mysqli_real_escape_string($conn, $book_author)."'");
	$book_id = mysqli_insert_id($conn);
	// insert book into booklocations table
	mysqli_query($conn, "INSERT INTO booklocations SET BookId='".mysqli_real_escape_string($conn, $book_id)."', ShelfId = '".mysqli_real_escape_string($conn, $shelf_id)."'");
	$ajax_action = 'get_books';
}// end if we have an add books ajax call

if($ajax_action == 'delete_book'){
	// deleting a book
	$book_id = get_value('book_id');
	// delete from all 3 tables
	mysqli_query($conn, "DELETE FROM loanhistory WHERE BookId = '".mysqli_real_escape_string($conn, $book_id)."'");
	mysqli_query($conn, "DELETE FROM booklocations WHERE BookId = '".mysqli_real_escape_string($conn, $book_id)."'");
	mysqli_query($conn, "DELETE FROM books WHERE BookId = '".mysqli_real_escape_string($conn, $book_id)."'");
	$ajax_action = 'get_books';
}// end if ajax action is delete book

if($ajax_action == 'toggle_borrow'){
	// toggling the availability
	$book_id = get_value('book_id');
	
	// first get the books availability
	$avail_query = mysqli_query($conn, "SELECT Availability FROM books WHERE BookId = '".mysqli_real_escape_string($conn, $book_id)."'");
	$avail_rs = mysqli_fetch_assoc($avail_query);
	$availability = $avail_rs['Availability'];
	
	if($availability == 1){
		// we need to borrow the book
		mysqli_query($conn, "UPDATE books SET Availability = 0 WHERE BookId = '".mysqli_real_escape_string($conn, $book_id)."'");
		mysqli_query($conn, "INSERT INTO loanhistory SET username = '".mysqli_real_escape_string($conn, $_SESSION['username'])."', BookId = '".mysqli_real_escape_string($conn, $book_id)."', DueDate = DATE_ADD(CURDATE(), INTERVAL 2 WEEK)");
	}else{
		// we need to return the book
		mysqli_query($conn, "UPDATE books SET Availability = 1 WHERE BookId = '".mysqli_real_escape_string($conn, $book_id)."'");
		mysqli_query($conn, "UPDATE loanhistory SET ReturnedDate = CURDATE() WHERE BookId = '".mysqli_real_escape_string($conn, $book_id)."'");
	}// end if this book is available
	
	$ajax_action = 'get_books';
}// end if ajax action is delete book

if($ajax_action == 'get_books'){
	// get the users number of books and
?>	
	<table border="1" style = "width:100%">
		<thead>	
<?php
	$shelves_query = mysqli_query($conn, "SELECT * FROM shelves");
	$shelves = array();
	while($shelves[] = $shelf = mysqli_fetch_assoc($shelves_query)){
		// get the shelves
?>		
			<th id = "<?= $shelf['ShelfId']?>" bgcolor="#71f442"><?= $shelf['ShelfName']?></th>	
<?php		
	}// end while loop over all our shelves
	array_pop($shelves);
?>	
		</thead>
<?php
	$books = array();
	foreach($shelves as $key => $shelf){
		// foreach loop over all our shelves getting the books
		$books_query = mysqli_query($conn, "SELECT * FROM books b INNER JOIN booklocations bl USING(BookId) WHERE ShelfId = '".mysqli_real_escape_string($conn, $shelf['ShelfId'])."'");
		
		while($book = mysqli_fetch_assoc($books_query)){			
			$books[$key][] = $book;
		}// end while loop storing the books in an array
	}// end foreach loop over all shelves
	
	$book_counts = array();
	foreach($books as $book_array){
		$book_counts[] = count($book_array);
	}// end foreach loop over all books
	
	if(count($book_counts) == 0){
		$max = 0;
	}else{
		$max = max($book_counts);
	}
	
	for($i = 0; $i < $max; $i++){
?>
		<tr>
<?php		
		foreach($shelves as $shelf){
			if(isset($books[$shelf['ShelfId']][$i])){
				$book = $books[$shelf['ShelfId']][$i];
				$display = $book['BookTitle'];
				
				if($book['Availability'] == 1){
					// book is available
?>					<td id="<?= $book['BookId'] ?>" onclick="setId(this); get_book_details(); $('#view-book').dialog('open');"><?= $display?></td>
<?php	
				}else{
					// book is not available
?>					<td bgcolor="#f22929" id="<?= $book['BookId'] ?>" onclick="setId(this); get_book_details(); $('#view-book').dialog('open');"><?= $display?></td>
<?php	
				}// end if this book is available, display in a different colors
				
			}else{
				$display = '&nbsp;';
?>				<td><?= $display?></td>
<?php				
			}// end if we have a book
	
		}// end foreach loop over all shelves
?>
		</tr>
<?php		
	}// end foreach loop adding our books to the table
	
	if($_SESSION['librarian']){
?>
		<tr>
<?php	
		foreach($shelves as $shelf){
?>
			<td><center><button onclick="setId(this); $('#add-book').dialog('open');" id="add_book_<?= $shelf['ShelfId']?>">Add Book</button></center></td>	
<?php				
		}// end foreach loop over all the shelves
?>
		</tr>
<?php	
	}// end if the user is a librarian
?>
	</table>

<?php	
	exit;	
}// end if ajax action is get books

if($ajax_action == 'get_borrow_history'){
	$username = get_value('user');
	$loan_query = mysqli_query($conn, "SELECT Username, DueDate, ReturnedDate, BookTitle, Author FROM loanhistory l INNER JOIN books USING(BookId) WHERE l.Username = '".mysqli_real_escape_string($conn, $username)."'");
	
	if(mysqli_num_rows($loan_query) == 0){
		echo -1;
	}else{
?>
		<table border="1" style = "width:100%">
		<thead>	
			<th>Username</th>
			<th>Book Title</th>
			<th>Book Author</th>
			<th>Due Date</th>
			<th>Returned Date</th>
		</thead>
<?php	
		while($rs = mysqli_fetch_assoc($loan_query)){
?>
			<tr>
				<td><?= $rs['Username'] ?></td>
				<td><?= $rs['BookTitle'] ?></td>
				<td><?= $rs['Author'] ?></td>
				<td><?= $rs['DueDate'] ?></td>
				<td><?= $rs['ReturnedDate'] ?></td>
			</tr>
<?php			
		}// end while loop over all loans to be displayed
?>
		</table>
<?php		
	}// end if we have rows	
	exit;
}// end if ajax action is getting the borrow history

if($ajax_action == 'get_book_details'){
	$book_id = get_value('id');
	$book_query = mysqli_query($conn, "SELECT * FROM books b LEFT JOIN loanhistory USING(BookId) LEFT JOIN booklocations USING(BookId) LEFT JOIN shelves USING(ShelfId) WHERE b.BookId = '".mysqli_real_escape_string($conn, $book_id)."' ORDER BY id DESC");
	$book_rs = mysqli_fetch_assoc($book_query);
	echo json_encode($book_rs);
	exit;
}// end if ajax_action == get_book_details

#######################
####  HTML SECTION ####
#######################
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" xmlns:Name="http://www.w3.org/1999/xhtml"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<title>Portal - Welcome</title>
<table width="75%">
<tr>
	<td><h1>Portal</h1></td>
	<td> Welcome <?= $_SESSION['username']?>! &nbsp;&nbsp;&nbsp; <a href="logout.php"><button>Logout</button></a>
<?php
	if($_SESSION['librarian']){
?>		<c id="button_display"><button onclick="getBorrowHistory(); return false;">Borrow History</button></c></td>
<?php
	}// end if user is a librarian
?>
</td>	
</tr>
<tr>
	<td id="extra_text">Click on a book to view its details.</td>
</tr>
<tr>
	<td><div id="books_table"></div></td>
</tr>
</table>

<div id="add-book">
    <form onsubmit="return false;">
		<label id="title" for="book_title">Book Title:</label>
		<br>
		<input size="30" type="text" id="book_title" name="book_title" class="text ui-widget-content ui-corner-all">
		<br>
		<br>
		<label id="title" for="post_title">Book Author:</label>
		<br>
		<input size="30" type="text" id="book_author" name="book_author" class="text ui-widget-content ui-corner-all">
    </form>
</div>

<div id="view-book">
    <form onsubmit="return false;">
		<label id="title" for="view_book_title">Book Title:</label>
		<label id="view_book_title" class="text ui-widget-content ui-corner-all"></label>
		<br>
		<br>
		<label id="author" for="view_book_author">Book Author:</label>
		<label id="view_book_author" class="text ui-widget-content ui-corner-all"></label>
		<br>
		<br>
		<label id="availability" for="view_book_availability">Availability:</label>
		<label id="view_book_availability" class="text ui-widget-content ui-corner-all"></label>
		<br>
		<br>
		<label id="shelf" for="view_book_availability">Shelf:</label>
		<label id="view_book_shelf" class="text ui-widget-content ui-corner-all"></label>
    </form>
</div>		

<script>
id = 0;
$( document ).ready(function() {
	// get the books table
    getBooks();
	//Initialize add-books dialog
	$("#add-book").dialog({
		title: "Add a Book",
		autoOpen: false,
		resizable: false,
		modal: true,
		width: 400,
		height: 250,
		buttons:
            [
              {
                  text: "Add",
				  id: "add",
                  click: function() {
					
					if($('#book_title').val() == '' || $('#book_author').val() == ''){
						alert('Book title and author are required fields.');
					}else{
						$.post( "Portal.php", {ajax_action: id, book_title: $('#book_title').val(), book_author: $('#book_author').val()}, function( data ) {
							// this function is called after the ajax request has been made
							document.getElementById('books_table').innerHTML = data;
						});
						$('#book_title').val('');
						$('#book_author').val('');
						$(this).dialog("close");
					}
                  }
              },
			  {
                  text: "Cancel",
                  click: function() {
					$('#book_title').val('');
					$('#book_author').val('');
                    $(this).dialog("close");
                  }
              }              
           ]
	});
	$("#view-book").dialog({
		title: "Book Details",
		autoOpen: false,
		resizable: false,
		modal: true,
		width: 400,
		height: 275,
		buttons:
            [
              {
                  text: "Delete",
				  id: "delete",
                  click: function() {
					  $.post( "Portal.php", { ajax_action: 'delete_book', book_id: id}, function( data ) {
							// this function is called after the ajax request has been made
							document.getElementById('books_table').innerHTML = data;
						});
					$(this).dialog("close");
                  }
              },
			  {
                  text: "Cancel",
				  id: "cancel",
                  click: function() {
					$('#view_book_title').val('');
					$('#view_book_author').val('');
                    $(this).dialog("close");
                  }
              }              
           ]
	});
	
<?php
	if(!$_SESSION['librarian']){
?>	 	
	$( "#delete" ).unbind();
	
	$("#delete").click(function () {
		toggleBorrow();
		$( "#cancel" ).trigger( "click" );
	});
	
<?php
	}// end if not a librarian
?>	
});

function getBooks() {
	$.post( "Portal.php", { ajax_action: 'get_books'}, function( data ) {
		// this function is called after the ajax request has been made
		document.getElementById('books_table').innerHTML = data;
	});
<?php
	if($_SESSION['librarian']){
?>
		
	document.getElementById('button_display').innerHTML = '<button onclick="getBorrowHistory(); return false;">Borrow History</button>';
	document.getElementById('extra_text').style.display = '';
<?php	
	}// end if this user is a librarian
?>	
}// end function for getting the books html

function get_book_details(){
	$.post( "Portal.php", { ajax_action: 'get_book_details', id: id}, function( data ) {
		// this function is called after the ajax request has been made
		data = jQuery.parseJSON(data);
		document.getElementById('view_book_title').innerHTML = data['BookTitle'];
		document.getElementById('view_book_author').innerHTML = data['Author'];
		
		if(data['Availability'] == 1){
			document.getElementById('view_book_availability').innerHTML = "This book is available.";
<?php
			if(!$_SESSION['librarian']){
?>	 			
				$('#delete').button('option', 'label', 'Borrow');
				$('#delete').show();			
<?php
			}// end if not a librarian
?>		
		}else{
			
			if(data['Username'] == "<?= $_SESSION['username']?>"){
				document.getElementById('view_book_availability').innerHTML = "You have this book checked out.";
			}else{
				document.getElementById('view_book_availability').innerHTML = "This book is borrowed by " + data['Username'];
			}// end if the borrow username != the logged in username
<?php
			if(!$_SESSION['librarian']){
?>	 
				if(data['Username'] == "<?= $_SESSION['username']?>"){
					$('#delete').button('option', 'label', 'Return');
					$('#delete').show();
				}else{
					$('#delete').hide();
				}// end if the borrow username != the logged in username
<?php
			}// end if not a librarian
?>		
		}// end if the book is available
		document.getElementById('view_book_shelf').innerHTML = data['ShelfName'];
	}); // end of ajax call
}// end function for getting a books details

function setId(obj){
	id = obj.id;
}// end function for setting the clicked id

function toggleBorrow(){
	$.post( "Portal.php", { ajax_action: 'toggle_borrow', book_id: id}, function( data ) {
		// this function is called after the ajax request has been made
		document.getElementById('books_table').innerHTML = data;
	});
}// end function for toggling the availability

function getBorrowHistory(){
	
	var username = prompt('Enter a username:');
	if(username != '' && username !== null){
		$.post( "Portal.php", { ajax_action: 'get_borrow_history', user: username}, function( data ) {
			// this function is called after the ajax request has been made
			if(data == -1){
				alert(username + ' does not have any loan history.');
			}else{
				document.getElementById('extra_text').style.display = 'none';
				document.getElementById('button_display').innerHTML = '<button onclick="getBooks(); return false;">View Shelves</button>';
				document.getElementById('books_table').innerHTML = data;
			}
		});
	}// end if username is not blank	
}// end function for getting borrow history
</script>

<?php
#######################
####  PHP FUNCTIONS ###
#######################

function is_librarian($username){
	global $conn;
	$librarian_query = mysqli_query($conn, "SELECT * FROM users u WHERE u.userName = '".mysqli_real_escape_string($conn, $username)."' AND u.Librarian = 1");
	
	if(mysqli_num_rows($librarian_query) > 0){
		return true;
	}else{
		return false;
	}// end if they are a librarian
}// end function for determining if this user is a librarian