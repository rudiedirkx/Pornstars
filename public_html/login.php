<?php

require_once('inc.config.php');

if ( logincheck(false) ) {
	Go('./');
}

if ( '1' !== $GAMEPREFS['general_login'] && empty($_SESSION[$sessionname.'_ADMIN']) ) {
	$tickerstatus = '1' === $GAMEPREFS['ticker_on'] ? "ON" : "OFF";
	$signupmsg = '1' === $GAMEPREFS['general_signup'] ? "YOU CAN SIGN UP ALREADY, for the next round of course!" : "Sign up is also closed!";
	Save_Msg("<b>Login is closed for the moment.</b> That doesnt mean the ticker isnt working.<br>Ticker is <b>$tickerstatus</b>!<br><br><b>$signupmsg</b>","red");
	Go();
}

if ( isset($_POST['u'], $_POST['p']) )
{
	$arrUser = db_select('planets', "email = '".addslashes($_POST['u'])."' AND password = MD5(CONCAT(id,':".addslashes($_POST['p'])."'));");
	if ( 1 === count($arrUser) ) {
		$arrUser = $arrUser[0];
		if ( time()-$arrUser['lastlogin'] < $CHECK_TIME_BETWEEN_LOGINS ) {
			exit(json::encode(array(
				array('msg', 'There is less time than '.$CHECK_TIME_BETWEEN_LOGINS.' seconds since your last login on this account. You sure you\'re not cheating? Wait longer!!'),
			)));
		}
		else if ( !empty($arrUser['activationcode']) ) {
			exit(json::encode(array(
				array('msg', 'Your account has not been activated yet. Please do so!'),
				array('location', 'activate.php?email='.urlencode($arrUser['email'])),
			)));
		}
		else if ( '1' === $arrUser['closed'] ) {
			exit(json::encode(array(
				array('msg', 'Your account is closed, probably due to multiing!'),
			)));
		}
		else if ( $arrUser['sleep']>time() && empty($_POST['sleepmode_override']) ) {
			$s = $arrUser['sleep'] - time();
			$h = floor($s/3600);
			$s -= 3600*$h;
			$m = floor($s/60);
			$s -= 60*$m;
			exit(json::encode(array(
				array('eval', 'if(confirm(\'You\\\'re in sleepmode! It ends in '.$h.' hours, '.$m.' minutes and '.$s.' seconds. Do you want to deactivate it, so you can login right now!?\')){$(\'f_login\').elements[\'sleepmode_override\'].value=\'1\';$(\'f_login\')[\'onsubmit\']();}'),
			)));
		}
		else {
			$save = array(
				'planet_id'	=> $arrUser['id'],
				'unihash'	=> rand_string(16),
			);
			$_SESSION[$sessionname] = $save;
			if ( $arrUser['sleep']>time() ) {
				db_update('planets', 'nextsleep='.(time()+14*3600).' WHERE id = '.(int)$arrUser['id'].';');
				$arrUser['nextsleep'] = (time()+14*3600);
			}
			db_update( 'planets', 'sleep = 0, lastaction = '.time().', lastlogin = '.time().", unihash = '".$save['unihash']."'", 'id = '.(int)$arrUser['id'] );
			logbook('login', 'unihash='.$save['unihash'].'&sleep='.$arrUser['sleep'].'&nextsleep='.$arrUser['nextsleep'], (int)$save['planet_id']);
			exit(json::encode(array(
				array('eval', 'document.location.reload()'),
			)));
		}

	} // END 1 === count($arrUser)

	// No records found for this username & password
	exit(json::encode(array(
		array('msg', 'Invalid login combination!'),
	)));

} // END if ( isset($_POST['u'], $_POST['p']) )

else if ( isset($_POST['pwdvergeten']) )
{
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
	Go();
}

?>
<html>

<head>
<title><?php echo $GAMENAME; ?></title>
<script type="text/javascript" src="general_1_2_6.js"></script>
<script type="text/javascript" src="ajax_1_3_1.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript">
<!--//
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
//-->
</script>
</head>

<body onload="$('login_u').focus();">
<?php echo $indextitel; ?>
<center>
<br />
<?php echo (isset($_SESSION['ps_msg']))?"<font color=\"".$_SESSION['ps_msg']['color']."\">".$_SESSION['ps_msg']['msg']."</font>":""?><br>
<?php echo (!$GAMEPREFS['general_login'])?"<font color=red><b>LOGIN HAS BEEN DISABLED</b></font><br>":""?>
<br />
<b>LOGIN</b><br />
<br />
<br />
<form id="f_login" method="post" action="login.php" onsubmit="return postForm(this,L);" />
<input type="hidden" name="sleepmode_override" value="" />
E-mail address<br>
<input type="text" id="login_u" name="u" style="width:200;" /><br>
<br />
Password<br>
<input type="password" id="login_p" name="p" style="width:200;" onfocus="this.select();" /><br>
<br />
<br />
<input type="submit" value="login" style="width:200px;" /><br />
</form>
<br />
<br />
<br />
<br />
<form method=post><input type=hidden name=pwdvergeten value=1>
<b>Forgot your password? Dont worry!<br />
<br />
Email: <input type=text name=email style='width:200;'><br />
<br />
<input type=submit value="Create new password!" style='width:200;'>
</form>

</body>

</html>
<?php

unset($_SESSION['ps_msg']);

?>
