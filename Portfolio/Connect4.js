var Game = {
  current_player_color: 1,
  rows: 6,
  columns: 7,
  board: 0,

  init_board : function() {
    // initializes the board by setting all elements in array to 0
    this.board = new Array();
    this.current_player_color = 1; // make sure the current player starts as yellow

    for (i = 0; i < this.rows; i++) {
        this.board[i] = new Array();

        for (j = 0; j < this.columns; j++) {
            // 0 for empty pieces, 1 for yellow, 2 for red
            this.board[i][j] = 0;
        }// end for loop initializing the board with empty pieces
    }// end outter for loop initializing the board
    document.getElementById('game_text').innerHTML = "It is yellow's turn.";
  }, // end init_board function

  draw_board : function() {
    // draws the board to HTML elements in the DOM using the initialized board Array
    for(i = 0; i < this.rows; i++){

        for(j = 0; j < this.columns; j++){
            var board_position = i.toString() + j.toString();
            var board_piece = document.getElementById(board_position);

            if(this.board[i][j] == 0){
               // 0 for empty pieces
               board_piece.className = "empty";
            }else if(this.board[i][j] == 1){
              // 1 for yellow pieces
              board_piece.className = "yellow";
            }else if(this.board[i][j] == 2){
              // 2 for red pieces
              board_piece.className = "red";
            }// end if for setting the color when drawing the board
        }// end for loop over all the columns to draw
    }// end for loop over all the rows to draw
  },// end draw_board function, draws to the HTML table

  attach_event_listeners : function(){
    // for each table cell element, attach a click listener for dropping tokens
    $("td").each(function() {
        var id = $(this).attr("id");
        var board_piece = document.getElementById(id);
        board_piece.addEventListener("click", drop_token);
    }); // end of each function over all td elements

    // attach a listener to our reset button to restart the game
    document.getElementById('reset').addEventListener("click", function(){
        Game.init_board();
        Game.draw_board();
    });// end of function for resetting the game
  },// end function for attaching button listeners to DOM elements

  toggle_player_turn : function(){
    // toggle the players turn after each token is dropped
    if(this.current_player_color == 1){
       this.current_player_color = 2;
       document.getElementById('game_text').innerHTML = "It is red's turn.";
    }else if(this.current_player_color == 2){
       this.current_player_color = 1;
       document.getElementById('game_text').innerHTML = "It is yellow's turn.";
    }// end if for toggling the players turn
  },// end of function for toggling the players turn

  board_is_full : function(){
    // function for determining if the entire board is full, does no win checking
    for(i = 0; i < this.rows; i++){
        for(j = 0; j < this.columns; j++){
            if(this.board[i][j] == 0){
              return false;
            }// end if we found an empty space
        }// end for loop over columns
    }// end for loop over rows
    return true;
  }// end of function for determing if the board is full

};// end of object Game

function drop_token(){
   // drop a token in the correct position based on which click listener has been triggered
   column = this.id.substring(1);

   // find which row we can put this piece in
   for(row = Game.rows - 1; row >= 0; row--){
     if(Game.board[row][column] == 0){
        break;
     }// end if we have found an empty piece, break out of the loop
   }// end for loop over all rows

   if(Game.board[row][column] != 0){
     return;
   }// end if we are on the last row, we cant place a piece

   (function animate (i) {

     setTimeout(function () {
       // set a timeout function so that the color is set on each row before the row to be dropped on
       // this will create a sort of dropping token effect
       Game.board[i][column] = Game.current_player_color;
       Game.draw_board();
       if (i < row) {          // If i > 0, keep going

         setTimeout(function () {
           // set this timeout function so that after the color is set,
           // the color will be reset back to empty
           Game.board[i][column] = 0;
           Game.draw_board();
         }, 20);
         // recurse setting timeout functions until we reach the final row
         animate(i);
       }else{
         // we have reached the final row, set the color
         // toggle the players turn, redraw the board, and check if the game is over
         Game.board[row][column] = Game.current_player_color;
         Game.toggle_player_turn();
         Game.draw_board();

         if(Game.board_is_full()){
            document.getElementById('game_text').innerHTML = "Game has ended in a tie! Click 'Restart' to begin a new game.";
         }// board is full and nobody won, game is a tie
       }// end if we've reached the last row
     }, 20);// end timeout function for animating the chip falling

     i++;
   })(-1); // end of animate function for token animation
}// end function for dropping tokens, event listener for each table cell

Game.init_board();
Game.draw_board();
Game.attach_event_listeners();
