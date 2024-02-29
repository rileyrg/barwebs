jQuery.fn.clearForm = function () {
    $(':input', this).each(function () {
        var type = this.type;
        var tag = this.tagName.toLowerCase(); // normalize case
        // it's ok to reset the value attr of text inputs,
        // password inputs, and textareas
        if (type === 'text' || type === 'password' || tag === 'textarea') {
            this.value = "";
        }
        // checkboxes and radios need to have their checked state cleared
        // but should *not* have their 'value' changed
        else if (this.name !== "basicchk" && (type === 'checkbox' || type === 'radio')) {
            this.checked = false;
        }
        // select elements need to have their 'selectedIndex' property set to -1
        // (this works for both single and multiple select elements)
        else if (tag === 'select') {
            this.selectedIndex = -1;
        }
    });
};

jQuery.fn.populateEditor = function (data_array) {
    $(this).clearForm();
    $(this).data("orgevent", data_array);
    var ed = $(this);
    $.each(data_array, function (key, value) {
        $('[name="' + key + '"]', ed).each(function () {
            var $this = $(this);
            switch (this.type) {
            case 'select-one':
            case 'select-multiple':
                $this.attr('selected', true);
                break;
            case 'password':
            case 'select':
            case 'text':
            case 'hidden':
            case 'textarea':
                $this.val(value);
                break;
            case 'checkbox':
            case 'radio':
                $(this).attr("checked", value === 1 || value === "1" ? true : false);
            }
        });
    });
    $('[name="lang"]', this).val($("#language").val());
    var ni = $('.image', this).val();
    $('.imgpreview img', this).attr("src", ni.length > 0 ? ni : "common-images/noimg.png");
};

jQuery.fn.checkToken = function () {

    // if they type in a token do various automatic stuff to other fields affected
    var token = $(this).val().toLowerCase();
    $(this).val(token);
    if (token.length !== 0) {

        $(".class,.ogactions,.startdate,.duration", this).val('');
        $(".fenableshare", this).attr('checked', false);

        // set groupby depending on token
        var sg = [];
        sg["admin."] = "adminmessages";
        sg["ev:"] = "eventdefaults";
        sg["web:"] = "web";
        sg["fb:"] = "facebook";
        sg["sys:"] = "system";
        sg["md:"] = "microdata";
        sg["js:"] = "javscript";
        sg["jq:"] = "jquery";
        sg["contact:"] = "contact";
        sg["list:"] = "list-settings";
        sg["og:"] = "opengraph";
        sg["google:"] = "google";
        sg["bing:"] = "bing";
        sg["ical:"] = "ical";
        sg.js = "javascript";
        sg.raw = "raw";
        sg.css = "css";
        sg.meta = "meta";

        for (var index in sg) {
            if (token.indexOf(index) === 0) {
                $('[name="category"]', $(this).closest(".editorcontainer")).css("color", "red").val(curpage());
                $('[name="groupby"]', $(this).closest(".editorcontainer")).css("color", "red").val(sg[index]);
                $('[name="fadmin"]', $(this).closest(".editorcontainer")).attr('checked', true);
                return true;
            }
        }
    }
    return true;
};

jQuery.fn.focusLanguageFlags = function ()
{
    $(".languageflags .flag",this).addClass("hoverbright");
    $(".languageflags ."+$('[name="lang"]',this).val()).removeClass("hoverbright");
};

jQuery.fn.showLanguageFlags = function(rec)
{

    if(!rec){
        rec=jsonrecords[$('[name="id"]',this).val()];
    }

    $(".languageflags",this).empty();

    if(typeof rec.languages === "undefined")
        return;

    var a=rec.languages;
    if(!a)
        return;
    var l=Object.keys(a).length;
    for(var i=0; i<l; i++) {
        var lang=a[i];
        $(".languageflags",this).append($('<li title="'+lang+'" class="flag scalehalf'+(lang!=$('[name="lang"]',this).val()? " hoverbright ":" ")+lang+'"/>').data("lang",lang).click(
            function () {
                var lang=$(this).data("lang");
                $(this).closest(".editorcontainer").find('[name="lang"]').val(lang);
                $(this).closest(".editorcontainer").find(".changed").removeClass("changed");
                changeLanguage(null,$(this).closest(".editorcontainer"));
            }
        ));
    }
};

jQuery.fn.editedRec = function () {
    var o = {};
    o.id=$('[name="id"]',this).val();
    $(this).find(".changed").not(".nosave").each(function () {
        o[$(this).attr("name")] = $(this).attr("type") == "checkbox" ? ($(this).prop("checked") ? "1" : "0") : ($(this).val() || '');
    });
    return o;
};

