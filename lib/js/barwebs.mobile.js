function mobileDisplay()
{
    window.mobilemode=true;
    $(".mobileoff").click(function () {
        $(this).toggle("explode",function(){
            $.ajax({
                type: "POST",
                async: true,
                data: {
                    mobilemode: 0
                },
                success: function (response) {
                    location.reload(true);
                },
                error : function (response) {
                }
            });
        });
    });

}

$(document).ready(function () {
    log("mobile init");
    mobileDisplay();
});
