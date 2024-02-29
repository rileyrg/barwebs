<?php

global $_factories;

$_factories{"default"}{"default"}="print_r";

$_factories{"event"}{"container"}{"default"}="displayEvent";
$_factories{"event"}{"content"}{"default"}="singleEventHTML";
$_factories{"settings"}{"container"}{"default"}="displayEvent";
$_factories{"settings"}{"content"}{"default"}="getSettingHTML";

$_factories{"event"}{"table"}="events";
$_factories{"event"}{"auth"}="hasAuthority";
$_factories{"event"}{"populate"}="populateEventObject";
$_factories{"event"}{"list"}="getEventsHTML";
$_factories{"event"}{"editform"}="createEventForm";

$_factories{"event"}{"options"}{"filterfields"}="event,title";
$_factories{"event"}{"options"}{"fields"}=array ("id","token","category","embed","cssclass","orderby","groupby","startdate","fenabled","fenableshare","fadmin","duration","eventlink","image");
$_factories{"event"}{"options"}{"id"}="id";

$_factories{"event"}{"nodeleteclass"}{"meta"}=true;

$_factories{"event"}{"news"}{"time"}="08:00";
$_factories{"event"}{"news"}{"defaultdate"}=TRUE;

$_factories{"event"}{"*"}{"preuserincludes"}=array();

$_factories{"event"}{"pubquiz"}{"time"}="21:00";
$_factories{"event"}{"pubquiz"}{"defaultdate"}=TRUE;
$_factories{"event"}{"pubquiz"}{"searchurl"}="http://en.wikipedia.org/w/index.php?title=Special%3ASearch&search=";


$_factories{"event"}{"page"}{"classes"}="";
$_factories{"event"}{"footerlink"}{"classes"}="";

$_factories{"adminpage"}{"page"}=true;
$_factories{"adminpage"}{"footerlink"}=true;
$_factories{"adminpage"}{"settings"}=true;
$_factories{"adminpage"}{"bug"}=true;
?>
