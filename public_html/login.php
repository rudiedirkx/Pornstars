<?php

use rdx\ps\Planet;

require 'inc.bootstrap.php';

if ( logincheck(false) ) {
	return do_redirect('index');
}

// LOGIN CLOSED
if ( !$g_prefs->general_login && empty($_SESSION['ADMIN']) ) {
	$tickerstatus = $g_prefs->ticker_on ? "ON" : "OFF";
	$signupmsg = $g_prefs->general_signup ? "YOU CAN SIGN UP ALREADY, for the next round of course!" : "Sign up is also closed!";
	Save_Msg("<b>Login is closed for the moment.</b> That doesnt mean the ticker isnt working.<br>Ticker is <b>$tickerstatus</b>!<br><br><b>$signupmsg</b>","red");

	return do_redirect('index');
}

// LOG IN
if ( isset($_POST['u'], $_POST['p']) ) {
	$objPlanet = Planet::first(['email' => $_POST['u']]);

	// Invalid login
	if ( !$objPlanet || !$objPlanet->checkPassword($_POST['p']) ) {
		return do_json([
			['msg', 'Invalid login combination!'],
		]);
	}

	// Need activation
	if ( $objPlanet->activationcode ) {
		return do_json([
			['msg', 'Your account has not been activated yet. Please do so!'],
			['location', 'activate.php?email=' . urlencode($objPlanet->email)],
		]);
	}

	// Need activation
	if ( $objPlanet->closed ) {
		return do_json([
			['msg', 'Your account is closed, probably due to multiing!'],
		]);
	}

	// Sleep mode
	if ( $objPlanet->sleep > time() && empty($_POST['sleepmode_override']) ) {
		$s = $objPlanet->sleep - time();
		$h = floor($s/3600);
		$s -= 3600*$h;
		$m = floor($s/60);
		$s -= 60*$m;
		return do_json([
			array('eval', 'if(confirm(\'You\\\'re in sleepmode! It ends in '.$h.' hours, '.$m.' minutes and '.$s.' seconds. Do you want to deactivate it, so you can login right now!?\')){$(\'f_login\').elements[\'sleepmode_override\'].value=\'1\';$(\'f_login\')[\'onsubmit\']();}'),
		]);
	}

	// Success!

	$save = array(
		'planet_id'	=> $objPlanet->id,
		'unihash'	=> rand_string(16),
	);
	$_SESSION = $save + $_SESSION;

	if ( $objPlanet->sleep > time() ) {
		$objPlanet->update(['nextsleep' => time() + 14*3600]);
	}

	$objPlanet->update([
		'sleep' => 0,
		'lastaction' => time(),
		'lastlogin' => time(),
		'unihash' => $save['unihash'],
	]);

	// logbook('login', 'unihash='.$save['unihash'].'&sleep='.$objPlanet->sleep.'&nextsleep='.$objPlanet->nextsleep, (int)$save['planet_id']);

	return do_json([
		array('eval', 'location.reload()'),
	]);
}

// PASSWORD RESET
else if ( isset($_POST['pwdvergeten']) ) {
	$q = db_query("SELECT id,email,password FROM planets WHERE email = '$_POST[email]';");
	if ( 0 < mysql_num_rows($q) )
	{
		$i = mysql_fetch_assoc($q);
		$newpwd = substr(md5(time()),0,12);
		db_query("UPDATE planets SET password ='".md5((int)$i['id'].':'.$newpwd)."' WHERE id = ".(int)$i['id'].";");
		logbook('pwd_reminder', 'email='.$i['email'].'&new='.$newpwd, (int)$i['id']);
		$gameaddress = str_replace("index.php","misc.php",$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
		$gamename0 = str_replace("www.","",$_SERVER['HTTP_HOST']);
		$gamename = "PS ".$gamename0." LOGIN";
	$headers  = "From: PornStarS PASSWORD <pornstars@".$gamename0.">\r\n";
	$headers .= "Return-Path: <pornstars@".$gamename0.">\r\n";
	$headers .= "X-Sender: <pornstars@".$gamename0.">\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$mailed = mail($i['email'],"PORNSTARS - Forgotten Password","Your new password: ".$newpwd,$headers);
		$msg = ($mailed) ? "Your password has been sent to \"<b>".htmlspecialchars($i['email'])."\"" : "We have failed in sending you your password. Wrong e-mail server";
		$color = ($mailed) ? "green" : "red";
		Save_Msg($msg,$color);
		Go();
	}

	Save_Msg("Cant find the emailaddress \"<b>".htmlspecialchars($_POST['email'])."</b>\"","red");

	return do_redirect('index');
}

?>
<html>

<head>
<? include 'tpl.head.php' ?>
<title><?= html($GAMENAME) ?></title>
<script src="general_1_2_6.js"></script>
<script src="ajax_1_3_1.js"></script>
<script>
function L(a) {
	var t = a.responseText;
	try {
		eval('var r = ' + t);
		for ( var i=0; i<r.length; i++ ) {
			var v = r[i][1];
			switch ( r[i][0] )
			{
				case 'msg':
					alert(v);
				break;

				case 'eval':
					eval(v);
				break;

				case 'location':
					document.location = v;
				break;
			}
		}
	} catch(e) { alert(t); }
}
</script>
</head>

<body>

<?php echo $indextitel; ?>

<center>

<br />

<?php include 'inc.message.php'; ?>

<?php echo (!$g_prefs->general_login)?"<font color=red><b>LOGIN HAS BEEN DISABLED</b></font><br>":""?>

<br />

<b>LOGIN</b><br />

<br />
<br />

<form id="f_login" method="post" action="login.php" onsubmit="return postForm(this,L);" />
	<input type="hidden" name="sleepmode_override" value="" />

	E-mail address<br>
	<input type="email" autofocus name="u" /><br>
	<br />

	Password<br>
	<input type="password" name="p" onfocus="this.select();" /><br>
	<br />
	<br />

	<button>LOG IN</button>
</form>

<br />
<br />
<br />
<br />

<form method="post">
	<input type="hidden" name="pwdvergeten" value="1" />

	<b>Forgot your password? Dont worry!<br />
	<br />

	Email: <input type="text" name="email" /><br />
	<br />

	<button>Create new password!</button>
</form>

</body>

</html>
<?php

unset($_SESSION['ps_msg']);
