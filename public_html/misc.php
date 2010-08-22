<?php

require_once('inc.config.php');

if ( isset($_GET['pwdvergeten'], $_GET['email'], $_GET['password']) )
{
	$a = PSQ("SELECT * FROM $TABLE[users] WHERE email='".$_GET['email']."' AND password='".$_GET['password']."';");
	if (mysql_num_rows($a)==0)
		Go("?changepage=index");

	$i = mysql_fetch_assoc($a);
	$UID = $i['id'];

	Logbook("login","");
	$save['email'] = trim($_GET['email']);
	$save['password'] = trim($_GET['password']);
	$save['uid'] = $UID;
	$save['ingelogd'] = TRUE;
	$save['uniek'] = md5(time());
	$_SESSION[$sessionname] = $save;

	if ($i['sleep']>time())
		PSQ("UPDATE $TABLE[users] SET nextsleep=".($time+14*3600)." WHERE id='$UID'");
	PSQ("UPDATE $TABLE[users] SET sleep='0' WHERE id='$UID';");

	Go("?changepage=menu");
}
else if ( isset($_POST['action'], $_POST['activationcode'], $_POST['email']) && $_POST['action'] == 'activate' )
{
	$s = PSQ("UPDATE $TABLE[users] SET activationcode='',lastaction='".time()."' WHERE email='".trim($_POST['email'])."' AND activationcode='".trim($_POST['activationcode'])."';");
	if (mysql_affected_rows())
	{
		Save_Msg("Your account has been activated. You can now proceed to login","green");
		Go("./?changepage=login");
	}
	else
	{
		Save_Msg("Your account has either already been activated, or the data your sent was not correct!","red");
		Go();
	}
}
else if (isset($_POST['action']) && $_POST['action']=="new_email" && isset($_POST['new_email_code']) && isset($_POST['old_email']) && isset($_POST['new_email']) && isset($_POST['pwd']))
{
	$s = PSQ("UPDATE $TABLE[users] SET email=new_email,new_email='',new_email_code='',lastaction='".time()."' WHERE email='".trim($_POST['old_email'])."' AND new_email='".trim($_POST['new_email'])."' AND new_email_code='".trim($_POST['new_email_code'])."' AND password='".md5($_POST['pwd'])."';");
	if (mysql_affected_rows())
	{
		Save_Msg("Your E-mail address has been updated! You can proceed to login or continue your old session.","green");
		Go("./?changepage=login");
	}
	else
	{
		Save_Msg("There was no request found for a new e-mail address (WRONG_DATA?). Contact the Site Admin or try again in the Preferences!","red");
		Go();
	}
}

?>
<html>

<head>
<title><?=$GAMENAME?></title>
<link rel=stylesheet href="css/styles.css">
</head>

<body bgcolor=black><font color=aaaaaa>
<?php

if ( isset($_GET['action']) && $_GET['action'] == 'activate' )
{
	?>
<table border=0 cellpadding=5 cellspacing=0 width=100%><tr><td><center><a href="./?changepage=index">Index</a> &nbsp; || &nbsp; <a href="./?changepage=login">Login</a> &nbsp; || &nbsp; <a href="./?changepage=signup">Signup</a> &nbsp;||&nbsp; <a href="./?changepage=menu"><b>Play</b></a> &nbsp;||&nbsp; <a href="./comp.php" target=_parent>Administrate</a></td></tr></table>
<center><br>
<font color="<?=$_SESSION['ps_msg']['color']?>"><?=$_SESSION['ps_msg']['msg']?></font><br>
<br>
<b style='font-size:30px;'>ACTIVATION</b><br>
<br>
<form name=activate method=post><input type=hidden name=action value=activate>
Email:<br>
<input type=text name=email<?=(isset($_GET['email']))?" value=\"".trim($_GET['email'])."\"":""?> style='width:200px;'><br>
<br>
Activationcode:<br>
<input type=text name=activationcode<?=(isset($_GET['activationcode']))?" value=\"".trim($_GET['activationcode'])."\"":""?> style='width:200px;'><br>
<br>
<br>
<input type=submit value="Activate"></form>
	<?
}
else if ( isset($_GET['action']) && $_GET['action'] == 'new_email' )
{
	?>
<table border=0 cellpadding=5 cellspacing=0 width=100%><tr><td><center><a href="./?changepage=index">Index</a> &nbsp; || &nbsp; <a href="./?changepage=login">Login</a> &nbsp; || &nbsp; <a href="./?changepage=signup">Signup</a> &nbsp;||&nbsp; <a href="./?changepage=menu"><b>Play</b></a> &nbsp;||&nbsp; <a href="./comp.php" target=_parent>Administrate</a></td></tr></table>
<center><br>
<font color="<?=$_SESSION['ps_msg']['color']?>"><?=$_SESSION['ps_msg']['msg']?></font><br>
<br>
<b style='font-size:30px;'>NEW E-MAIL ADDRESS</b><br>
<br>
<form name=new_email method=post><input type=hidden name=action value=new_email>
Old E-mail<br>
<input type=text name=old_email value="" style='width:200px;'><br>
<br>
New E-mail<br>
<input type=text name=new_email value="<?=(isset($_GET['new_email']))?$_GET['new_email']:""?>" style='width:200px;'><br>
<br>
Password<br>
<input type=password name=pwd value="" style='width:200px;'><br>
<br>
Activation Code<br>
<input type=text name=new_email_code value="<?=(isset($_GET['new_email_code']))?$_GET['new_email_code']:""?>" style='width:200px;'><br><br>
<br>
<br>
<input type=submit value="Activate"></form>
<?
}
else
{
	Go("index.php");
}

?>