<?php

require_once('inc.config.php');

if ( isset($_POST['activation_code'], $_POST['activation_email']) )
{
	db_update( 'planets', 'activationcode = \'\', lastaction = unix_timestamp(now())', 'email = \''.addslashes($_POST['activation_email']).'\' AND activationcode = \''.addslashes($_POST['activation_code']).'\'' );
	if ( 0 < db_affected_rows() ) {
		Save_Msg('Account activated! You can now login!', 'lime');
		Go('./index.php?changepage=login');
	}
	Save_Msg('Failed! Account is already activated or data is wrong!', 'red');
	Go();
}

else if ( isset($_POST['resend_code']) ) {
	$szUserEmail = db_select_one( 'planets', 'email', "email = '".addslashes($_POST['resend_code'])."'" );
	if ( $szUserEmail ) {
		$szActivationCode = md5(microtime());
		db_update('planets', "activationcode = '" . $szActivationCode . "'", "email = '" . addslashes($szUserEmail) . "'");
		$szGameHost = str_replace('www.', '', $_SERVER['HTTP_HOST']);
		$headers  = "From: PORNSTARS <pornstars@".$szGameHost.">\r\n";
		$headers .= "Return-Path: <pornstars@".$szGameHost.">\r\n";
		$headers .= "X-Sender: <pornstars@".$szGameHost.">\r\n";
		if ( mail($szUserEmail, 'PORNSTARS - ACTIVATION CODE', 'Your new activationcode = '.$szActivationCode, $headers) ) {
			Save_Msg( 'E-mail sent!', 'lime' );
		}
		else {
			Save_Msg( 'E-mail not sent :( Try again soon!', 'red' );
		}
	}
	else {
		Save_Msg( 'No e-mail found!', 'red' );
	}
	Go();
}

?>
<html>

<head>
<title><?php echo $GAMENAME; ?></title>
<link rel=stylesheet href="css/styles.css" />
</head>

<body bgcolor="black">
<center>

<?php include 'inc.message.php'; ?>

<h1>ACTIVATION</h1><br />
<br />
<form method="post">
E-mail:<br>
<input type="text" name="activation_email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" size="40" /><br />
<br />
Activationcode:<br />
<input type="text" name="activation_code" value="<?php echo isset($_GET['activationcode']) ? htmlspecialchars($_GET['activationcode']) : ''; ?>" size="40" /><br />
<br />
<br />
<input type="submit" value="Activate" />
</form>

<br />
<br />
<br />

<h2>Resend code</h2>
<form method="post" action="">
E-mail:<br />
<input type="text" name="resend_code" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" size="40" /><br />
<br />
<input type="submit" value="Resend" />
</form>

</body>

</html>
<?php unset($_SESSION['ps_msg']); ?>
