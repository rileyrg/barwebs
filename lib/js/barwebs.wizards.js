function wizardEdit(wizards,i,wizmenu)
{
    wizard=wizards[i];
    var wizeditor=$('<form class="wizardeditor editorcontainer"/>');
    for (var f in wizard.fields){
        if (!wizard.fields.hasOwnProperty(f)) {
            //The current property is not a direct property of p
            continue;
        }
    
        fieldmeta=wizard.fields[f];

        var nf;

        if(fieldmeta.type!=="hidden"){
            // copy the fields from the main editor
            nf=$("#editortemplate").find('[name="'+f+'"]:first').closest(".eventfieldedit").clone(true).addClass("wizardeditorfield");
            nf.find("label").text(fieldmeta.label.en);
            var f2=nf.find("input,textarea").addClass("inputfield");
            if(f2){
                if(fieldmeta.validation){
                    for(var v in fieldmeta.validation){
                        if(fieldmeta.validation.hasOwnProperty(v)){
                            if(fieldmeta.validation[v].length)
                                f2.attr(v,fieldmeta.validation[v]);
                            else
                                f2.prop(v,true);
                        }

                    }
                }
            }
        }else{
            // hidden so no need to do anything fancy.
            nf=$('<div><input name="'+f+'"></input></div>');
        }

        nf.addClass(fieldmeta.type);

        if(f=="category" && fieldmeta.value=="*"){
            fieldmeta.value = curpage(); /* so dont pick up default which might be a data page */
        }

        if(typeof fieldmeta.value !== "undefined"){
            $v=fieldmeta.value;
            nf.find("[name]").val($v);
        }

        wizeditor.append(nf);
    }

    $("textarea,:checkbox,input", wizeditor).addClass("changed");

    wizeditor.data("cssclasses",wizard.classes).append($('<input value="save" type="submit">').button());

    wizeditor.append($('<div class="help">'));
    if (wizard.icon){
        $(".help",wizeditor).append($("<img>").attr("src",wizard.icon).addClass("wizardicon"));
    }
    if(typeof wizard.help!=="undefined"){
        if(typeof wizard.help.url != "undefined")
            $(".help",wizeditor).append($('<a title="Click for help" href="'+wizard.help.url+'">').append($('<div class="adminhelp icon">')));
        if(typeof wizard.help.sample != "undefined")
            $(".help",wizeditor).append($('<span>').text(wizard.help.sample));
    }

    $(".startdate",wizeditor).datetimepicker({
        dateFormat: 'yy-mm-dd',
        numberOfMonths: 2,
        stepMinute: 15,
        separator: ' '
    });

    wizeditor.validate({
        submitHandler : function(form){
            if($('[name="category"]',form).length===0){
                $(form).append($('<input name="category" class="changed"/>').val(curpage()));
            }
            $(form).saveEvent(true);
            return false;
        }
    });
    
    wizeditor.dialog({
        modal: false,
        autoOpen: true,
        resizeable: true,
        closeOnEscape: true,
        title : wizard.title.en + " : " + wizard.description.en,
        width:"38em"
    });


}

function buildWizardUI(wizards,connectTo,c,t,missing)
{

    if(isEmpty(wizards))
        return;

    if(typeof t === "undefined")
        t="Wizards";
    var wizmenu=$('<div class="focuscontrol removeable wizardmenu roundedcorners draggable '+c+'"/>');
    wizmenu.append($('<h3>'+t+'</h3>'));

    for (var i in wizards){
        try{
            var desc=wizards[i].description.en;
            var tit=wizards[i].title.en;
            var icon=wizards[i].icon;
            var important=wizards[i].shouldhave;
            var autoopen= wizards[i].autoopen==true||wizards[i].autoopen==curpage();
            var wc=wizards[i].wizardclass+(important ?" important":"");
            var wizchoice=$('<div class="wizardchoice'+(autoopen?" autoopen":"")+'" title="'+desc+'" data-wizardname="'+i+'"><div class="wizardchoicetitle">'+tit+'</div><img src="'+icon+'"></div></div>').click(function(e){wizardEdit(wizards,$(this).attr("data-wizardname"),$(this).closest(".wizardmenu"));}).addClass(wc);
            wizmenu.append(wizchoice);
            if(important){
                missing.push({description:desc,wizardbutton: wizchoice});
            }else{
                wizchoice.addClass("hoverbright");
            }
        }catch(err){
            log("error building wizards",err);
            alert("Error building wizards : please alert Richie!");
        }
    }
    (connectTo||$("body")).prepend(wizmenu);
}

$(document).ready(function () {

    var missing= [];

    buildWizardUI(wizardjson,null,"standardwizards","Add Events/Items",missing);
    buildWizardUI(metawizardjson,null,"metawizards","Meta Data Wizards",missing);

    if(missing.length){
        var missingUI=$('<div>').append($('<div  style="margin:0 auto;" class="adminwizard icon"/>')).append($('<div class="wizardmissingsettings"/>'));
        for (var i = 0; i < missing.length; i++) {
            var el=$('<div class="wizardmissingsetting">').append($('<div class="adminnewevent icon"/>')).append($('<span class="wizardtext"/>').text(missing[i].description));
            el.data("wizard",missing[i].wizardbutton).click(function(e){$(this).data("wizard").click();$(this).fadeOut(3000,function(){$(this).remove();$(".wizardmissingsettings:empty").closest(".editorcontainer").fadeOut("3000");});});
            $(".wizardmissingsettings",missingUI).append(el);
        }
        missingUI.dialog({
            modal: false,
            position:"center top",
            autoOpen: true,
            resizeable: true,
            closeOnEscape: true,
            width:'auto',
            title : "Missing Meta Settings: on the "+curpage()+" page"
        });
    }
    $(".wizardmenu .autoopen").click();
    log("wizards init");
});
