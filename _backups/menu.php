<?

include("config.php");

logincheck();

include("securitycheck.php");

?>
<html>

<head>
<title><?=$PA[name]?> <?=ereg_replace("www.","",$_SERVER[HTTP_HOST])?></title>
</head>

<frameset cols="180,*" border=0 framespacing=0 frameborder=NO>
	<frame name="a1" SRC="meny.php"  noresize marginwidth=0 marginheight=0 scrolling=auto scrollbars=no>
	<frame name="ED_T_main" SRC="overview.php"   noresize marginwidth=0 marginheight=0 scrolling=yes scrollbars=no>
</frameset>