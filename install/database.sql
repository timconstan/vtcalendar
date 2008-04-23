CREATE TABLE vtcal_adminuser (
  id varchar(50) NOT NULL default '',
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_auth (
  calendarid text NOT NULL,
  userid text NOT NULL,
  sponsorid int NOT NULL default '0'
);

CREATE TABLE vtcal_calendar (
  id varchar(100) NOT NULL default '',
  name text,
  title text,
  header text,
  footer text,
  bgcolor text,
  maincolor text,
  todaycolor text,
  pastcolor text,
  futurecolor text,
  textcolor text,
  linkcolor text,
  gridcolor text,
  viewauthrequired int(11) default '0',
  forwardeventdefault int(11) NOT NULL default '0',  
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_calendarviewauth (
  calendarid text NOT NULL,
  userid text NOT NULL
);

CREATE TABLE vtcal_category (
  calendarid text NOT NULL,
  id int NOT NULL auto_increment,
  name text NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_event (
  calendarid text NOT NULL,
  id varchar(18) NOT NULL default '',
  timebegin text,
  timeend text,
  sponsorid int NOT NULL default '0',
  title text NOT NULL,
  wholedayevent int default '0',
  categoryid int default '0',
  description text,
  location text,
  price text,
  contact_name text,
  contact_phone text,
  contact_email text,
  url text,
  recordchangedtime text,
  recordchangeduser text,
  approved int default '0',
  rejectreason text,
  displayedsponsor text,
  displayedsponsorurl text,
  repeatid varchar(13) default NULL,
  showondefaultcal int default '0',
  showincategory int default '0'
);

CREATE TABLE vtcal_event_public (
  calendarid text NOT NULL,
  id varchar(18) NOT NULL default '',
  timebegin text,
  timeend text,
  sponsorid int NOT NULL default '0',
  title text NOT NULL,
  wholedayevent int default '0',
  categoryid int default '0',
  description text,
  location text,
  price text,
  contact_name text,
  contact_phone text,
  contact_email text,
  url text,
  recordchangedtime text,
  recordchangeduser text,
  displayedsponsor text,
  displayedsponsorurl text,
  repeatid varchar(13) default NULL
);

CREATE TABLE vtcal_event_repeat (
  calendarid text NOT NULL,
  id varchar(13) NOT NULL default '',
  repeatdef text,
  startdate text,
  enddate text,
  recordchangedtime text,
  recordchangeduser text,
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_searchfeatured (
  id int NOT NULL auto_increment,
  calendarid text NOT NULL,
  keyword text,
  featuretext text,
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_searchkeyword (
  id int NOT NULL auto_increment,
  calendarid text NOT NULL,
  keyword text,
  alternative text,
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_searchlog (
  id int NOT NULL auto_increment,
  calendarid text NOT NULL,
  time varchar(19) default NULL,
  ip varchar(15) default NULL,
  numresults int default '0',
  keyword text,
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_sponsor (
  calendarid text NOT NULL,
  id int NOT NULL auto_increment,
  name text NOT NULL,
  url text,
  email text,
  admin int default '0',
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_template (
  calendarid text NOT NULL,
  id int NOT NULL auto_increment,
  name text NOT NULL,
  sponsorid int NOT NULL default '0',
  title text NOT NULL,
  wholedayevent int default '0',
  categoryid int default '0',
  description text,
  location text,
  price text,
  contact_name text,
  contact_phone text,
  contact_email text,
  url text,
  recordchangedtime text,
  recordchangeduser text,
  displayedsponsor text,
  displayedsponsorurl text,
  PRIMARY KEY (id)
);

CREATE TABLE vtcal_user (
  id text NOT NULL,
  password text NOT NULL,
  email text NOT NULL
);

INSERT INTO vtcal_calendar (id, name, title, header, footer, bgcolor, maincolor, todaycolor, pastcolor, futurecolor, textcolor, linkcolor, gridcolor, viewauthrequired, forwardeventdefault) VALUES ('default', 'MyOrg Main Event Calendar', 'Calendar', '', '', '#ffffff', '#ff9900', '#ffcc66', '#eeeeee', '#ffffff', '#000000', '#3333cc', '#cccccc', 0, 0);

INSERT INTO vtcal_category (calendarid, id, name) VALUES ('default',1,'Category 1');

INSERT INTO vtcal_category (calendarid, id, name) VALUES ('default',2,'Category 2');

INSERT INTO vtcal_category (calendarid, id, name) VALUES ('default',3,'Category 3');

INSERT INTO vtcal_sponsor (calendarid,id,name,url,email,admin) VALUES ('default',1,'Administration', 'http://calendar.myorg.edu/', 'calendar@myorg.edu',1);

INSERT INTO vtcal_auth (calendarid,userid,sponsorid) VALUES ('default','adminuserid',1);

INSERT INTO vtcal_adminuser (id) VALUES ('adminuserid');

INSERT INTO vtcal_user (id, password, email) VALUES ('adminuserid', 'adminpassword', '');
