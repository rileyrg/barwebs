function contactFormInit()
{
    $(".contactform").validate({
        submitHandler : function(form){
            $(form).showSaving(true);
            $cf={
                        fullname: $('[name="fullname"]',form).val(),
                        email:$('[name="email"]',form).val(),
                        phone:$('[name="phone"]',form).val(),
                        emailbody:$('[name="emailbody"]',form).val()
            };
            log($cf);
            $.ajax({
                type: "POST",
                async: true,
                context:form,
                data: {
                    contactform: JSON.stringify($cf),
                    captcha: $('[name="captcha"]',form).val()
                },
                dataType: "json",
                success: function (response) {
                    $(this).showSaving(false);
                    if(response.success)
                        eventEl($(this)).fadeOut("slow");
                    alert(response.response);
                    return false;
                },
                error : function (response) {
                    $(this).showSaving(false);
                    alert("Unhandled error Sending ContactForm");
                    return false;
                }
            });
            return false;
        }
    });

} 

$(document).ready(function () {
    log("contactform init");
    contactFormInit();
});
