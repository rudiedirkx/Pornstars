<?

include("config.php");

if ($UID)
{
	Header("Location: menu.php");
	exit();
}

if (!$GAMEPREFS[general_login] && !$_SESSION[ED_TEST_ADMIN])
{
	echo "<html>
	<head>
	<title>$PA[name] ".ereg_replace("www.","",$_SERVER[HTTP_HOST])." LOGIN</title>
	<link rel=stylesheet href=\"css/styles.css\">
	</head>
	<body bgcolor=black><font color=aaaaaa>
	<center>
	<br>
	<br>
	<br><img src=\"images/shadow.jpg\"><br><br>";

	$tickerstatus = ($GAMEPREFS[ticker_on]) ? "ON" : "OFF";
	$signupmsg = ($GAMEPREFS[general_signup]) ? "YOU CAN SIGN UP ALREADY, FOR THE NEXT ROUND!" : "Sign up is also closed!";

	die("<b>Login is closed for the moment.</b> That doesnt mean the ticker isnt working.<br>Ticker is <b>$tickerstatus</b>!<br><br><b>$signupmsg</b>");
}

if ($_POST[check])
{
	$usr	= $_POST[username];
	$pwd	= $_POST[password];

	$result = mysql_query("SELECT id,username,password,closed,lastlogin FROM $TABLE[users] WHERE username='$usr' AND password='".md5($pwd)."'");
	$myrow = mysql_fetch_assoc($result);
	if (time()-$myrow[lastlogin] < 300)
	{
		die("<html>
		<head>
		<title>$PA[name] ".ereg_replace("www.","",$_SERVER[HTTP_HOST])." LOGIN</title>
		<link rel=stylesheet href=\"css/styles.css\">
		</head>
		<body bgcolor=black><font color=aaaaaa>
		<center>
		<br>
		<br>
		<br><img src=\"images/hellspawn.jpg\"><br><br>There is less time than 5minutes since your last login on this account. You sure you're not cheating? Wait more minutes!!");
	}
	if ($myrow[closed]==1)
	{
		die("<html>
		<head>
		<title>$PA[name] ".ereg_replace("www.","",$_SERVER[HTTP_HOST])." LOGIN</title>
		<link rel=stylesheet href=\"css/styles.css\">
		</head>
		<body bgcolor=black><font color=aaaaaa>
		<center>
		<br>
		<br>
		<br><img src=\"images/hellspawn.jpg\"><br><br>Your account is closed, probably due to multiing!");
	}

	if (mysql_num_rows($result))
	{
		$UID = $myrow[id];
		Logging("login",$usr);

		$save[username] = $usr;
		$save[password] = md5($pwd);
		$save[uid] = $UID;

		$_SESSION[ED_TEST] = $save;

		$time = time();
		mysql_query("UPDATE $TABLE[users] SET sleep='0',lastaction='$time',lastlogin='$time' WHERE id='$UID'");

		Header("Location: menu.php?");
		exit();
	}
	else
	{
		die("<html>
		<head>
		<title>$PA[name] ".ereg_replace("www.","",$_SERVER[HTTP_HOST])." LOGIN</title>
		<link rel=stylesheet href=\"css/styles.css\">
		</head>
		<body bgcolor=black><font color=aaaaaa>
		<center>
		<br>
		<br>
		<br><img src=\"images/hellspawn.jpg\"><br><br>Wrong login! You'd better be right next time!!");
	}
}

else if ($_POST[pwdvergeten])
{
	$i = mysql_fetch_assoc(mysql_query("SELECT username,email,password FROM $TABLE[users] WHERE email='$_POST[email]'"));

	if ($i[username])
	{
		$gameaddress = $_SERVER[HTTP_HOST].$_SERVER[PHP_SELF];
		$gamename="ED ".ereg_replace("www.","",$_SERVER[HTTP_HOST])." LOGIN";
		$mailed = @mail($i[email],"$gamename - Forgotten Password","Use the following link to login. Change your password as soon as possible after that.\n\nhttp://".$gameaddress."?pwdvergeten=1&username=$i[username]&password=$i[password]\n\n\nGoodluck","From: ED Test <ED_TEST>\nReturn-Path: <ED_TEST>");

		$msg = ($mailed) ? "Your password has been sent to <b>$i[email]" : "We have failed in sending you your password. Wrong e-mail server";
		Header("Location: ./?msg=$msg");
		exit;
	}
	else
	{
		Header("Location: ./?msg=Cant find the emailaddress <b>$_POST[email]</b>");
		exit;
	}
}

else if ($_GET[pwdvergeten] && $_GET[username] && $_GET[password])
{
	$a = mysql_query("SELECT * FROM $TABLE[users] WHERE username='$_GET[username]' AND password='$_GET[password]'");
	if (!mysql_num_rows($a))
	{
		Header("Location: ?");
		exit;
	}

	$i = mysql_fetch_assoc($a);
	$UID = $i[id];
	Logging("login",$usr);

	$save[username] = $i[username];
	$save[password] = $i[password];
	$save[uid] = $UID;
	$save[access] = $i[acces];

	$_SESSION[ED_TEST] = $save;

	mysql_query("UPDATE $TABLE[users] SET sleep='0' WHERE id='$UID'");

	Header("Location: menu.php?");
	exit();
}

else if (!$_POST[check] && !$_POST[pwdvergeten])
{
	?>
<html>

<head>
<title><?=$PA[name]?> <?=ereg_replace("www.","",$_SERVER[HTTP_HOST])?> LOGIN</title>
<link rel=stylesheet href="css/styles.css">
</head>

<body bgcolor=black><font color=aaaaaa>
<center>
<br>
<br>
<br>

<b>LOGIN</b><br>
<br>
<br>

<form name=login method=post action=?>
<input type=hidden name=check value=1>
Username<br>
<input type=text name=username style='width:200;'><br>
<br>

Password<br>
<input type=password name=password style='width:200;'><br>
<br>

<br>
<input type=submit value="login" style='width:200;'><br>
</form>

	<?
}

?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<form action=? method=post><input type=hidden name=pwdvergeten value=1>

<b>Forgot your password? Dont worry!<br>
<br>
Email: <input type=text name=email style='width:200;'><br>
<br>
<input type=submit value="Send Password" style='width:200;'>

</form>