<?php
global $localLoadLibs;
$localLoadLibs[]="barwebs.contactform.js";
?>
<link rel="stylesheet" href="contactform.css" type="text/css">
<form title="<?php echoSysMsg("sendmail.title","Contact Form");?>" id="contactform" class="contactform noprint roundedcorners thinborder">
  <label for="fullname">
    <?php echoSysMsg("admin.sendmail.fullname","Your Full Name");?>
  </label>
  <input  class="inputfield"  required type="text" minlength="10" name="fullname" value="<?php echo isset($_SESSION['sendmail.fullname'])?$_SESSION['sendmail.fullname']:"";?>"/>
  <label for="email">
    <?php echoSysMsg("admin.sendmail.emailaddress","Your Email Address For Us To Reply To");?>
  </label>
  <input class="inputfield" type="email" size="22" required name="email" value="<?php echo isset($_SESSION['sendmail.email'])?$_SESSION['sendmail.email']:""?>"/>
  <label for="phone">
    <?php echoSysMsg("admin.sendmail.phone","Your phone number");?>
  </label>
  <input class="inputfield optional" type="text" name="phone" value="<?php echo isset($_SESSION['sendmail.phone'])?$_SESSION['sendmail.phone']:""?>"/>
  <label for="emailbody">
    <?php echoSysMsg("admin.sendmail.emailbody","Enter Your Message Text Below And Click Submit");?>
  </label>
  <textarea  minlength="32" rows="5"  required name="emailbody" class="emailbody inputfield">
    <?php echo isset($_SESSION['sendmail.emailbody'])?$_SESSION['sendmail.emailbody']:""?>
  </textarea>
<?php
    require_once("spamcheck.php");
?>
  <input value="Send" type="submit" class="contactformsubmit"/>
</form>
