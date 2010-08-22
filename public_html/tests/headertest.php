<?

include("../config.php");


$news = (mysql_num_rows(PSQ("SELECT id FROM $TABLE[news] WHERE uid='$UID' AND seen='0' LIMIT 1"))) ? "<img src=\"images/mNews.png\" border=0>" : "NEWS";
$mail = (mysql_num_rows(PSQ("SELECT id FROM $TABLE[mail] WHERE uid='$UID' AND seen='0' LIMIT 1"))) ? "<img src=\"images/mMail.png\" border=0>" : "MAIL";
$curleader = mysql_fetch_assoc(PSQ("SELECT x,y,planetname FROM $TABLE[users] ORDER BY score DESC LIMIT 1"));

$a = explode(".",basename($_SERVER['SCRIPT_NAME']));
$stF = $a[0];

?>
<html>

<head>
<title><?=$GAMENAME?> <?ereg_replace("www.","",$_SERVER['HTTP_HOST'])?></title>
<link rel=stylesheet href="../css/styles.css">
</head>

<body leftmargin=4 topmargin=4 rightmargin=0 bottommargin=0 bgcolor=black>

<table border=0 cellpadding=0 cellspacing=0 width=700 height=180>
<tr><td><table border=0 cellpadding=5 cellspacing=0 height=60><tr valign=middle><td width=100><table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr valign=middle><td><center><?=$news?></td></tr><tr><td><center><?=$mail?></td></tr></table></td><td width=400><table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr valign=middle><td><center>P A G E N A M E</td></tr></table></td><td width=200><table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr valign=middle><td align=right>62+ min since last tick (5 s.)<br>Ticker stopped!</td></tr></table></td></tr></table></td></tr>
<tr><td><table border=0 cellpadding=5 cellspacing=0 height=60><tr valign=middle><td width=550 colspan=2><?="<table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr><td><center>Metal</td><td><center>Crystal</td><td><center>Energy</td><td><center>Score</td><td><center>Rank</td></tr><tr><td><center>".nummertje($USER['metal'])."</td><td><center>".nummertje($USER['crystal'])."</td><td><center>".nummertje($USER['energy'])."</td><td><center>".nummertje($USER['score'])."</td><td><center># ".$USER['rank']."</td></tr></table>"?></td><td width=150><table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr valign=middle><td align=right>GameTime<br>--time--<br><br>Myt - xxx</td></tr></table></td></tr></table></td></tr>
<tr><td><table border=0 cellpadding=5 cellspacing=0 height=60><tr valign=middle><td width=140><table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr valign=middle><td><center><font color=red>GALAXY HOSTILES</td></tr><tr><td><center><font color=green>GALAXY FRIENDLIES</td></tr></table></td><td width=300><table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr valign=middle><td><center>P A G E N A M E</td></tr></table></td><td width=260><table border=1 cellpadding=2 cellspacing=0 width=100% height=100%><tr valign=middle><td align=right>Current Leader:<br></td></tr></table></td></tr></table></td></tr>
</table>
<?

include("../footer.php");

?>