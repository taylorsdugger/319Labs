
function Problem2Output(){
	
	
	var c = document.getElementById("myCanvas");
	var ctx = c.getContext("2d");
	ctx.moveTo(0,0);
	ctx.lineTo(200,100);
	ctx.stroke();
}
/*var c = document.getElementById("myCanvas");
var ctx = c.getContext("2d");
var w = window.innerWidth;
var h = window.innerHeight;
var snakeSize = 10;
var snake;
var stop = false;
var curX = 0;
var curY = 50;

var c = document.getElementById("myCanvas");
var ctx = c.getContext("2d");
ctx.moveTo(0,0);
ctx.lineTo(200,100);
ctx.stroke();

document.getElementById('stop').onClick = function(){
	stop = true;
};
	
	while(!stop){
		ctx.moveTo(curX, curY);
		ctx.lineTo(curX+200, cuyY);
		ctx.stroke();
		curX+=10;
		wait(100);
	}
}

var draw = (function(){
	var body = function(x,y){
		ctx.fillStyle = 'red';
		ctx.fillRext(x*snakeSize, y*snakeSize, snakeSize,snakeSize);
		
	};
});

var drawSnake = function(){
	var len = 1;
	snake = [];
	
	for(var i = len; i>=0; i--){
		snake.push({x:i, y:0});
	}
};

var point = function(){
	ctx.fillStyle = 'white';
	ctx.fillRect(0,0,w,h);
	
	ctx.strokeStyle = 'black';
    ctx.strokeRect(0, 0, w, h);
 
    var snakeX = snake[0].x;
    var snakeY = snake[0].y;
    
};*/