jQuery.fn.saveEvent = function(closeaftersave) {
    
    if($('[name="fduplicate"]',this).attr("checked")){
        if(!confirm("Really Duplicate?"))
            return false;
        $('[name="id"]',this).val(null);
        $(".editorpane textarea,.editorpane select,.editorpane :checkbox,.editorpane input", this).addClass("changed");
    }

    $(this).showSaving(true);
    var editedrec=JSON.stringify($(this).editedRec());
    $.ajax({
        type: "POST",
        async: true,
        context:this,
        data: {
            saveeventrec: editedrec,
            settings:true,
            fullhtml:true,
            category:curpage(),
            search:$("#search").val()
        },
        dataType: "json",
        success: function (response) {
            $(".centercontainer").html(response.html);
            $(".event").canedit();
            $(this).showSaving(false);
            interactiveInit();
            if(closeaftersave){
                $(this).fadeOut("3000",function(){$(this).remove();});
            }else{
                $('[name="id"]',this).val(response.id);
                $(this).showLanguageFlags();
                $(this).dialog("option","title",eventEl(response.id).editorTitle());
            }
        },
        error : function (response) {
            $(this).showSaving(false);
        }
    });
};

jQuery.fn.editorTitle = function (){
    var id=$(this).eventID(),t='#'+id+" "+(jsonrecords[id].title||(jsonrecords[id].token?"Setting: "+jsonrecords[id].token:""));
    return t.length>32? t.substring(0,30)+"..":t;
};

jQuery.fn.openEditor = function (id, pane) {
    $(".fduplicate",this)[id?"show":"hide"]();
    $(this).submit(function(){
        var ed=$(this).closest(".editorcontainer");
        ed.saveEvent(false);
        return false;
    }).dialog({
        width: 'auto',
        position:"center top",
        modal: false,
        autoOpen: false,
        resizeable: true,
        closeOnEscape: true,
        title: !id?'New Event': eventEl(id).editorTitle()
    });

    var rec=id?jsonrecords[id]:$(".centercontainer").data("eventtemplate");

    $(this).populateEditor(rec);
    $(this).showLanguageFlags(rec);
    $(this).setEditorPane(typeof pane == "undefined" ? "basicchk" : pane);

    $(".editorpane textarea,.editorpane select,.editorpane :checkbox,.editorpane input", this).change(function () {
        $(this).addClass("changed");
    });

    if(!rec.id) {
        $(".editorpane textarea,.editorpane :checkbox,.editorpane input", this).addClass("changed");
    }

    zapFlash();

    $(".eventfieldedit .startdate",this).datetimepicker({
        dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        stepMinute: 15,
        separator: ' '
    });

    $(this).dialog("open");

};

function openEditor(id, pane) {
    var newed = $(".editortemplate").clone(1).attr("id",null).addClass("editor").removeClass("editortemplate");
    newed.openEditor(id, pane);
}



jQuery.fn.editEvent = function (pane) {
    openEditor($(this).eventID(), pane);
    return $(this);
};

jQuery.fn.updateEditorHelpPane = function (pane) {
    var helptext = getSysMsg("admin.eventeditor.pane-" + (typeof pane == "undefined" ? "basicchk" : pane));
    $('.editorhelppane', this).empty().append($("<p></p>").html(helptext));
    return helptext;
};

jQuery.fn.setEditorChkTitles = function (name) {
    // log("setEditorChkTitles:" + name);
};


jQuery.fn.setEditorPane = function (pane) {
    $(':radio.' + pane, this).click();
};

jQuery.fn.editorReady = function () {

    $(".token", this).change(function () {
        $(this).checkToken();
    });

    $('[name="lang"]', this).change(
        function () {
            changeLanguage(null,$(this).closest(".editorcontainer"));
        }
    );

    $(".clearedit", this).click(function () {
        $(this).prevAll("textarea:first,input:first").val("");
    });

    $(".advancedchecks input[type=radio]", this).click(function () {
        setEditorChkTitles($(this).val());
    });

    $(".image", this).change(

        function () {
            var ni = $(this).val();
            $(this).closest(".editor").find(".imgpreview img").attr("src", ni.length > 0 ? ni : "common-images/previewicon.png");
        });

    $(".basicedits", this).css("display", "block");

    $(".googlebutton").click(function(){
        var url=$(this).attr("data-url")+"?&q="+$(this).closest(".wizardeditor").find('[name="title"]').val()+"&tbs=isch:1,isz:m";
        window.open(url,"Google for image");
        return false;
    });

};

$(document).ready(function () {
    log("editor init");
    $('[name="editor"]').editorReady();
});
