<?php

require 'inc.bootstrap.php';

if ( logincheck(false) ) {
	return do_redirect('overview');
}

include 'tpl.anon-menu.php';

if ($tickdif > $TICKERTIME) {
	$tickertimetxt = "<font color=red>".(($tickdif>=24*3600)?(date("d",$tickdif)-1)." dagen, ":"").(date("H",$tickdif)-1)."h ".date("i\m s\s",$tickdif)."</font>";
}
else {
	$tickertimetxt = "$tickdif seconds";
}

$tickertimetxt = (($tickdif > $TICKERTIME)?"<font color=red>":"") . Verschil_In_Tijd($tickdif,' days',' hours',' minutes',' seconds') . (($tickdif > $TICKERTIME)?"</font>":"");

?>
<html>
<head>
<? include 'tpl.head.php' ?>
<title><?php echo $GAMENAME; ?></title>
<script>if (top.location!=document.location) { top.location=document.location; }</script>
</head>
<body bgcolor="black">
<center>
<br />

<?php include 'inc.message.php'; ?>

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

echo $db->count('planets') . ' registered accounts<br />';
echo $db->count('planets', 'lastaction > ?', [time()-180]) . " online users.<br />";
echo "Time: " . date('H:i:s') . "<br />";

unset($_SESSION['ps_msg']);
