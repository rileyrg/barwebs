$(document).ready(function () {
    log("admintoolbar init");

    $(".nerdbadge").click(function () {
        $("body").toggleClass("nerdmode");
        if(!nerdMode())
            $(".disabledevent,.advancednerd").hide();
    });

    $(".adminmetawizard").click(function () {
	$(".metawizards").toggle();
    });

    $(".adminnewevent").click(function () {
	openEditor(0);
    });

    $(".admintoggledisabled").click(function () {
        $(".disabledevent,.nerdmode .advancednerd").toggle();
    });

    $(".adminpages").click(function () {
        document.location.href = "pages";
    });
    $(".adminfooters").click(function () {
        document.location.href = "footerlinks";
    });
    $(".adminsettings").click(function () {
        document.location.href = "settings";
    });
    $(".adminbugs").click(function () {
        document.location.href = "bug";
    });
    $(".webmail").click(function () {
        $('form[name="webmail"]').submit();
    });
    $(".adminmsgcodes").click(function () {
        document.location.href = "msgcode";
    });
    $(".adminvideos").click(function () {
        document.location.href = "http://www.youtube.com/playlist?list=PL0690D9C115AA780C";
    });
    $(".adminhelp").click(function () {
        alert("not yet implemented");
    });
    $(".admintoolbar .closebutton").click(function () {
        document.location.href = "act_adminlogout.php";
    });

    $(".admintoolbar .icon").addClass("hoverbright");

}
);
