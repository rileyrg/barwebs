var mouse_is_inside = false;
 
$(document).ready(function() {
    $('.loginform input[type=password]').val("");
    $(".login").click(function() {
        if ($(".loginform").is(":visible"))
            $(".loginform").fadeOut("fast");
        else{
            $(".loginform").fadeIn("fast",function(){$(this).delay(5000).fadeOut(10000);});
	    }
        return false;
    });

    $(".login").hover(function(){
        $(">*",this).fadeIn("slow"); 
    }, function(){ 
       $(">*",this).fadeOut("fast"); 
    });
 

 
    $(".loginform").hover(function(){ 
        mouse_is_inside=true; 
    }, function(){ 
        mouse_is_inside=false; 
    });
 
    $("body").click(function(){
        if(! mouse_is_inside) $(".login>*").fadeOut("fast");
    });
});
