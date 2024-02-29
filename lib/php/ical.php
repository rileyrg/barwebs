<?php require_once("utils.php"); require_once("ical-lib.php");?>BEGIN:VCALENDAR
VERSION:2.0
X-WR-CALNAME:<?php echo getSetting("ICAL:AllTitle")."\n";?>
PRODID:<?php echo getSetting("ICAL:ProdID")."\n";?>
X-WR-TIMEZONE:<?php echo getSetting("ICAL:TimeZone")."\n";?>
CALSCALE:GREGORIAN
<?php generateICAL($_GET["page"]);?>END:VCALENDAR
