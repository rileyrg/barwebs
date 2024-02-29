function confirmaction(s, iconclass) {
    if (confirm(s)) {
        return true;
    }
    else {
        return false;
    }
}

function zapFlash() {
    $("embed").attr("wmode", "opaque");
    var embedTag;
    $("embed").each(function (i) {
        try {
            embedTag = $(this).attr("outerHTML");
            if (embedTag) {
                embedTag = embedTag.replace("/embed /gi", 'embed wmode="opaque"');
                $(this).attr("outerHTML", embedTag);
            }
            else {
                $(this).wrap("<div></div>");
            }
        }
        catch (err) {
            log("error in zapflash");
        }
    });
    return true;
}

function setShareFade() {
    $(".normalmode .barwebsshare").closest(".event").not('[data-cookietimeout]').hover(function (e) {
        $(".event").removeClass("outline hovered");
        if(!$("body").hasClass("singleevent"))
            $(this).addClass("outline");
        $(".barwebsshare").stop(1).fadeTo("slow",0.2);
        $(".barwebsshare:first", this).fadeTo("fast",1);
        $(this).addClass("hovered");
    }, function (e) {
        $(".barwebsshare", this).delay(5000).fadeTo("slow",0.1,function(){$(this).closest(".event").removeClass("hovered");});
    });
};

function changeLanguage(lang,el) {
    if(typeof el !== "undefined"){
        if($('[name="id"]', el).val()){
            el.css('cursor', 'wait');
            $.getJSON("ajax", {
                edited: $('[name="id"]', el).val(),
                newlang: $('[name="lang"]', el).val()
            }, function (rec) {
                $('[name="title"]', el).removeClass("changed").val(rec.title);
                $('[name="description"]', el).removeClass("changed").val(rec.description);
                el.focusLanguageFlags();
                el.css('cursor', 'default');
                return true;
            });
        }
        $('[name="lang"]', el).addClass("changed");
        return true;
    }else{
        setCookie("lang",lang,{path:"/"});
        $.getJSON("ajax?newlang="+lang,function(){
            window.location.reload();
        });
        return true;    
    }
};

function reattachEvents()
{
    $('.notmobilemode [data-attachto]').each(function(){
        $('#'+$(this).attr("data-attachto").replace("#","")).css({"position":"relative"});
        $(this).appendTo("#"+$(this).attr("data-attachto").replace("#",""));
    });

}

function applyTimeConstraints()
{
    $("[data-displaydelay],[data-displayfor]").each(function(){
        $(this).delay(1000*$(this).attr("data-displaydelay")).queue(function(next){ 
            $(this).removeClass("displaydelayed").addClass("displaynow");
            if(!adminMode())
               $(this).slideDown();
            if($(this).attr('data-displayfor')){
                $(this).delay($(this).attr('data-displayfor')*1000).queue(function(next){
                    if(!adminMode())
                        $(this).slideUp(function(){$(this).remove();});
                    else
                        $(this).removeClass("displaynow").addClass("displaytimedout");
                    next();
                });
            }
            next();
        });
    });

}

function interactiveInit() {

    if(!mobileMode())
        setShareFade();

    $(".eventicon img").error(function () {
        $(this).closest(".eventicon").remove();
    });

    $(".event.block:visible").css({"display":"block","margin":"0 auto"});

    $(".fbcontainer .title:first").after($("#barwebs-fblogincontainer"));
    $("#languagecontainer").append($("#language"));
    $("#pagescontainer").append($("#pages"));
    $("#footerlinkscontainer").append($("#footerlinks"));
    $("#searchcontainer").append($("#searchpane"));

    $("input:submit,input:reset").button();

    $(".draggable").draggable();

    $(".paperclip").append($('<div class="paperclipmarker"></div>'));
    $(".underreview").append($('<div class="underreviewmarker"></div>'));

    $(".removeable").append($('<div title="close" class="icon removebutton hoverbright"/>').click(function(e){$(this).closest(".removeable").remove();e.stopPropagation();}));
    $(".event.addclosebutton:not(:has(.closebutton,[data-cookietimeout]))").append($('<div title="close" class="hoverbright closebutton">close</>').button({icons: {primary: "ui-icon-close"}}).click(function(e){$(this).closest(".event").remove();e.stopPropagation();}));

    reattachEvents();
    popupsInit();
    applyTimeConstraints();

    $(".adminmode .event:not(.containsembeddedlist)").addClass("focuscontrol");

    $(".fadein").fadeIn();
    $(".fadeinslow").fadeIn("slow");
    $(".fadeinfast").fadeIn("fast");
    $(".fadeout").fadeOut();
    $(".fadeoutslow").fadeOut("slow");
    $(".fadeoutfast").fadeOut("fast");

}

$(document).ready(function () {
    log("interactive init");
    interactiveInit();
});

