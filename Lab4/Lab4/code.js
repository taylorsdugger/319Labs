
$(document).ready(function () {

    $("#button1").click(function () {
        hideShow1();
    });

    $("#button2").click(function () {
        slidey();
    });

    $("#button3").click(function () {
        fadey();
    });

    $("#p3").on({
       mouseenter:function () {
           $(this).css("background-color", "lightgray");
       },
        mouseleave: function () {
            $(this).css("background-color", "lightblue");
        },
        click: function () {
            $(this).css("background-color", "red");
        },
        dblclick: function () {
            $(this).css("background-color", "yellow");
        }
    });

    var hide = 0;
    var slides = 0;

    function hideShow1() {
        if (hide == 0) {
            $("#p1").hide();
            hide = 1;
        } else {
            $("#p1").show();
            hide = 0;
        }
    }

    function slidey() {
        if (slides == 0) {
            $("#p2").slideUp(800);
            slides = 1;
        } else {
            $("#p2").slideDown(600);
            slides = 0;
        }
    }

    function fadey() {
        $("#p4").fadeToggle("500"); // found the toggle feature a little late
    }
});