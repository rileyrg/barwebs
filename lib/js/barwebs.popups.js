jQuery.fn.cookieName= function () {
    return ($(this).attr("data-cookiename")||"eventcookie-"+$(this).eventID());
};

function deleteAllCookies()
{
    $("[data-cookiename]").each(function(){
        log("deleting : ",$(this).attr("data-cookiename"));
        $.cookie($(this).attr("data-cookiename"),null,{path:"/"+curpage(),expires:-1});
    });
    $.cookie("cookiesallowed",null,{expires:-1});
    $.cookie("lang",null,{expires:-1});
    return true;
}

function orderbysortfunc(a, b) {
        /* use order by for choosing first*/
        a = $(a).attr("data-orderby");
        b = $(b).attr("data-orderby");
        
        if(a > b) {
            return 1;
        } else if(a < b) {
            return -1;
        } else {
            return 0;
        }
}

function popupsInit(){

    $(".notmobilemode.normalmode  .nowon,.notmobilemode.normalmode  .today, .notmobilemode.normalmode .tomorrow").filter(function(){return $(this).nativePage();}).first().addClass("eventpopup previewpopup");

    var firstcookieprotected=false;
    var x=$(".eventpopup,.cookieprotected");
    x.sort(orderbysortfunc);

    /* filter whichi ones are active */
    x.each(function(){
        /* only show first cookieprotected */
        if($(this).hasClass("cookieprotected")){
            if(!adminMode()){
                var cookieexists=$.cookie($(this).cookieName());
                if(cookieexists || (firstcookieprotected && !$(this).hasClass("always"))){
                    $(this).remove();
                    return true;
                }
                if(!$(this).hasClass("always"))
                    firstcookieprotected=true;
            }else
                $(this).addClass("advancednerd");
        }

        if($(this).hasClass("eventpopup"))
            $(this).toggleClass("eventpopup visiblepopup");

        if(!mobileMode()&&$(this).attr("data-popupposition")){
            var p=$(this).attr("data-popupposition");
            p=p.split(",");
            $(this).css({"top":p[0],"left":p[1],"bottom":"","right":""});
        }
        return true;
    });

    $(".visiblepopup:not(:has(.hidebutton)),.cookieprotected:not(:has(.hidebutton))").not(".noclose").addClass("focuscontrol"). append($('<div class="info"></div>')).append($('<div class="hidebutton" title="close">close</div>').button({
        icons: {
            primary: "ui-icon-close"
        }
    }).click(function(e){
        e.stopImmediatePropagation();
        var el=$(this).closest(".event");
        if(!adminMode()&&el.hasClass("cookieprotected")){
            var name=el.cookieName();
            var cookietimeout=el.attr("data-cookietimeout");
            if(cookietimeout){
                var options ={expires: parseInt(cookietimeout,10),path:"/"+(el.hasClass("everypage")?'':curpage()),cookiename:name};
                setCookie(name,true,options);
            }
        }
        if(el.hasClass("cookieprotected")){
            el.remove();
            // interactiveInit();
        }else{
            el.draggable("destroy");
            el.removeClass("eventpopup previewpopup visiblepopup  hoverbrightsubtle");
            $(".hidebutton,.info",el).remove();
            el.css({"left":"","top":"","right":"","bottom":""});
        }
    })).has(".cookieprotected").append($("#dontshowagain").clone());

    $(".notmobilemode .visiblepopup").draggable().resizable();

    $('.adminmode [data-cookietimeout]').each(function(){
        var cookievalue=JSON.parse($.cookie($(this).cookieName()));
        if(cookievalue){
            $(this).append($('<div data-cookiename="'+cookievalue.cookiename+'" title="cookie : '+cookievalue.cookiename+' (expires:'+cookievalue.expires+')" class="icon cookie hoverbright"/>').click(function (e){
                if(confirm("Really erase the cookie for this event?")){
                    var cookiename=$(this).closest(".event").attr("data-cookiename");
                    setCookie(cookiename,null,{expires:-1,path:"/"+curpage()});
                    $(this).remove();
                }
                e.stopPropagation();
            }));
        }
    });
}

$(document).ready(function () {

    log("cookies init");

});

