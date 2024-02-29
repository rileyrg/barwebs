function fbLoginStatusChanged(response) {
    /* flyspell check Apple */

    $.ajax({
        type: "POST",
        data: {
            fbLoginStatusChanged: JSON.stringify(response)
        },
        dataType: "json",
        async: true,
        success: function(ack) {
            if (response.authResponse && response.authResponse.accessToken) {
                FB.api('/me?fields=first_name,picture', function (r) {
                    $(".fbuserimg").attr("src", r.picture.data.url);
                    if (ack.numlogins == 1) {
                        alert(ack.firstlogintext.replace("%s", response.first_name));
                    }
                });
            }
            else {
                $(".fbuserimg").attr("src", "/lib/common-images/fb/fb-logged-in.gif");
                $(".fbloggedinonly").remove();
            }
        },
        complete: arrWhenFBLoadedFn
    });
    
}

function fbCreateAction(action, object, url, starttime, durationmins, place) {

    var d = new Date(starttime);

    var curtime = new Date();
    var expires = Math.round(starttime !== null ? (d - curtime) / 1000 + (durationmins * 60) : null);
    var commandurl = '/me/' + og_namespace + ':' + action + '?start_time=' + ISODateString(curtime) + '&place=' + place + '&expires_in=' + expires + '&scrape=1&'+object+'=' + encodeURIComponent(url);
    FB.api(commandurl, 'post', function (response) {
        if (!response || response.error) {
            log('Error occured');
        }
        else {
        }
    });
}

function fbClientConnect() {

    log("fbClientConnect");

    arrWhenFBLoadedFn.push(function () {$(".displayafterfbloaded").removeClass("displayafterfbloaded");});
    arrWhenFBLoadedFn.push(function () {
        $(".findusonfacebook,.registeronfacebook").fadeOut(3000,function(){$(this).fadeIn(3000);});
    });

    window.fbAsyncInit = function () {

        log("calling init. appid == "+$("meta[property='fb:app_id']").attr("content"));

        FB.init({
            appId: $("meta[property='fb:app_id']").attr("content"),
            channelUrl : window.channelfile,
            status: true,
            cookie: true,
            xfbml: true
        });

        FB.Event.subscribe('auth.authResponseChange', function (response) {
            log("FB auth.authResponseChange="+response.authResponse);
            if (response.authResponse) {
                // fbLoggedIn(response);
            }
            else {
                // fbLoggedOut(response);
            }
        });

        FB.getLoginStatus(function (response) {
            if (response.authResponse) {
                log("FB Logged in");
            }
            else {
                log("FB Logged out");
            }
            fbProcessLiked();
            fbLoginStatusChanged(response);
        });
    };

 // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
      js.src = "//connect.facebook.net/en_GB/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));
}

var arrWhenLikedFn = [];
var arrWhenNotLikedFn = [];
var arrWhenFBLoadedFn = [];

function runWhenLiked(fn) {
    arrWhenLikedFn.push(fn);
}

function runWhenNotLiked(fn) {
    arrWhenNotLikedFn.push(fn);
    return false;
}

function runWhenFBLoaded(fn) {
    arrWhenFBLoadedFn.push(fn);
}

function _runWhenLiked() {
    log("processing liked function chain");
    for (var i = 0; i < arrWhenLikedFn.length; i++) {
        arrWhenLikedFn[i]();
    }
    return true;
}

function _runWhenNotLiked() {
    log("processing not liked function chain");
    for (var i = 0; i < arrWhenNotLikedFn.length; i++) {
        arrWhenNotLikedFn[i]();
    }
    return false;
}

function _runWhenFBLoaded() {
    for (var i = 0; i < arrWhenLikedFn.length; i++) {
        arrWhenFBLoadedFn[i]();
    }
}

function checkDoesLike () {
  FB.api({ method: 'pages.isFan', page_id : fb_likeid }, function(resp) {
    if (resp) {
        return TRUE;
    } else {
        return FALSE;
    }
  });
}

function fbProcessLiked() {
    return FB.api('/me/likes/' + fb_likeid, function (response) {
        if( response.data ) {
        // if( checkDoesLike() ) {
            _runWhenLiked();
        } else {
            _runWhenNotLiked();
        }    
    });
}

$(document).ready(function () {
    log("fb-utils init");
    fbClientConnect();
    runWhenNotLiked(function(){$(".whennotliked").removeClass("whennotliked");$(".whenliked").remove();});
    runWhenLiked(function(){$(".whenliked").removeClass("whenliked");$(".whennotliked").remove();});
    $(".registeronfacebook").click(function(){
        FB.login(function(response) {
            window.location=fb_page;
        }, {scope: fb_perms});
        ;
    });
});
