<?php

require_once("eventfuncs.php");

function icalEscape($s)
{
	return str_replace(";","\;",str_replace(",","\,",$s));
}

function generateICAL($category=null){ /* an eventtoken==ical must in the category to make export */
	$sql='select *,(select msgtext from eventmsgcodes where   (eventid=events.id) and field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") limit 1) as title, (select msgtext from eventmsgcodes where (eventid=events.id) and field="description" and   (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") limit 1) as description from events where '.($category?'category="'.$category.'" and ':'').'startdate<>"" and (unix_timestamp(startdate) >= "'.expireTime(localSiteTime(time())).'") and fenabled=1 order by orderby,startdate';

    global $mysqli;
	$result=$mysqli->query($sql);
	if(!$result)
		return;
	
	if(($numrows=$result->num_rows)==0){
		$result->free();
		return 0;
	}
	
	$email=icalEscape(getSetting("email","contact"));
	$ICALlocation=icalEscape(shareURL($row));
	$ICALorganiser=icalEscape(getSetting("ICAL:organiser"));
	$ICALUIDhost=icalEscape(getSetting("ICAL:UIDhost"));
	
	while($row=$result->fetch_assoc()){
		$dur=$row{"duration"};
		if(!$dur)
			$dur=120; /* default to 2 hours*/
		echo "BEGIN:VEVENT\n";
		$type=sysMsg("links.".$row{"category"});
		$desc=icalEscape(shareTitle($row));
		echo "UID:".md5($desc)."@".$ICALUIDhost."\n";
		echo "DTSTART:".($uid=icalDate($row{"startdate"}))."\n";
		echo "DURATION:PT".((int)($dur/60))."H".($dur%60)."M\n";
		echo "SUMMARY;LANGUAGE=us-EN:".$desc."\n";
		echo "DESCRIPTION;LANGUAGE=us-EN:".icalEscape($row{"description"}.($row{"specialinfo"}?" : ".$row{"specialinfo"}."":""))."\n";
		echo "LOCATION:".icalEscape(getSetting("ICAL:location"))."\n";
		echo "URL:".icalEscape(shareURL($row))."\n";
		echo "CLASS:PUBLIC\n";
		echo "TRANSP:TRANSPARENT\n";
		echo "CATEGORIES:".$row{"category"}."\n";
		echo "ORGANIZER;CN=".$ICALorganiser.":MAILTO:".$email."\n";
		echo "END:VEVENT\n";
	}
	
	$result->free();
	
}


?>