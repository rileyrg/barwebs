function setupCalendars()
{

    $('.addtocal').each(
            function(){ 
                e=$(this).closest(".event").find(".times:first");
                t="";
                $('meta[property="' + og_namespace + ':contact:street_address"]:first').each(function () {
                    t = $(this).attr("content");
                });
                try{
                    $(this).icalendar({
                        icons:"common-images/icalendar.png",
                        compact:true,
		        title: $(".title",e.parent()).text(),
		        description: $(".description",e.parent()).text(),
                        start: new Date(1000*e.attr('data-starttime')),
                        end: new Date(1000*e.attr('data-endtime')),
		        location:eventlocation,
		        url: e.attr('data-calurl')
                    });
                } catch(err){
                    log("error adding icalendar to event '"+$(".title",e).text()+"'");
                };
            });


}


$(document).ready(function () {
    log("calendar interfaces loaded");
    setupCalendars();
    $('.event.today,.event.nowon,.event.tomorrow,.event.countdown').not(".nocountdown,.cancelled,.expired").each(function(){$(".startdate",this).after($('<div class="countdowntimer"/>').countdown({until:(new Date($(this).closest(".event").find("[data-starttime]").attr("data-starttime")*1000)),alwaysExpire:true, expiryText:'NOW SHOWING',compact: true, layout: '{dn} days,{hn} hours, {mn} mins, and {sn} seconds', description: ''}));});
});
