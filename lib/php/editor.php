<?php
require_once("editorfields.php");

if(!adminMode())
exit("not admin");

$category=currentPage();
global $_factories;

$subjecturl=isset($_factories["event"][$category]["searchurl"])?$_factories["event"][$category]["searchurl"]:null;
if(!$subjecturl)
$subjecturl="http://www.google.com/search?&q=";
global $localLoadLibs;
$localLoadLibs[]='barwebs.editor.js';
$localLoadLibs[]='datetimepicker/datetimepicker_css.js';
$localLoadLibs[]='external/jquery-ui-timepicker-addon.js';
$newevent=array("id"=>"0","category"=>currentPage());
populateEventObject($newevent);
?>

<form  name="editor" id="editortemplate" class="editortemplate editorcontainer" method="post"">
  <div class="chkgroup togglehelppane">
    <div class="icon adminhelp"></div>
    <div>
      <label for="fhelpchk"><?php echoSysMsg("admin.eventeditor.fhelpchk","Help");?></label>
      <input type="checkbox" name="fhelpchk" class="fhelpchk" value="editorhelp" onClick='if($(this).is(":checked"))$(".editor .editorhelppane").show("slow");else $(".editorhelppane",editor).hide("slow");'/>
    </div>
  </div>
  <div class="basicchk">
    <label for="basicchk"><?php echoSysMsg("admin.editor.basiccheck","basic");?></label>
    <input class="basicchk" type="radio"  name="editorpanechk" checked  onClick='$(".editor .editorpane").hide();$(".basicedits").show();$(this).updateEditorHelpPane($(this).val());' value="basic"/>
  </div>
  <div class="advancedchk">
    <label for="csschk"><?php echoSysMsg("admin.editor.csschk","Display Style");?></label>
    <input class="csschk" type="radio" value="css" name="editorpanechk" onClick='$(".editor .editorpane").hide();$(".cssedits,.idclassedits,.commondisplayedits").show();$(this).updateEditorHelpPane($(this).val());'/>
  </div>
  <div class="advancedchk">
    <label for="fbchk"><?php echoSysMsg("admin.editor.fbookcheck","FB/Ograph");?></label>
    <input class="fbchk" type="radio" value="fb" name="editorpanechk" onClick='$(".editor .editorpane").hide();$(".fbedits").show();$(this).updateEditorHelpPane($(this).val());'/>
  </div>
  <div class="advancedchk">
    <label for="settingchk"><?php echoSysMsg("admin.editor.settingedit","Setting");?></label>
    <input class="settingchk" type="radio" value="setting" name="editorpanechk" onClick='$(".editor .editorpane").hide();$(".settingedits,.tokenedits").show();$(this).updateEditorHelpPane($(this).val());'/>
  </div>
  <div class="advancedchk">
    <label for="embedchk"><?php echoSysMsg("admin.editor.embeddedchk","Include/Embed");?></label>
    <input class="embedchk" type="radio" value="embedded" name="editorpanechk" onClick='$(".editor .editorpane").hide();$(".settingedits,.tokenedits").hide();$(".embeddededits").show();$(this).updateEditorHelpPane($(this).val());'/>
  </div>
  <div class="advancedchk">
    <label for="jschk"><?php echoSysMsg("admin.editor.js","Javascript");?></label>
    <input class="jschk" type="radio" value="javascript" name="editorpanechk" onClick='$(".editor .editorpane").hide();$(".jsedits").css("display",$(this).attr("checked")?"block":"none");$(".jsedits,.tokenedits,.idclassedits").show();$(this).updateEditorHelpPane($(this).val());'/>
  </div>
  <div class="advancedchk">
    <label for="cookies"><?php echoSysMsg("admin.editor.cookieschk","Cookies");?></label>
    <input class="cookieschk" type="radio" value="cookies" name="editorpanechk" onClick='$(".editor .editorpane").hide();$(".cookieedits").show();$(this).updateEditorHelpPane($(this).val());'/>
  </div>
  <div class="editorhelppane">
    <label class="underline"><?php echoSysMsg("admin.eventeditor.editorhelp","Event Editor Entry Field Help");?></label>
  </div>
  <div class="editorpane basicedits">
    <div>
      <div class="twocolumn">
        <div class="eventfieldedit ilb">
	  <label for="fenabled"><?php echoSysMsg("admin.event.fenabled","Enabled");?></label>
	  <input type="checkbox" name="fenabled" class="fenabled" />
        </div>
        <div class="eventfieldedit ilb">
	  <select class="language" name="lang" ><?php echo addLanguageSelectValues(getCurrentLanguage());?></select>
          <ul class="languageflags"></ul>
	  <label class="fduplicate" for="fduplicate"><?php echoSysMsg("admin.event.fduplicate","New");?></label>
	  <input type="checkbox" name="fduplicate" class="nosave fduplicate" />
        </div>
        <div  class="eventfieldedit">
	  <label for="title"><?php echoSysMsg("admin.event.eventtitle","Title");?></label>
	  <textarea name="title" class="title msgtext resizetext" ></textarea>
	  <?php echo clearEditWidget();?>
        </div>
        <div class="eventfieldedit">
	  <label for="description"><?php echoSysMsg("admin.event.description","Event Details")?></label>
	  <textarea name="description" class="description resizetext msgtext"></textarea>
	  <?php echo clearEditWidget();?>
        </div>
        <div class="eventfieldedit">
	  <label for="specialinfo"><?php echoSysMsg("admin.event.specialinfo","Hilite Info")?></label>
	  <textarea name="specialinfo" class="description resizetext msgtext"></textarea>
	  <?php echo clearEditWidget();?>
        </div>
      </div> 
      <div class="twocolumn">
        <div class="dateandtimefields">
          <div class="eventfieldedit">   
	    <label for="startdate"><?php echoSysMsg("admin.event.datetime","Date");?></label>
	    <input  class="startdate" type="text" name="startdate" value=""/>
	    <?php echo clearEditWidget();?>
          </div>
          <div class="eventfieldedit">
	    <label for="duration"><?php echoSysMsg("admin.event.duration","Duration (hh:m)");?></label>
	    <input type="text" name="duration" title="Format is Hours:Minutes" class="duration timeentryfield" value=""/>
	    <?php echo clearEditWidget();?>
          </div>
        </div> <!-- date and time -->
        <div class="ordergroupbyfields">
          <div class="eventfieldedit" >
	    <label for="orderby"><?php echoSysMsg("admin.event.orderby","Order (1 before 2)");?></label>
	    <input type="text" name="orderby" class="orderby" value=""/>
          </div>
          <div class="eventfieldedit" >
	    <label for="groupby"><?php echoSysMsg("admin.event.groupby","Event Group");?></label>
            <input type="text" name="groupby" class="groupby" value=""/>
          </div>
          <div class="eventfieldedit" >
	    <label for="category"><?php echoSysMsg("admin.event.category","Type/Page");?></label>
	    <input type="text" name="category" class="category" value=""/>
          </div>
        </div> <!-- ordergroupby -->
      </div> 
    </div>
    <div class="linksfields">
      <div class="links twocolumn">
        <div class="eventfieldedit" >
          <label for="eventlink"><?php echoSysMsg("admin.event.link","External Click URL");?></label>
          <input  type="text" name="eventlink" class="eventlink" value=""/>
          <?php echo clearEditWidget();?>
          <div class="icon googlebutton editmod" data-url="http://www.google.com/images"></div>
                                                                </div>
                                                                <div class="eventfieldedit" >
                                                                <label for="image"><?php echoSysMsg("admin.event.image","URL of event image");?></label>
	  <input type="text"  name="image"  class="image" value=""/>
	  <?php echo clearEditWidget();?>
	  <div  class="icon googlebutton editmod" data-url="http://www.google.com/images"></div>
        </div>
      </div> <!-- links -->
      <div class="imgpreview twocolumn">
        <?php echo '<img src="common-images/previewicon.png"/>';?>
      </div> <!-- imgpreview -->
    </div> <!-- links container -->
  </div>
  
  <div class="editorpane tokenedits">
    <div class="adminonly eventfieldedit">
      <label for="fadmin"><?php echoSysMsg("admin.editor.fadmin","Admin Only");?></label>
      <input type="checkbox" name="fadmin" class="fadmin" />
    </div>
    <div class="eventfieldedit">
      <label for="token"><?php echoSysMsg("admin.eventeditor.token","Token/Setting Name");?></label>
      <input type="text" <?php if($row["id"]&&($category=="msgcode")) echo 'readonly="readonly" ';?>name="token" class="token" value="<?php echo $row["token"]; ?>"/>
    </div>
  </div>
  <div class="editorpane idclassedits">
    <div class="eventfieldedit">
    <label for="cssclass"><?php echoSysMsg("admin.eventeditor.cssclass","CSS Classes");?></label>
    <input type="text" name="cssclass" class="cssclass" value=""/>
    </div>
    <div class="eventfieldedit">
    <label for="cssid"><?php echoSysMsg("admin.eventeditor.cssid","CSS ID");?></label>
    <input type="text" name="cssid" class="cssid" value=""/>
    </div>
  </div>
  <div class="editorpane cookieedits">
    <div class="eventfieldedit" >
    <label for="cookiename"><?php echoSysMsg("admin.eventeditor.cookiename","PopUp : Cookie Name");?></label>
    <input type="text" name="cookiename" class="cookiename" value=""/>
  </div>
    <div class="eventfieldedit" >
      <label for="cookietimeout"><?php echoSysMsg("admin.eventeditor.cookietimeout","PopUp : Allow event to be closed for N days");?></label>
      <input type="text" name="cookietimeout" class="cookietimeout" value=""/>
    </div>
  </div>


  <div class="editorpane cssedits">
    <div class="eventfieldedit" >
      <label for="csslink"><?php echoSysMsg("admin.eventeditor.csslink","External CSS URL");?></label>
      <input type="text" name="csslink" class="csslink" value=""/>
    </div>
    <div class="eventfieldedit" >
      <label for="cssstyle"><?php echoSysMsg("admin.eventeditor.cssstyle","CSS Styling");?></label>
      <textarea name="cssstyle" class="cssstyle msgtext resizetext"></textarea>
    </div>
    <?php echo clearEditWidget();?>
  </div>
  <div class="editorpane commondisplayedits">
    <div class="eventfieldedit" >
      <label for="attachto"><?php echoSysMsg("admin.eventeditor.attachto","ID to attach to");?></label>
      <input type="text" name="attachto" class="attachto" value=""/>
    </div>
    <div class="eventfieldedit" >
      <label for="displaydelay"><?php echoSysMsg("admin.eventeditor.displaydelay","Display Delay (seconds)");?></label>
      <input type="text" name="displaydelay" class="displaydelay" value=""/>
    </div>
    <div class="eventfieldedit" >
      <label for="displayfor"><?php echoSysMsg("admin.eventeditor.displayfor","Display for (seconds [0==forever]))");?></label>
      <input type="text" name="displayfor" class="displayfor" value=""/>
    </div>
    <div class="adminonly eventfieldedit">
      <label for="fadmin"><?php echoSysMsg("admin.eventeditor.fpopup","Display As PopUp");?></label>
      <input type="checkbox" name="fpopup" class="fadmin" />
    </div>
    <div class="eventfieldedit" >
      <label for="popupposition"><?php echoSysMsg("admin.eventeditor.popupposition","PopUP : position (top,left)");?></label>
      <input type="text" name="popupposition" class="popupposition" value=""/>
    </div>
    <div class="eventfieldedit" >
      <label for="fclosebutton"><?php echoSysMsg("admin.eventeditor.fclosebutton","Close Button");?></label>
	  <input type="checkbox" name="fclosebutton" class="fclosebutton" />
    </div>
  </div>                                                               
  <div class="editorpane fbedits">
    <div class="eventfieldedit">

      <label for="fenableshare"><?php echoSysMsg("admin.eventeditor.fenableshare","Enable Sharing");?></label>
      <input type="checkbox"  name="fenableshare" class="fenableshare" />
    </div>
    <div class="eventfieldedit">

      <label for="fbcomments"><?php echoSysMsg("admin.eventeditor.fbcomments","Enable FB Comments");?></label>
      <input type="checkbox" name="fbcomments" class="fbcomments" />
    </div>
    <div class="eventfieldedit">

      <label for="ogtype"><?php echoSysMsg("admin.eventeditor.ogtype","OpenGraph Type");?></label>
      <input type="text" name="ogtype" class="ogtype" value=""/>
    </div>
    <div class="eventfieldedit">

      <label for="ogactions"><?php echoSysMsg("admin.eventeditor.ogactions","OpenGraph Action Types");?></label>
      <input type="text" name="ogactions" class="ogactions" value=""/>
    </div>
    <div class="eventfieldedit">
      <label for="mdtype"><?php echoSysMsg("admin.eventeditor.mdtype","Microdata type");?></label>
      <input type="text" name="mdtype" class="mdtype" value=""/>
    </div>              
    <div class="eventfieldedit">
      <label for "fjsonenabled"><?php echoSysMsg("admin.eventeditor.fjsonenabled","Allow JSON Fetch");?></label>
      <input type="checkbox" name="fjsonenabled" class="fjsonenabled" />
    </div>
  </div>
  <div class="editorpane settingedits">
    <div class="eventfieldedit">
      <label for="setting"><?php echoSysMsg("admin.eventeditor.setting","Setting value");?></label>
      <textarea name="setting" class="setting msgtext resizetext"></textarea>
    </div>
    <?php echo clearEditWidget();?>
    <label for="guard"><?php echoSysMsg("admin.eventeditor.guard","Guard");?></label>
    <select class="guard guardselect thinborder" name="guard" ><?php echo addGuardSelectValues("noGuard");?></select>
    <?php echo clearEditWidget();?>
  </div>
  <div class="editorpane embeddededits">
    <div class="eventfieldedit" >
      <label for="embeddedlist"><?php echoSysMsg("admin.eventeditor.embeddedlist","Embeddedlist (category,group[,number]");?></label>
      <textarea name="embeddedlist" class="embeddedlist msgtext resizetext"></textarea>
      <?php echo clearEditWidget();?>
    </div>
    <div class="eventfieldedit" >
      <label for="embed"><?php echoSysMsg("admin.eventeditor.embed","Embedded Code");?></label>
      <textarea name="embed" class="embed msgtext resizetext htmleditor"></textarea>
      <?php echo clearEditWidget();?>
    </div>
    <div class="eventfieldedit" >
      <label for="includelink"><?php echoSysMsg("admin.eventeditor.includelinks","User Include Link");?></label>
      <input name="includelink" class="includelink" type="text"/>
      <?php echo clearEditWidget();?>
    </div>
  </div>
  <div class="editorpane jsedits">
    <label for="javascriptlink"><?php echoSysMsg("admin.eventeditor.jslink","External Javascript URL");?></label>
    <input type="text" name="javascriptlink" class="jslink" value=""/>
    <label for="javascript"><?php echoSysMsg("admin.eventeditor.js","Javascript");?></label>
    <textarea name="javascript" class="js msgtext resizetext"></textarea>
    <?php echo clearEditWidget();?>
  </div>
  <input value="Save" type="submit"/>
  <div class="hidden">
    <input type="hidden" name="id" class="id"/>
    <input type="hidden" name="languages" class="languages"/>
    <input type="hidden" name="factory" class="factory" value="event"/>
  </div>
</form>
<script type="text/javascript">
 var imgwin;
 var subjectwin;
</script>
