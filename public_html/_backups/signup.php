<?

include("config.php");

$planets_in_one_galaxy = 4;

if (!$GAMEPREFS[general_signup] && !$_SESSION[ED_TEST_ADMIN])
{
	echo "<html>
	<head>
	<title>$PA[name] ".ereg_replace("www.","",$_SERVER[HTTP_HOST])." SIGNUP</title>
	<link rel=stylesheet href=\"css/styles.css\">
	</head>
	<body bgcolor=black><font color=aaaaaa>
	<center>
	<br>
	<br>
	<br><img src=\"images/shadow.jpg\"><br><br>";

	$tickerstatus = ($GAMEPREFS[ticker_on]) ? "ON" : "OFF";
	$loginstatus = ($GAMEPREFS[general_login]) ? "" : "NOT";

	die("<b>Signup is closed for the moment.</b> That doesnt mean the ticker isnt working.<br>Ticker is <b>$tickerstatus</b>!<br>To login is <b>$loginstatus possible</b>!");
}

$username=$_POST[username];
$email=$_POST[email];
$ruler=htmlspecialchars($_POST[ruler]);
$planet=htmlspecialchars($_POST[planet]);
if ($_POST[check])
{
	if (!$username)
	{
		die("Username not specified!");
	}
	if (!$email)
	{
		die("E-Mail not specified!");
	}
	if (!$planet)
	{
		die("Planetname not specified!");
	}
	if (!$ruler)
	{
		die("Rulername not specified!");
	}

	if (!preg_match("/(?i)^([a-z0-9._-])+@([a-z0-9.-])+\.([a-z0-9]){2,4}$/",$email))
	{
		die("E-mail is not valid!");
	}

	if (!Goede_Gebruikersnaam($username))
	{
		die("Username is not valid! Dont use any weird charachters!");
	}

	$result = mysql_query("SELECT id FROM $TABLE[users]") or die(mysql_error());
	$count = mysql_num_rows($result);
	$miny=1;
	$minx=1;

	for ($i=1;$i<=$count;$i++)
	{
		$result2 = mysql_query("SELECT * FROM $TABLE[users] WHERE x='$minx' AND y='$planets_in_one_galaxy'") or die(mysql_error());
		if ($myrow = mysql_fetch_array($result2))
		{
			$minx++; $miny=1;
		}

		$result = mysql_query("SELECT * FROM $TABLE[users] WHERE x='$minx' AND y='$miny'") or die(mysql_error());
		if ($myrow = mysql_fetch_array($result))
		{
			$miny++;
		}
	}

	$result = mysql_query("SELECT id FROM $TABLE[users] WHERE username='$username'");
	if (mysql_num_rows($result))
	{
		die("User <b>\"$username\"</b> already registered!");
	}
	$result = mysql_query("SELECT id FROM $TABLE[users] WHERE email='$email'");
	if (mysql_num_rows($result))
	{
		die("User with e-mail <b>\"$email\"</b> already registered!");
	}
	$result = mysql_query("SELECT id FROM $TABLE[users] WHERE rulername='$ruler'");
	if (mysql_num_rows($result))
	{
		die("Rulername <b>\"$ruler\"</b> already registered!");
	}
	$result = mysql_query("SELECT id FROM $TABLE[users] WHERE planetname='$planet'");
	if (mysql_num_rows($result))
	{
		die("Planetname <b>\"$planet\"</b> already registered!");
	}

	$garbage = substr(md5(time()),0,12);
	$time=time();

	$sql = "INSERT INTO $TABLE[users] (username,email,password,rulername,planetname,x,y,timer,asteroid_ui,newbie,galpic) VALUES ('$username','$email','".md5($garbage)."','$ruler','$planet','$minx','$miny','$time','1','200','images/death.jpg')";
	Logging("register",$sql);

	$result = mysql_query($sql) or die(mysql_error());
	$nid = mysql_insert_id();
	mysql_query("UPDATE $TABLE[users] SET vote='$nid' WHERE id='$nid'");

//	Header("Location: ./?msg=Password = $garbage<br><br>Password will be sent to your e-mail ($email)... Change it in PREFS asap!");

	echo "<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%><tr valign=middle><td><font style='font-size:11px;font-family:Verdana;'><center>Password = $garbage<br><br>Password will be sent to your e-mail ($email)...<br><br><br><br><a href=\"login.php\">login</td></tr></table>";
	@mail($email,$PA[name]." - Account",$PA[name]."\n\n account:\n\nUsername: $username\nPassword: $garbage\n\nYou can edit the password under 'Preferences' after you log in.","From: ED Test <ED_TEST>\nReturn-Path: <ED_TEST>");

	exit;
}
else
{
	?>
<html>

<head>
<title><?=$PA[name]?> <?=ereg_replace("www.","",$_SERVER[HTTP_HOST])?> SIGNUP</title>
<link rel=stylesheet href="css/styles.css">
</head>

<body bgcolor=black><font color=aaaaaa>
<center>
<br>
<br>
<br>

<b>SIGNUP</b><br>
<br>
<br>

<form name=register method=post action=?>
<input type=hidden name=check value=1>
Username<br>
<input type=text name=username style='width:200;' maxlength=15><br>
<br>

E-mail<br>
<input type=text name=email style='width:200;'><br>
<br>

Rulername<br>
<input type=text name=ruler style='width:200;'><br>
<br>

Planetname<br>
<input type=text name=planet style='width:200;'><br>
<br>

<br>
<input type=submit value="sign up" style='width:200;'><br>
</form>

	<?

}


