/* searching doesnt use ajax at the moment : it simply uses jquery to hide stuff on the page */
function filterEvents(filter,dest){
        $(".event").each(function(){
            var t = $(".title",this).text()+$(".description",this).text();
            var f=(t.toLowerCase()).indexOf(filter.toLowerCase())!=-1;
            $(this)[f?"show":"hide"]();
        });
}

var filtertimer;
var filtertimerInterval = 1000;

function filterEventsKeyUp(filterelement, dest) {
    filtertimer = setTimeout(
        function () {
            filterEvents(filterelement.val(),dest);
        }, filtertimerInterval);
    return true;
}

function filterEventsKeyDown() {
    clearTimeout(filtertimer);
}

$(document).ready(function () {
    $(".searchinput").keydown(function () {
        filterEventsKeyDown();
    }).keyup(function () {
        filterEventsKeyUp($(this), "#centercontainer");
    });
    $(".clearsearchicon").click(function(){
        $(this).prev().val("");
        filterEvents("","#centercontainer");
    });
});
