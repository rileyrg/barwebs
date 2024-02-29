-- move all text into msgcodes
-- insert into msgcodes (id,lang_id,msgtext)  select concat(id,"-event"),1,event from events;
-- insert into msgcodes (id,lang_id,msgtext)  select concat(id,"-title"),1,title from events;
-- alter table events drop title;
-- alter table events drop event;

-- added code field to languages to hold two letter language code

-- move to fkey constraint from msgcodes
-- alter table languages drop column code;
-- alter table languages add column code char(2) after id;
-- update languages set code=lcase(substring(language FROM 1 FOR 2));
-- alter table msgcodes change id id varchar(32);
-- alter table languages drop column field;
-- alter table msgcodes add column field varchar(32) after lang_id;
-- alter table msgcodes drop column fkey;
-- alter table msgcodes add column fkey int(10) unsigned after lang_id;
-- alter table msgcodes ENGINE = InnoDB;
-- alter table events ENGINE = InnoDB;
-- update msgcodes set fkey=SUBSTRING_INDEX(id,"-",1) where id  REGEXP '^[0-9]+-';
-- update msgcodes set field=SUBSTRING_INDEX(id,"-",-1) where id  REGEXP '^[0-9]+-';
-- alter table msgcodes add constraint deletecodes foreign key (fkey) references events (id) on delete cascade;


-- get coalesce working properly.
-- alter table msgcodes change msgtext msgtext varchar(4096) NULL;
-- update msgcodes set msgtext=NULL where msgtext="";

-- delete from msgcodes where id="";
-- alter table msgcodes drop primary key;
-- update msgcodes set id=SUBSTRING_INDEX(id,"-",1) where id  REGEXP '^[0-9]+-';

-- move nav links into events

-- alter table msgcodes drop primary key;
-- ALTER TABLE msgcodes ADD PRIMARY KEY (id,db,lang_id,field);

-- alter table events add column fadmin tinyint(1);
-- alter table events add column fenabled tinyint(1) default 1;

-- alter table events change column category category varchar(16);
-- alter table events change column eventurl eventurl varchar(128);
-- alter table events add unique index(category,eventurl);
-- delete from events where category="page";
-- insert into events (category, token, eventurl,orderby,image,fadmin,fenabled) select "page", "navigation",pageurl,ord,image,fadmin,fenabled from pages;
-- drop table pages;
-- alter table events change column orderby orderby smallint;

-- insert into msgcodes (id,msgtext) values ("admin.event.orderby", "Order (1-100 : 0==Top, 1000=Bottom)");
-- insert into msgcodes (id,msgtext) values ("admin.event.disabled", "**DISABLED**");

-- alter table events change column orderby orderby smallint default 99;
-- alter table events add column pageid varchar(32);
-- SET FOREIGN_KEY_CHECKS = 0;
-- delete from events where category="page";
-- SET FOREIGN_KEY_CHECKS = 1;
-- insert into events (pageid, category, token, eventurl,orderby,image,fadmin,fenabled) select id,"page", "navigation",pageurl,ord,image,fadmin,fenabled from pages;
-- update msgcodes,events set msgcodes.fkey = events.id, msgcodes.field="title"   where events.pageid = msgcodes.id;
-- delete from events where fenabled=0 and category="page";
-- update events set fadmin=0  where fadmin is null;
-- insert into msgcodes (id,lang_id,msgtext)  values("admin.event.category",1,"Page Name");
-- alter table events drop column pageid;

-- insert into msgcodes (id,lang_id,msgtext)  values("admin.offline",1,"This web has been taken offline");

-- insert into msgcodes (id,msgtext) values ("admin.event.fadmin", "Admin Only");

-- alter table msgcodes add constraint deletecodes foreign key (fkey) references events (id) on delete cascade;

