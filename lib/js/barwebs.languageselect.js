
$(document).ready(function () {
    log("languageselect init");
    $("#language").change(
        function(){ changeLanguage($(this).val());});
});
