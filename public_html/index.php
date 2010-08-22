<?php

require_once('inc.config.php');


if ( isset($_GET['ps_page']) && $_GET['ps_page'] == 'playtitelbalk' ) {
	die("<html><head><title>".$GAMENAME."</title><link rel=stylesheet href=\"css/styles.css\" /></head><body bgcolor=\"black\"><base target=\"_parent\" /><table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" width=\"828\" height=\"100%\" style=\"border-bottom:solid 0px #444444;\"><tr><td align=\"center\"><a href=\"./\">Index</a> &nbsp; || &nbsp; <a href=\"login.php\">Login</a> &nbsp; || &nbsp; <a href=\"signup.php\">Signup</a> &nbsp;||&nbsp; <a href=\"./\"><b>Play</b></a> &nbsp;||&nbsp; Ticker: <a href=\"tickah.php?SET_USER_IS_TICKER=1\" target=\"t0\">ON</a> / <a href=\"tickah.php?SET_USER_IS_TICKER=0\" target=\"t0\">OFF</a> &nbsp;||&nbsp; <a href=\"comp.php\" target=\"_parent\">Administration</a></td></tr></table></body></html>");
}


if ( logincheck(false) ) {
	?>
<html>
<head>
<title><?php echo $GAMENAME; ?></title>
</head>
<frameset rows="50,*" frameborder="0" border="0">
	<frameset cols="220,*" frameborder="0" border="0">
		<frame name="t0" SRC="<?php echo !empty($_SESSION['ps_is_ticker']) ? 'tickah.php?special=yes' : 'leeg.php'; ?>" noresize="noresize" marginwidth="0" marginheight="0" scrolling="no" frameborder="0">
		<frame name="a0" SRC="index.php?ps_page=playtitelbalk" noresize="noresize" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
	</frameset>
	<frameset cols="220,*" frameborder="0" border="0">
		<frame name="a1" SRC="menu.php" noresize="noresize" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
		<frame name="a9" SRC="overview.php" noresize="noresize" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
	</frameset>
</frameset>
</html>
	<?php

	exit;
}


echo $indextitel;

if ($tickdif > $TICKERTIME)
	$tickertimetxt = "<font color=red>".(($tickdif>=24*3600)?(date("d",$tickdif)-1)." dagen, ":"").(date("H",$tickdif)-1)."h ".date("i\m s\s",$tickdif)."</font>";
else
	$tickertimetxt = "$tickdif seconds";

$tickertimetxt = (($tickdif > $TICKERTIME)?"<font color=red>":"") . Verschil_In_Tijd($tickdif,' days',' hours',' minutes',' seconds') . (($tickdif > $TICKERTIME)?"</font>":"");

?>
<html>
<head>
<title><?php echo $GAMENAME; ?></title>
<link rel=stylesheet href="css/styles.css">
<script>if (top.location!=document.location) { top.location=document.location; }</script>
</head>
<body bgcolor="black">
<center>
<br />
<?php echo (isset($_SESSION['ps_msg']))?"<font color=\"".$_SESSION['ps_msg']['color']."\">".$_SESSION['ps_msg']['msg']."</font>":""?><br>
<br />
<br />
<a href="manual.php">Manual</a><br />
<br />
<a href="login.php">Login</a><br />
<br />
<a href="signup.php">Make An Account</a><br />
<br />
<br />
<br />
Game status:<br />
<?php echo $tickertimetxt; ?> since last tick.</font>
<br />
MyT = <?php echo $MyT; ?><br />
<br />
<?php

echo db_count('planets').' registered accounts<br />';

echo db_count('planets', 'lastaction > '.(time()-180))." online users.<br />Time: ".date('H:i:s')."<br />";

unset($_SESSION['ps_msg']);

?>