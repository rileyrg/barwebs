if (!window.console) console = {log: function() {}};

function log() {
    console.log(arguments);
}

$.fn.log = function () {
    return this;
};

function eventEl(e) {
    return isInt(e)?$('[data-pkey="'+e+'"]'):e.closest(".event");
}

jQuery.fn.eventID =function(){
    return $(this).closest(".event").attr('data-pkey');
};

function css(a){
    var sheets = document.styleSheets, o = {};
    for(var i in sheets) {
        var rules = sheets[i].rules || sheets[i].cssRules;
        for(var r in rules) {
            if(a.is(rules[r].selectorText)) {
                o = $.extend(o, css2json(rules[r].style), css2json(a.attr('style')));
            }
        }
    }
    return o;
}

jQuery.fn.nativePage = function()
{
    var dp=$(this).attr("data-category");
    if(dp){
        var p=dp.split("data-");
        if(typeof p[1]=="undefined")
            return true;
        return p[1]==curpage();
    }
    return true;    
};

jQuery.fn.showSaving = function (f,t)
{
    $(this).css('cursor', f?'wait':'default');
    if(f){
        $(this).append($('<img class="savingicon" src="common-images/adminmode/saveicon.png"/>'));
    }
    else
        $(".savingicon").remove();

    return $(this);
};

function css2json(css){
        var s = {};
        if(!css) return s;
        if(css instanceof CSSStyleDeclaration) {
            for(var i in css) {
                if((css[i]).toLowerCase) {
                    s[(css[i]).toLowerCase()] = (css[css[i]]);
                }
            }
        } else if(typeof css == "string") {
            css = css.split("; ");          
            for (var i2 in css) {
                var l = css[i2].split(": ");
                s[l[0].toLowerCase()] = (l[1]);
            }
        }
        return s;
    }

function onlyNumbers(char){

    var txt = char;
    var found = false;
    var validChars = "0123456789"; //List of valid characters

    for(j=0;j<txt.length;j++){ //Will look through the value of text
        var c = txt.charAt(j);
        found = false;
        for(x=0;x<validChars.length;x++){
            if(c==validChars.charAt(x)){
                found=true;
                break;
            }
        }
        if(!found){
            //If invalid character is found remove it and return the valid character(s).
            document.getElementById('txtFld').value = char.substring(0, char.length -1);
            break;
        }
    }
}

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - this.outerHeight()) / 2) + 
                                                $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - this.outerWidth()) / 2) + 
                                                $(window).scrollLeft()) + "px");
    return this;
};

function isInt(n) {
   return n % 1 === 0;
}

function eventJavaScriptError(el, msg) {
    log("error:" + msg + " : el=", el);
}

function mysqlDateTimeToDate(dt) {
    //function parses mysql datetime string and returns javascript Date object
    //input has to be in this format: 2007-06-05 15:26:02
    var regex = /^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
    var parts = dt.replace(regex, "$1 $2 $3 $4 $5 $6").split(' ');
    return new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], parts[5]);
}


function ISODateString(d) {
    function pad(n) {
        return n < 10 ? '0' + n : n;
    }
    return d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate()) + 'T' + pad(d.getUTCHours()) + ':' + pad(d.getUTCMinutes()) + ':' + pad(d.getUTCSeconds()) + 'Z';
}

jQuery.fn.staggerElements = function(xd,yd){
    if(typeof xd == undefined)
        xd=1;
    if(typeof yd == undefined)
        yd=1;
    $(this).each(function(){
    });
};


function curpage() {
    var sPath = $(location).attr('href');
    var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
    if (sPage.length === 0) {
        sPage = "index";
    }
    var t = sPage.split("?");
    return t[0];
}

function setCookie(name,v,o,m)
{
   if($.cookie("cookiesallowed")==null){
       $('<div id="cookiesdialog">'+(typeof m === "undefined" ? 'We use cookies to remember your preferences.': m)+'<div  class="icon cookie"></div></div>').dialog({
           width: 'auto',
           height:'auto',
           modal: false,
           autoOpen: true,
           resizeable: true,
           closeOnEscape: true,
           title: "Cookies",
           buttons : {
               "YES Of Course! I Trust You!" : function() {
                   $.cookie("cookiesallowed",true,{expires:365,path:"/"});
                   $.cookie(name,v,o);
                   $(this).dialog('close');
               },
               "NO Never! (Well not today..)" : function() {
                   $.cookie("cookiesallowed",false,{expires:1,path:"/"});
                   $(this).dialog('close');
               },
               "Hmm. Ask Me Next Time." : function() {
                   $(this).dialog('close');
               }
           }
       }).css('overflow', 'hidden');
   }   
    
    if($.cookie("cookiesallowed")=="true")
        $.cookie(name,v,o);
}


function adminMode()
{
return $("body").hasClass("adminmode");
}
function nerdMode()
{
return $("body").hasClass("nerdmode");
}
function mobileMode()
{
return $("body").hasClass("mobilemode");
}

function time () {
  // http://kevin.vanzonneveld.net
  // +   original by: GeekFG (http://geekfg.blogspot.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: metjay
  // +   improved by: HKM
  // *     example 1: timeStamp = time();
  // *     results 1: timeStamp > 1000000000 && timeStamp < 2000000000
  return Math.floor(new Date().getTime() / 1000);
}

function getSysMsg(msg) {
    var msgtext;
    $.ajax({
        type: "GET",
        data: {
            msgcode: msg
        },
        cache: true,
        async: false,
        dataType: "html",
        success: function (m) {
            msgtext = m;
        }
    });
    return msgtext;
}

var isEmpty = function(obj) {
  return Object.keys(obj).length === 0;
};