-- drop table images;
-- CREATE TABLE `images` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `fkey` int(10) unsigned DEFAULT NULL,
--   `image` blob,
--   `name` varchar(30) NOT NULL,
--   `type` varchar(30) NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `deleteimages` (`fkey`),
--   CONSTRAINT `deleteimages` FOREIGN KEY (`fkey`) REFERENCES `events` (`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB;

-- alter table events add column embed varchar(1024) after category;

-- insert into msgcodes (id,msgtext) values ("admin.event.share", "Share Me!");
-- insert into msgcodes (id,msgtext) values ("admin.event.share.description", "Something is occurring ... yessur....");

-- alter table events add column fenableshare tinyint(1)  default 1 after fadmin ;

-- insert into msgcodes (id,msgtext) values ("admin.event.fenableshare", "Enable Sharing");
-- update events set fenableshare=1;
-- update msgcodes set msgtext="" where id="admin.event.share";

-- insert into msgcodes (id,msgtext) values ("admin.event.emptyform", "Start New");
-- insert into msgcodes (id,msgtext) values ("admin.event.save", "Save");

-- insert into msgcodes (id,msgtext) values ("admin.setting.edit", "Edit Setting");
-- insert into msgcodes (id,msgtext) values ("admin.setting.new", "New Setting");
-- update events set token="" where token="navigation";

-- insert into msgcodes (id,msgtext) values ("admin.setting.title", "*setting*");
-- update events set startdate =null  where token<>"";
-- update events set token="contact.email"  where token="email";
-- update events set fadmin=0 where token<>"" and token <> "meta";
-- update events set fadmin=1 where token="web.title";


-- update events set token="email" where token="contact.email";
-- update events set token="phone" where token="contact.phone";

-- insert into msgcodes (id,msgtext) values ("admin.delete.confirm", "Really Delete This Item?");

-- update msgcodes set msgtext="Send A Message" where msgtext like "Use this form to send me a message%";
-- alter table events modify class varchar(1024);

-- insert into fleetenkieker.users (id,usertype,password,priviliges) values ("ml","usertype.admin",md5("jack0404"),"A");

-- insert into events (category, image, eventurl,fadmin,fenabled) values ("page","common-images/settings.png","setting",1,1);

-- update events set image="common-images/title.png" where token="web.title";
-- update events set image="common-images/google.png" where token="meta";
-- update events set image="common-images/map.gif" where token="map";

-- insert into msgcodes (id,msgtext) values ("admin.event.duplicate", "Duplicate");


-- update events set token="webtitle" where token="web.title";
-- update events set token="css" where token="css.custom";

-- insert into fleetenkieker.users (id,usertype,password,priviliges) values ("fleet","usertype.admin",md5("fleet123"),"A");
-- insert into msgcodes (id,msgtext) values ("admin.sendmail.failed", "Sending failed. Please contact us by phone or via our email address.");


-- update events set image="" where token <>"";


-- insert into msgcodes (id,msgtext) values ("admin.reset", "Reset");
-- insert into msgcodes (id,msgtext) values ("admin.clear", "Clear");
-- insert into fleetenkieker.users (id,usertype,password,priviliges) values ("eob","usertype.admin",md5("eob123"),"A");

-- alter table events add column groupby varchar(32) after orderby;

-- insert into msgcodes (id,msgtext) values ("admin.event.groupby", "Group By");

-- insert into events (category, image, eventurl,orderby,groupby,fadmin,fenabled) values ("page","common-images/bugs.png","bug","998","",1,1);
-- update events set groupby="default";
-- update event set groupby ="z-group" where groupby="default";

-- insert into gerrydoylemusic.users (id,usertype,password,priviliges) values ("lb","usertype.admin",md5("lb123"),"A");

-- UPDATE events SET token=CONCAT(SUBSTRING(token,1,4),":",SUBSTRING(token,5)) WHERE token like "ical%" and substring(token,5,1)<>":";
-- UPDATE events SET token=CONCAT(SUBSTRING(token,1,6),":",SUBSTRING(token,7)) WHERE token like "google%" and substring(token,7,1)<>":";

-- alter table msgcodes drop primary key;
-- insert into events (comments,class,orderby,groupby,startdate,duration,fadmin,fenableshare,category) select msgtext,id,lang_id,CONCAT("msgcode-",lang_id),null,0,1,0,"msgcode" from msgcodes where id like "admin.%" and length(msgtext)>0;
-- alter table msgcodes add column token varchar(128);
-- insert into msgcodes (msgtext,fkey,lang_id,field,token) select comments,id,orderby,"event",class from events where category="msgcode";
-- insert into msgcodes (msgtext,fkey,lang_id,field,token) select class,id,orderby,"title",class from events where category="msgcode";
-- delete from msgcodes where id like "admin.%";
-- update events set token="",class="",comments="",orderby=0  where category="msgcode";
-- alter table msgcodes drop id;
-- insert into events (category, token, image, eventurl,orderby,groupby,fadmin,fenabled) values ("page","","common-images/msgcodes.png","msgcode","103","default",1,1);
-- CREATE TABLE  `facebookuser` (`userid` BIGINT( 20 ) NOT NULL ,`lastlogin` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,`numlogins` INT NOT NULL DEFAULT  '1',`email` VARCHAR( 32 ) NOT NULL);
-- ALTER TABLE  `facebookuser` ADD  `accesstoken` VARCHAR( 64 ) NOT NULL COMMENT  'last access id';
-- ALTER TABLE  `facebookuser` CHANGE  `accesstoken`  `accesstoken` VARCHAR(64) COMMENT  'last access id';
-- ALTER TABLE  `facebookuser` ADD UNIQUE (`userid`);
-- ALTER TABLE  `facebookuser` ADD  `fullname` VARCHAR(128);
-- ALTER TABLE  `facebookuser` CHANGE  `birthday`  `birthday` VARCHAR( 32 )
-- update events set groupby="facebook" where token like "fb:%";
-- update events set groupby="opengraph" where token like "og:%";
-- update events set groupby="css" where token ="css";
-- update events set groupby="csslinks" where token ="css-link";
-- update events set groupby="system" where token like "sys:%";
-- update events set groupby="lists" where token like "list:%";
-- update events set groupby="ICAL" where token like "ICAL%";
-- update events set groupby="meta" where token ="meta";
-- update events set token = (select token from msgcodes where msgcodes.fkey= events.id and lang_id=1 and msgcodes.field="title")  where category="msgcode";
-- update events set groupby="adminmessages" where token like "admin.%";
-- delete from  msgcodes using msgcodes  inner join events on msgcodes.lang_id<>1 and msgcodes.fkey=events.id and events.token like "css%";
-- ALTER TABLE  `msgcodes` CHANGE  `lang_id`  `lang` CHAR(2);
-- update msgcodes set lang="en" where lang=1;
-- update msgcodes set lang="de" where lang=2;
-- update msgcodes set lang="da" where lang=3;
-- alter table languages drop db;
-- alter table languages drop id;
-- alter table languages drop createdby;
-- alter table languages drop updatedby;
-- alter table languages drop createdd;
-- alter table languages drop lastupdd;
-- ALTER TABLE languages CHANGE  code  lang CHAR(2);
-- ALTER TABLE languages ADD PRIMARY KEY (lang);
-- update languages set lang="da" where lang like "d%" and lang <> "de";
-- drop table languages;
-- drop table  admin         ;
-- drop table  categories    ;
-- drop table  contacts      ;
-- drop table  loginattempts ;
-- drop table  pages         ;
-- drop table  players       ;
-- drop table  playertypes   ;
-- drop table  questions     ;
-- drop table  teams         ;
-- drop table  teamtypes     ;
-- drop table  usertypes ;    
-- alter table events drop column db;
-- alter table events add column fjsonenabled tinyint(1) default 1 after fadmin;
-- update events set token=lcase(token);
-- update msgcodes set token=lcase(token);
-- update events set groupby=lcase(groupby);

-- alter table  msgcodes alter column lang set default "en";
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.fjsonenabled","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.fjsonenabled","JSON Enabled","event",last_insert_id());

-- update events set duration=null where duration=0 or startdate="" or startdate is null;

-- alter table users add column expires DATETIME default null after password;

-- insert into events (category, groupby, token, eventurl,orderby,image,fadmin,fenabled) values("page","default","","http://www.youtube.com/user/MrRichiehamburg#p/c/0690D9C115AA780C",998,"common-images/youtube.png",1,1);

-- insert into events (category, groupby, token, eventurl,orderby,image,fadmin,fenabled) values("adminlogin","default","fb:enablelogin","",0,"",1,1);
-- insert into events (category, groupby, token, eventurl,orderby,image,fadmin,fenabled) values("adminlogin","default","fb:enablelike","",0,"",1,1);

-- insert into events (category, groupby, token, eventurl,orderby,image,fadmin,fenabled) values("page","default","","http://www.youtube.com/user/MrRichiehamburg#p/c/0690D9C115AA780C",998,"common-images/youtube.png",1,1);

-- update events set token="ev:duration" where token="duration";
-- update events set token="ev:orderby" where token="orderby";
-- update events set token="ev:groupby" where token="groupby";
-- update events set token="ev:class" where token="class";
-- update events set token="ev:fenableshare" where token="ev:fenableshare";
-- update events set token="ev:fenablejson" where token="ev:fenablejson";
-- update events set groupby ="eventdefaults" where token like "ev:%";

-- delete from events where category="";

-- update events set token="css" where token="css-link";
-- update events set token ="sys:phone" where token="phone";
-- update events set groupby ="system" where token="sys:phone";
-- update events set token ="sys:email" where token="email";
-- update events set groupby ="system" where token="sys:email";
-- insert into richardriley.events (category, embed,token, groupby,eventurl,class,orderby,image,fadmin,fenabled) select category, embed, token, groupby,eventurl,class,orderby,image,fadmin,fenabled from shamrockirishbar.events where category="photo";

-- update events set token="ev:defaulttime" where token like "defaulttime";
-- update events set token="ev:defaulduration" where token like "%duration%";
-- update events set groupby="eventdefaults" where token like "ev:%";
-- update events set token="ev:defaultclasses" where token like "list:cla%";
-- update events set image="common-images/admin" where eventurl="adminlogin";
-- update events set class="adminloginlink" where eventurl="adminlogin";
-- update events set eventurl="http://barwebs.com" where eventurl like "%richardriley%";
-- update events set category="features" where groupby="hilites";
-- update events set token="sys:address" where token="address";
-- update events set category="footerlink" where eventurl="adminlogin";
-- update events set groupby="default" where category="footerlink";
-- update events set orderby="999" where eventurl="adminlogin";

-- alter table events add column fdelayembed tinyint(1) default 0;
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.fdelayembed","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.fdelayembed","Delay Embed","event",last_insert_id());

-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.delayembed","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.delayembed","Click here to load the plugin!","event",last_insert_id());

-- update users set id="admin" where id="rgr";

-- update  msgcodes set msgtext="Click to see further information!" where token="admin.event.delayembed";

-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.changelanguage","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.changelanguage","Change Language","event",last_insert_id());
-- update events set image="common-images/legal.png" where eventurl="impressum";

-- update events set category="footerlink", groupby="default"  where eventurl="link";
-- update events set image="common-images/links.png" where eventurl="link";
-- update events set groupby="announce"  where category="bio";


-- alter table events add column jquery  varchar(1024) after embed;
-- alter table events add column style  varchar(1024) after class;
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.style","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.style","CSS Style","event",last_insert_id());
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.jquery","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.jquery","JQuery Code","event",last_insert_id());
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.showadvanced","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.showadvanced","Click checkbox to see advanced options..","event",last_insert_id());
-- insert into events (category,fadmin,token,groupby) values ("setting","1","sys:disableshare_page","system");
-- insert into msgcodes (token,msgtext,field,fkey) values("sys:disableshare_page","1","event",last_insert_id());
-- insert into events (category,fadmin,token,groupby) values ("setting","1","sys:disableshare_footerlink","system");
-- insert into msgcodes (token,msgtext,field,fkey) values("sys:disableshare_footerlink","1","event",last_insert_id());
-- update events set fenableshare=0 where category="page";
-- update events set fenableshare=0 where category="footerlink";

-- alter table events add column fbcomments tinyint(1)  default 0 after fdelayembed ;
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.fbcomments","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.fbcomments","Enable FB Comments","event",last_insert_id());

-- alter table events add column eventuid varchar(128) after class;
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.eventuid","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.eventuid","#ID","event",last_insert_id());

-- UPDATE msgcodes SET msgtext = REPLACE(msgtext, "#center", ".center") WHERE msgtext LIKE "%#center%";
-- UPDATE msgcodes SET msgtext = REPLACE(msgtext, "#body", "body") WHERE msgtext LIKE "%#body%";

-- update events set eventurl ="act_adminlogout" where eventurl="adminlogout";

-- UPDATE msgcodes inner join events on msgcodes.lang="en"	and msgcodes.fkey=events.id SET events.style = msgcodes.msgtext WHERE events.token like "css%";
-- DELETE  msgcodes from msgcodes join events on msgcodes.fkey=events.id and  events.token like "css%";

-- alter table events add column ogtype varchar(128) after fbcomments;
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.ogtype","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.ogtype","Open Graph Type","event",last_insert_id());

-- alter table events add column setting varchar(4096) after ogtype;
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.setting","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.setting","Setting Value","event",last_insert_id());

-- alter table events add column ogactions varchar(128) after fbcomments;
-- insert into events (category,fadmin,token,groupby) values ("msgcode","1","admin.event.ogactions","adminmessages");
-- insert into msgcodes (token,msgtext,field,fkey) values("admin.event.ogactions","Open Graph Actions (comma seperated)","event",last_insert_id());
-- update events set ogactions="join,interest" where category="pubquiz" and startdate <> "";
-- update events set ogactions="attend" where category="tvsport" and startdate <> "";

-- drop table contacts,players,playertypes,questions,teams,teamtypes,pages,languages,images;
-- update events set token="ev:fenableshare" where token="ev:enableshare";
-- update events set fenableshare="" where length(token)>0;
-- alter table events add column csslink varchar(256) after style;
-- alter table events add column jslink varchar(256) after csslink;
-- alter table events change style cssstyle varchar(1024);
-- alter table events change jquery js varchar(1024) after jslink;
-- alter table events change class cssclass varchar(1024);
-- update events set token="ev:cssclass" where token="ev:class";
-- update events set token="ev:js" where token="ev:jquery";
-- update events set token="ev:cssstyle" where token="ev:style";
-- update msgcodes set msgtext="CSS ID" where token="admin.event.eventuid";
-- update msgcodes set token="admin.event.cssclass" where token="admin.event.class";
-- update msgcodes set token="admin.event.cssstyle" where token="admin.event.style";
-- update msgcodes set token="admin.event.js" where token="admin.event.jquery";
-- alter table msgcodes drop column db;
-- insert into msgcodes (token,msgtext) values ("admin.eventeditor.pane-basic","Basic settings. Click the google buttons by the Image URL field for google to find you some images based on the other text fields. Don't forget to set the date and duration! The event will automatically disappear after its scheduled time. No need for you to do anything. If you have two events with no date then set the Order field to determine which appears first. If you wish the events to appear together assign them the same Group value. If you wish to move the event to another page simply update the Event Type field.");

-- insert into msgcodes (token,msgtext) values ("admin.eventeditor.pane-fb","Edit these settings to allow the event to be shared via FB and other media");

-- insert into msgcodes (token,msgtext) values ("admin.eventeditor.pane-embedded","To embed something like a slideshow into an Event simply paste the object code into the Embed field. If you feel this embedded control is too heavy then select 'Delay Embed' : the user will need to click the control to fetch it. To embed pure html (no event wrapper) then set Token to 'userhtml' and paste the html into the Embed field. To include an external html file with no Event wrapper then set Token to 'userinclude' and put the URL of the external file into the Setting field. ");

-- insert into msgcodes (token,msgtext) values ("admin.eventeditor.pane-css","To set page wide styling set Token to 'css' and type the styling into the CSS Style field. You can also put URL into the External CSS URL field: this will create  a CSS include. If you DO NOT set token to 'css' then the CSS in the CSS Styling field will be applied INLINE to the contained Event. If you wish to add specific CSS Classes to the event add them to the CSS Code field.");

-- insert into msgcodes (token,msgtext) values ("admin.eventeditor.pane-setting","This pane deals with system settings indexed by Token. If you need to read this you shouldn't be...");

-- insert into msgcodes (token,msgtext) values ("admin.eventeditor.pane-js","Here you can apply jscript to your page. Set Token to 'js' and add either an external url into the External Javascript URL field or type javascript directly into the js code field.");
-- alter table msgcodes change token token varchar(128) after lang;
-- alter table msgcodes change lang lang char(2) after token;
-- delete from events where category="page" and  fadmin=1;
-- delete from events where category="page" and image like "%youtube%";

-- update rileyrg_stevefairway.events set eventurl="gigs" where eventurl="event";
-- update rileyrg_stevefairway.events set category="gigs" where category="event";

-- delete from events where eventurl="adminlogin";

-- alter table events add column googlemaps varchar(2048) after eventurl;
-- update msgcodes set field="description" where field="event";

-- update events set token="ev:description" where token="ev:event";

-- ALTER TABLE events CHANGE COLUMN googlemaps location varchar(2048);

-- delete from msgcodes;
-- alter table msgcodes add constraint deletecodes foreign key (fkey) references events (id) on delete cascade;
-- ALTER TABLE msgcodes ADD PRIMARY KEY (token,lang,field,fkey);

-- delete from msgcodes where (lang="" or lang is null) or ((token is null or token="")and (fkey is null or fkey=""));
-- drop table tmp;
-- create table tmp like msgcodes;
-- alter table tmp add unique(fkey,lang,field);
-- -- alter table tmp add unique(token,lang,field);
-- insert ignore  into tmp select * from msgcodes;
-- delete from tmp  where fkey not in (select id from events);
-- alter table tmp add constraint deletecodes foreign key (fkey) references events (id) on delete cascade;

-- rename table msgcodes to deleteme, tmp to msgcodes;

-- drop table deleteme;
-- drop table msgcodes_copy;

-- alter table msgcodes drop foreign key deletecodes;
-- alter table msgcodes drop key deletecodes;
-- alter table msgcodes drop index fkey;
-- alter table msgcodes drop index pkey;
-- alter table msgcodes drop index token;
-- update  msgcodes set token=fkey where fkey<>"" and fkey is not null;
-- alter table msgcodes drop column fkey;
-- alter table msgcodes add unique(token,lang,field);
-- alter table events drop fcommitted;
-- alter table events drop draft;
-- alter table events add column fdraft tinyint(1) default 0 after fadmin;

-- delete from events where duration=1969;
-- delete from msgcodes where token not in (select id from events);
-- ALTER TABLE events CHANGE COLUMN eventuid cssid varchar(128);

-- ALTER TABLE events add COLUMN cssoverride varchar(1024) default null after cssstyle ;
-- insert into events(category,fenabled,fadmin,groupby,setting,token,cssstyle) values ("contact",1,0,"contactform","lib/sendmailform.php","userinclude","width:80em");
-- update events set fenableshare=0 where token="userinclude";

-- update events set groupby="z-contactform" where groupby="contactform";
-- update events set cssstyle="" where token="userinclude";
-- update events set category="data-tvsport" where category="tvsport" and groupby="schedule";
-- update events set startdate=null where startdate="";
-- update events set duration=null where duration=0;
-- update events set cssoverride=null where cssoverride="";
-- alter table events add column embeddedlist varchar(256) after setting;
-- update events set embeddedlist=events.setting, token=null  where token="embeddedlist";

-- alter table events add column carouselwidth tinyint(1) default 0 after embeddedlist;
-- alter table events drop imageblob;
-- alter table events modify createdd datetime;
-- alter table events modify lastupdd datetime;
-- alter table events modify createdby varchar(128);
-- alter table events modify updatedby varchar(128);
-- alter table events add column includelink varchar(256) after js;
-- update events set includelink=setting,setting=null,token=null where token="userinclude";
-- alter table events drop fnodate;
-- ALTER TABLE events CHANGE COLUMN eventurl eventlink varchar(1024);
-- ALTER TABLE events CHANGE COLUMN googlemaps maplink varchar(1024);
-- alter table  events change column fadmin fadmin bit(1);
-- alter table  events change column fdraft fdraft bit(1);
-- alter table  events change column fjsonenabled fjsonenabled bit(1);
-- alter table  events change column fenableshare fenableshare bit(1);
-- alter table  events change column fenabled fenabled bit(1);
-- alter table  events change column fbcomments fbcomments bit(1);

-- putting msg link tables in
-- ALTER TABLE msgcodes ADD id INT PRIMARY KEY AUTO_INCREMENT;
-- create table  titles  ( eventid int(10) unsigned not null,msgid int(10) unsigned not null);
-- insert into titles (select events.id,msgcodes.id from events inner join msgcodes on msgcodes.token=events.id where msgcodes.field="title");
-- create table  descriptions  ( eventid int(10) unsigned not null,msgid int(10) unsigned not null);
-- insert into descriptions (select events.id,msgcodes.id from events inner join msgcodes on msgcodes.token=events.id where msgcodes.field="description");

-- alter table msgcodes change token eventid int(10);
-- alter table msgcodes drop index token;
-- alter table  msgcodes add unique eventid(eventid,lang,field);

-- alter table msgcodes drop index eventid;
-- alter table  msgcodes add unique fk_eventid(eventid,lang,field);
-- alter table msgcodes change eventid eventid int(10) unsigned;
-- set FOREIGN_KEY_CHECKS = 0;
-- alter table msgcodes add constraint deletecodes foreign key (eventid) references events (id) on delete cascade;
-- set FOREIGN_KEY_CHECKS = 1;

-- alter table events drop createdd;
-- alter table msgcodes drop createdd;
-- alter table events change lastupdd lastupdd timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
-- rename table msgcodes to eventmsgcodes;
-- create table sysmsgcodes ( id varchar(256) not null, lang char(2) default null,msgtext varchar(1024) default null,`lastupdd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,`createdby` varchar(8) default "admin" NOT NULL,`updatedby` varchar(8) default "admin" NOT NULL,unique unique_index(id,lang));
-- drop table sysmsgcodes;
-- create table sysmsgcodes ( id varchar(256) not null, lang char(2) default null,msgtext varchar(1024) default null,`lastupdd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,`createdby` varchar(8) default "admin" NOT NULL,`updatedby` varchar(8) default "admin" NOT NULL,unique unique_index(id,lang));


-- alter table eventmsgcodes change column eventid eventid int(10) unsigned not null;

-- alter table events add column specialinfo  varchar(256) after duration;-- alter table events add column pageid varchar(32);

-- update  events set image=null where category="page";
-- delete from events where token="list:disableshare";

-- update events set includelink="sendmailform.php" where includelink="lib/sendmailform.php";
-- update events set duration=120 where duration is null and startdate is not null;

-- alter table events change column duration duration int default 0;
-- update events set image=null where category="footerlink";
-- update events set fdraft=0 where isnull(fdraft);
-- drop table categories;
-- drop table msgcode_copy;
-- delete from events where category="msgcode";
-- delete from events where token!="" and token not like "%:%";
-- alter table events change column js javascript varchar(1024);
-- alter table events change column jslink javascriptlink varchar(256);
-- alter table events add column mdtype varchar(128) after ogtype;

-- update events set groupby="microdata" where token like "md:%";
-- update events set groupby="opengraph" where token like "og:%";
-- update events set groupby="facebook" where token like "fb:%";
-- update events set groupby="contactpath" where token like "contact:%";
-- update events set groupby="addresspath" where token like "contact:address";

-- update events set token="contact:email"  where token like "sys:email";
-- update events set token="contact:phone"  where token like "sys:phone";
-- update events set token="contact:address"  where token like "sys:address";


-- update events set token="web:title"  where token like "sys:title";


-- update events set token="contact:location",category="contact",groupby="contact"  where token like "sys:location";
-- update events set token="contact:opening",category="contact",groupby="contact"   where token like "sys:opening";
-- update events set category="contact" where token like "contact:%";

-- update events set csslink=NULL where csslink="";
-- update events set includelink=NULL where includelink="";
-- update events set javascriptlink=NULL where javascriptlink="";
-- update events set cssstyle=NULL where cssstyle="";
-- update events set javascript=NULL where javascript="";
-- update events set image=NULL where image="";
-- update events set setting=NULL where setting="";

-- update events set cssclass="food" where category="takeaway";

-- update events set groupby="contact" where token like "contact:%";
-- update events set groupby="web" where token like "web:%";

-- alter table events drop fdraft;

-- update events set category="pages" where category="page";
-- update events set category="photos" where category="photo";
-- update events set category="footerlinks" where category="footerlink";
-- update events set category="links" where category="link";
-- update events set eventlink="links" where eventlink="link";
-- update events set eventlink="photos" where eventlink="photo";


-- update events set includelink="contactform.php" where includelink="sendmailform.php";
-- update events set token="ev:addtocal" where token ="list:addtocal";
-- delete from eventmsgcodes where lang="fr"; 
-- delete from eventmsgcodes where msgtext="";
-- drop table  loginattempts ;
-- drop table  usertypes ;    
-- drop table  admin;
-- drop table sysmsgcodes;
-- ALTER TABLE users  ENGINE=InnoDB;
-- ALTER TABLE facebookuser  ENGINE=InnoDB;
-- ALTER TABLE facebookuser CONVERT TO CHARACTER SET 'utf8'; 
-- update events set includelink="contactform.php" where includelink="sendmailform.php";
-- update events set cssclass="nodelete" where includelink="contactform.php" ;
-- delete  FROM `events` WHERE `category` NOT IN ('index','pubquiz','contact','tvsport','about','events','settings','data-tvsport','data-pubquiz','data-links','links','pages','footerlinks','takeaway','food','photos','local');
-- update events set cssclass="specialsubject" where category="data-pubquiz";
-- alter table events add column cookietimeout smallint  default 0;
-- alter table events add column cookiename varchar(32) default null;
-- delete from events where category is null or category="";
-- alter table events add column attachcookieto varchar(32) default null;
-- alter table events add column popupclass varchar(32) default null;
-- alter table events add column popupposition varchar(32) default null;
-- alter table events change  column attachpopupto attachto varchar(32) default null;
-- update events set groupby="popup" where cookietimeout <> 0;
-- alter table events drop column popupdelay;
-- alter table events add column displaydelay smallint default 0;
-- alter table events add column displayfor smallint default 0;
-- alter table events change column displayfor displayfor  smallint default 20;

-- alter table events drop PopUpclass;
-- alter table events add column fclosebutton tinyint(1) default 0;
-- update events set category="data-sponsor" where cssclass like "%sponsor%";
-- alter table events change column cookietimeout cookietimeout smallint  default 30;
-- alter table events add column fpopup tinyint(1) default 0;
-- update events set cookietimeout=30 where cookietimeout<>0;
-- update events set fpopup=1 where popupposition is not null or cookietimeout <> 0;
-- alter table events drop column carouselwidth;
-- delete from events where token="fb:loginperms";
-- update events  set attachto="barwebs-fblogincontainer" where attachto="fblogincontainer";
alter table events add column fdeletewhenexpired  tinyint(1) default 1  after fenabled ;
