<?php if(!adminMode())exit("not admin");?>

  <div id="admintoolbar" class="focuscontrol adminpane admintoolbar draggable ">
    <div class="admindrag icon" title="move"></div>
    <div class="webmail  icon" title="WebMail"></div>
    <div class="adminhelp icon" title="Help"></div>
    <div class="drawerhandle  icon"></div>
    <div class="admintoolbardrawer drawer">
      <div class="adminnewevent icon" title="Add New"></div>
      <div class="nerdbadge icon" title="Enable Event Editing (nerd mode)"></div>
      <div class="admintoggledisabled nerdmodeonly icon" title="Toggle disabled and advanced edit badges"></div>
      <div class="adminmetawizard  advancednerd icon" title="Meta Data Wizards"></div>
      <div class="adminpages advancednerd icon" title="Navigation Links"></div>
      <div class="adminfooters advancednerd icon" title="Footer Links"></div>
      <div class="adminsettings advancednerd icon"  title="Web Wide Settings"></div>
      <div class="adminvideos icon" title="Information Videos"></div>
    </div>
    <div class="closebutton icon" title="Exit AdminMode"></div>
  </div>

<form name='webmail' action='<?php echo webmailLoginURL();?>'  target="_blank" method=POST>
 <input type=hidden name='user' value='<?php echo getSetting("host:webmailuser");?>'/>
 <input type=hidden name='pass' value='<?php echo getSetting("host:webmailpass");?>'/>
</form>

  <?php
  global     $localLoadLibs;
  $localLoadLibs[]='barwebs.admintoolbar.js';
  form
  ?>
