jQuery.fn.edittitle = function() {
    var js=$(this).data("eventrec");
    if(js.title.length)
        return js.title;
    if(js.cssid.length)
        return js.cssid;
    if(js.token.length)
        return js.token;
    return "<unknown>";
};

jQuery.fn.deleteEvent = function (conf) {
    if($(this).hasClass("nodelete")){
        alert("Deletion forbidden : nodelete class set");
        return false;
    }
    if (conf) {
        if (!confirmaction("Really delete?")) return false;
    }
    deleteEvent($(this));
};


function deleteEvent(el) {
    $.ajax({
        type: "GET",
        data: {
            delete: el.eventID()
        },
        success: function () {
            el.remove();
        },
        error: function () {
            alert("Deletion failed.");
        }
    });
    return false;
}

jQuery.fn.sewOnBadges = function (shield)
{
    if ($(this).getField("embeddedlist")) {
        shield.append('<li data-editorpane="embedchk" title="Embedded List" class="badge hoverbright advancednerd embeddedlistbadge scalehalf"></li>');
    }
    
    if(parseInt($(this).getField("fenabled"),10)===0){
        shield.append('<li data-editorpane="basicchk" title="Enable/Disable" class="badge hoverbright advancednerd disabledbadge scalehalf"></li>');
    }
    
    if ($(this).getField("cssid")) {
        shield.append('<li data-editorpane="csschk" title="UID" class="badge hoverbright advancednerd uidbadge scalehalf"></li>');
    }

    if ($(this).getField("cssstyle")||$(this).getField("csslink")||$(this).getField("cssclass")) {
        shield.append('<li data-editorpane="csschk" title="CSS Options" class="badge hoverbright advancednerd cssbadge scalehalf"></li>');
    }

    // if ($(this).getField("cssoverride")) {
    //     shield.append('<li  data-editorpane="csschk" title="Drag Position" class="badge hoverbright cssoverridebadge"></li>');
    // }

    if ($(this).getField("includelink")) {
        shield.append('<li  data-editorpane="embedchk" title="User Include" class="badge hoverbright advancednerd userincludebadge scalehalf"></li>');
    }
    if ($(this).getField("javascript")||$(this).getField("javascriptlink")||$(this).getField("cookietimeout")) {
        shield.append('<li  data-editorpane="jschk" title="Javascript" class="badge hoverbright advancednerd jsbadge scalehalf"></li>');
    }
    
    if (parseInt($(this).getField("fbenableshare"),10) ||parseInt($(this).getField("fjsonenabled"),10)||parseInt($(this).getField("fbcomments"),10)||$(this).getField("ogactions")||$(this).getField("ogtype")
       ) {
        shield.append('<li  data-editorpane="fbchk" title="FB/Share" class="badge hoverbright advancednerd fbbadge scalehalf"></li>');
    }
    
    if ($(this).getField("setting")) {
        shield.append('<li data-editorpane="settingchk" title="Setting" class="badge hoverbright advancednerd settingbadge scalehalf"></li>');
    }

    if ($(this).getField("embed")) {
        shield.append('<li data-editorpane="embedchk" title="Embedded Object" class="badge hoverbright advancednerd embedbadge scalehalf"></li>');
    }

    if ($(this).attr("data-datapage")) {
        shield.append('<li title="Open DataPage" class="badge hoverbright advancednerd datapagebadge scalehalf"></li>').click(
            function(e){
                document.location=jsonrecords[$(this).eventID()].category;
            });
    }

    shield.append('<li data-editorpane="basicchk" title="Edit" class="badge eventeditbadge"></li>');
    shield.append('<li title="delete" class="badge hoverbright advancednerd eventdelete scalehalf"></li>');
    
 
};

jQuery.fn.getField = function (f) {
    return jsonrecords[$(this).eventID()][f];
};


jQuery.fn.badges = function (shield)
{
    if (typeof shield == "undefined")
        shield="badges";
    $(this).each(function(){
        $("."+shield,this).remove();
        $('<ul class="'+shield+' focuscontrol"></ul>').prependTo($(this));
        $(this).sewOnBadges($("."+shield,this));
    });
    $(".badge",this).not(".datapagebadge,.eventdelete").click(function(e){
        eventEl($(this)).editEvent($(this).attr("data-editorpane"));
        e.stopImmediatePropagation();
    });
    return $(this);
};

jQuery.fn.canedit = function () {
    
    $(this).badges().hover(function (e,ui) {
        $(".outline").removeClass("outline");
        $(this).addClass("outline");
    }, function (e) {
        $(this).removeClass("outline");
    });
    
    return $(this);
};

$(document).ready(function () {

    log("adminmode init");

    $(".event").canedit();
    
    $(".drawerhandle").click(function (e) {
        $(this).toggleClass("draweropen");
        $(this).next(".drawer").toggleClass("display");
        e.stopPropagation();
    });

    $(".admintoolbar").show();

    $(".eventdelete").click(
        function (e) {
            $(this).closest(".event").deleteEvent(true);
            e.stopPropagation();
        });
});


