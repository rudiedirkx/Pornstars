<?php

require_once('inc.config.php');
logincheck();

if ( isset($_POST['oldpassword'], $_POST['newpassword'], $_POST['newpassword2']) )
{
	if ( md5($_POST['newpassword']) !== md5($_POST['newpassword2']) )
	{
		Save_Msg("Your two passwords are not identical!","red");
		Go();
	}
	$oldpwd = md5($_POST['oldpassword']);
	if ( !db_count('planets', 'password = MD5(CONCAT(id,\':'.addslashes($_POST['oldpassword']).'\')) AND id = '.PLANET_ID) )
	{
		Save_Msg("Your old password was wrong!","red");
		Go();
	}
	if ( db_update('planets', 'password = MD5(CONCAT(id,\':'.addslashes($_POST['newpassword']).'\'))', '') && db_affected_rows() ) {
		db_update('planets', 'oldpwd = \'0\'', 'id = '.PLANET_ID);
	}
	Save_Msg("Password changed!", "lime");
	Go();
}

/*else if ( isset($_POST['action']) && $_POST['action']=="changeemail" && $_POST['new_email']==$_POST['new_email2'] && preg_match("/(?i)^([a-z0-9._-])+@([a-z0-9.-])+\.([a-z0-9]){2,4}$/",$_POST['new_email']) )
{
	if (mysql_result(db_query("SELECT COUNT(*) AS a FROM $TABLE[users] WHERE email='".$_POST['new_email']."';"),0,'a') == 0)
	{
		$new_email_code = substr(md5(time()),0,18);
		$c = db_query("UPDATE $TABLE[users] SET new_email='".$_POST['new_email']."',new_email_code='$new_email_code' WHERE email='".$_POST['old_email']."' AND password='".md5($_POST['password'])."' AND id='$UID';") or die(mysql_error());
	}

	if ($c)
	{
		$m = @mail($_POST['new_email'],$GAMENAME." - New Emailaddress",$GAMENAME."\n\nYou want to change your emailaddress into ".$_POST['new_email']."\n\nYou have to activate it first though, by clicking on this URL:\nhttp://".$_SERVER['HTTP_HOST'].str_replace(basename($_SERVER['SCRIPT_NAME']),"misc.php",$_SERVER['SCRIPT_NAME'])."?action=new_email&new_email=".$_POST['new_email']."&new_email_code=$new_email_code\n\nYour old e-mail address: ".$USER['email']."\nYour new: ".$_POST['new_email']."\nYour new_email_code: $new_email_code\n\n\n","From: $GAMENAME <dont@return.com>\nReturn-Path: <dont@return.com>");

		Save_Msg("Your email has been changed to \"<b>".$_POST['new_email']."</b>\"".(($m)?"Check your emailaddress to activate it!":"There has not been sent an email, plz contact the Administrator..."),"green");
		Go();
	}
	else
	{
		Save_Msg("Your email has NOT been changed! You can try again though...","red");
		Go();
	}
}*/

else if ( isset($_POST['sleep']) )
{
	if ( time() > $g_arrUser['nextsleep'] )
	{
		db_update('planets', 'sleep = '.(time()+$_POST['sleep']).', nextsleep = '.(time()+$_POST['sleep']+14*3600), 'id = '.PLANET_ID);
		Go('logout.php');
	}
	$h = floor(($g_arrUser['nextsleep']-time())/3600);
	$m = ceil(($g_arrUser['nextsleep']-time()-$h*3600)/60);
	Save_Msg('You cannot go into sleepmode yet. You have to wait '.$h.' hours and '.$m.' minutes');
	Go();
}

_header();

?>

<div class="header">Preferences</div>

<br />
<br />

<form method="post" action="">
<table border="0" cellpadding="3" cellspacing="0">
<tr>
	<td width="130" align="right">Race</td>
	<td><b><?php echo $g_arrUser['race']; ?></td>
</tr>
<tr>
	<td width="130" align="right">Email</td>
	<td><b><?php echo $g_arrUser['email']; ?></td>
</tr>
<tr>
	<td colspan="2"><br /></td>
</tr>
<tr>
	<td></td>
	<td><b><u>Change Your Password</td>
</tr>
<tr>
	<td width="130" align="right">Old Password</td>
	<td><input type="password" name="oldpassword" style="width:200px;" /></td>
</tr>
<tr>
	<td width="130" align="right">New Password</td>
	<td><input type="password" name="newpassword" style="width:200px;" /></td>
</tr>
<tr>
	<td width="130" align="right">New Again</td>
	<td><input type="password" name="newpassword2" style="width:200;" /></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Save" /></td>
</tr>
</table>
</form>

<?php if ( $g_arrUser['nextsleep'] <= time() ) { ?>
<br />
<br />

<form method="post" action="">
<table border="0" cellpadding="3" cellspacing="0">
<tr>
	<th>Sleepmode</th>
</tr>
<tr>
	<td>You become immune from attacks while in sleep mode.<br>
If you login before your sleep mode ends, sleep mode is deactivated.<br>
You need to wait 14 hours after you exit sleepmode, to enter sleepmode again.</td>
</tr>
<tr>
	<td>
		<select name="sleep">
			<option value="28800">8 hours</option>
			<option value="25200">7 hours</option>
			<option value="21600">6 hours</option>
			<option value="18000">5 hours</option>
			<option value="14400">4 hours</option>
			<option value="10800">3 hours</option>
			<option value="7200">2 hours</option>
		</select>
		<input type=submit value="Enter Sleepmode" />
	</td>
</tr>
</table>
</form>
<?php } ?>

<?php /* ?>
<br />
<br />

<form method="post" action="">
<table border="1" cellpadding="3" cellspacing="0">
<tr><td></td><td><b><u>Change Your Email</td></tr>
<tr>
<td width=130 align=right>Old Email</td>
<td><input type=text name=old_email style='width:200;'> &nbsp; &nbsp; <font color=red>WARNING: Make sure you give a valid email!</td>
</tr>
<tr>
<td width=130 align=right>Password</td>
<td><input type=password name=password style='width:200;'> &nbsp; &nbsp; <font color=red>Your account will be blocked until you reactivate it!</td>
</tr>
<tr>
<td width=130 align=right>New Email</td>
<td><input type=text name=new_email style='width:200;'> &nbsp; &nbsp; <font color=red>The activationcode will be sent to your NEW email!</td>
</tr>
<tr>
<td width=130 align=right>New Again</td>
<td><input type=text name=new_email2 style='width:200;'> &nbsp; &nbsp; Just checking!</td>
</tr>
<tr><td></td><td><input type=submit value="Change"></td></tr>
</table>
</form>
<?php */ ?>

<br />

<?php

_footer();

?>
