<?php
  session_start();
  header("Content-Type: text/css");	
  if (strpos(" ".$_SERVER["HTTP_USER_AGENT"],"MSIE") > 0) { $ie = 1; }
  else { $ie = 0; }  
?>
BODY, TD, P {
	FONT-SIZE: <?php if ($ie) { echo "x-"; } ?>small; 
	FONT-FAMILY: Arial,Helvetica,Sans-Serif;
	MARGIN: 0 0 0 0;
}
A {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>; 
	TEXT-DECORATION: none;
}
A:visited {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>; 
	TEXT-DECORATION: none;
}
A:hover {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>; 
	TEXT-DECORATION: none;
}
.calendartitle {
	FONT-WEIGHT: bold; 
	FONT-SIZE: 20px;
}
.datetitle {
	FONT-SIZE: 20px; 
}
.eventtitlebig {
	FONT-WEIGHT: bold; 
	FONT-SIZE: 24px; 
}
.eventtimebig {
	FONT-SIZE: 18px; 
}
.littlecalendardatetitle {
	FONT-WEIGHT: bold; 
	FONT-SIZE: <?php if ($ie) { echo "x-"; } ?>small;
}
.littlecalendarheader {
	FONT-SIZE: <?php if ($ie) { echo "x"; } ?>x-small; 
	BACKGROUND-COLOR: <?php echo $_SESSION["BGCOLOR"]; ?>;
}
.littlecalendarday {
	FONT-SIZE: <?php if ($ie) { echo "x"; } ?>x-small; 
	BACKGROUND-COLOR: <?php echo $_SESSION["BGCOLOR"]; ?>;
}
.littlecalendarweek {
	FONT-SIZE: xx-small; 
	BACKGROUND-COLOR: <?php echo $_SESSION["BGCOLOR"]; ?>;
}
.littlecalendarother {
	FONT-SIZE: <?php if ($ie) { echo "x"; } ?>x-small; 
	COLOR: #cccccc;
}
.todayis {
	FONT-SIZE: <?php if ($ie) { echo "x-"; } ?>small;  
}
.todayis A {
	FONT-SIZE: <?php if ($ie) { echo "x-"; } ?>small; 
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
.todayis A:visited {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
.todayis A:hover {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
.weekheader {
	BACKGROUND-COLOR: <?php echo $_SESSION["GRIDCOLOR"]; ?>; 
	COLOR: <?php echo $_SESSION["TEXTCOLOR"]; ?>;
}
.monthheader {
	BACKGROUND-COLOR: #aaaaaa; 
	COLOR: #000000;
}
.past {
	BACKGROUND-COLOR: <?php echo $_SESSION["PASTCOLOR"]; ?>;
}
A.past {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
A.past:visited {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
A.past:hover {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
.today {
	BACKGROUND-COLOR: <?php echo $_SESSION["TODAYCOLOR"]; ?>;
}
.future {
	BACKGROUND-COLOR: <?php echo $_SESSION["FUTURECOLOR"]; ?>;
}
.eventtime {
	FONT-SIZE: <?php if ($ie) { echo "x"; } ?>x-small;
}
.eventcategory {
	FONT-SIZE: <?php if ($ie) { echo "x"; } ?>x-small;
}
.tabactive {
	COLOR: <?php echo $_SESSION["TEXTCOLOR"]; ?>; 
	BACKGROUND-COLOR: <?php echo $_SESSION["MAINCOLOR"]; ?>;
}
.tabinactive A {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
.tabinactive A:visited {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
.tabinactive A:hover {
	COLOR: <?php echo $_SESSION["LINKCOLOR"]; ?>;
}
.tabinactive {
	BACKGROUND-COLOR: <?php echo $_SESSION["GRIDCOLOR"]; ?>;
}
.announcement {
	FONT-SIZE: medium;
}
.feedbackpos {
	FONT-WEIGHT: bold; 
	FONT-SIZE: <?php if ($ie) { echo "x-"; } ?>small; 
	COLOR: #00CC14;
}
.feedbackneg {
	FONT-WEIGHT: bold; 
	FONT-SIZE: <?php if ($ie) { echo "x-"; } ?>small; 
	COLOR: #FF1A00;
}
.example {
  color: #999999;
}
code, pre {
   font-size: 10pt;
}