<?php

require_once('inc.config.php');
logincheck();

$USER['tag'] = addslashes($USER['tag']);

// CONFIRM: kick member
if (isset($_POST['action']) && $_POST['action']=="kickmember")
{
	die("<body bgcolor=black style='font-family:Verdana;font-size:11px;color:#cccccc;'><br>Are you sure you want to kick <b>".$userarray[$_POST[uid]]." of ".$planetarray[$_POST[uid]]."</b> out?<br><br><a href=\"".basename(PARENT_SCRIPT_NAME)."\">NO!!</a><br><br><br><br><br><a href=\"?action=kickmember&define=1&really=yes&kickid=".$_POST['uid']."\">Yes</a>");
}

// CONFIRM: hand over leadership
if (isset($_POST['action']) && $_POST['action']=="handoverleadership")
{
	die("<body bgcolor=black style='font-family:Verdana;font-size:11px;color:#cccccc;'><br>Are you sure you want to hand over your leadership to <b>".$userarray[$_POST[uid]]." of ".$planetarray[$_POST[uid]]."</b>?<br><br><a href=\"".basename(PARENT_SCRIPT_NAME)."\">NO!!</a><br><br><br><br><br><a href=\"?action=handoverleadership&define=1&really=yes&newleaderid=".$_POST['uid']."\">Yes</a>");
}

// CONFIRM: leave alliance
if (isset($_POST['action']) && $_POST['action']=="leavealliance" && isset($_POST['is_it_okay']) && $_POST['is_it_okay']==1)
{
	die("<body bgcolor=black style='font-family:Verdana;font-size:11px;color:#cccccc;'><br>Are you sure you want to leave this Alliance (<b>$USER[tag]</b>)?<br><br><a href=\"".basename(PARENT_SCRIPT_NAME)."\">NO!!</a><br><br><br><br><br><a href=\"?action=leavealliance&define=1&really=yes&leader_is_last=".((isset($_POST['leader_is_last'])) ? $_POST['leader_is_last'] : '')."\">Yes</a>");
}

// CONFIRM: terminate alliance
if (isset($_POST['action']) && $_POST['action']=="terminatealliance")
{
	die("<body bgcolor=black style='font-family:Verdana;font-size:11px;color:#cccccc;'><br>Are you sure you want to TERMINATE this Alliance (<b>$USER[tag]</b>)?<br><br><a href=\"".basename(PARENT_SCRIPT_NAME)."\">NO!!</a><br><br><br><br><br><a href=\"?action=terminatealliance&define=1&really=yes\">Yes</a>");
}

// ACTION: kick member
if (isset($_GET['action']) && $_GET['action']=="kickmember" && isset($_GET['define']) && $_GET['define']==1 && isset($_GET['really']) && $_GET['really']=="yes")
{
	PSQ("UPDATE $TABLE[users] SET tag='' WHERE id='".$_GET['kickid']."';");

	AddNews("alliance","You\'ve been kicked out of your alliance!",$_GET['kickid']);
	AddNews("alliance","You\'ve kicked <b>".$userarray[$_GET['kickid']]." of ".$planetarray[$_GET['kickid']]."</b> out of your alliance!",$UID);

	Logbook("alliance","You\'ve kicked <b>".$userarray[$_GET['kickid']]." of ".$planetarray[$_GET['kickid']]."</b> out of your alliance!");
	Save_Msg($userarray[$_GET['kickid']]." was kicked from your Alliance!","green");
	Go();
}

// ACTION: hand over leadership
if (isset($_GET['action']) && $_GET['action']=="handoverleadership" && isset($_GET['define']) && $_GET['define']==1 && isset($_GET['really']) && $_GET['really']=="yes")
{
	PSQ("UPDATE $TABLE[alliances] SET leader_id='".$_GET['newleaderid']."' WHERE tag='".$USER['tag']."';");

	AddNews("alliance","You\'ve been made leader of your Alliance (<b>".$USER['tag']."</b>)! $RULERNAME quit his job as Alliance leader...",$_GET['newleaderid']);
	Logbook("alliance","To new leader (uid=".$_GET['newleaderid']."):<br>You\'ve been made leader of your Alliance (<b>".$USER['tag']."</b>)! $RULERNAME quit his job as Alliance leader...");

	Save_Msg("The leadership has been handed over to <b>".$userarray[$_GET['newleaderid']]." of ".$planetarray[$_GET['newleaderid']]."!","green");
	Go();
}

// ACTION: leave alliance
if (isset($_GET['action']) && isset($_GET['define']) && isset($_GET['really']) && $_GET['action']=="leavealliance" && $_GET['define']==1 && $_GET['really']=="yes")
{
	PSQ("UPDATE $TABLE[users] SET tag='' WHERE id='$UID';");
	$ai = PSQ("SELECT leader_id FROM $TABLE[alliances] WHERE tag='".$USER['tag']."';");
	if (!mysql_num_rows($ai))
	{
		PSQ("UPDATE $TABLE[users] SET tag='' WHERE tag='".$USER['tag']."';");
	}
	else
	{
		$leader_id = mysql_result($ai,0,'leader_id');
		if (isset($_GET['leader_is_last']) && $_GET['leader_is_last']==1)
		{
			Logbook("alliance","You left your Alliance (<b>".$USER['tag']."</b>)! Since you were the only member, the Alliance is now terminated!");
			PSQ("DELETE FROM $TABLE[alliances] WHERE tag='".$USER['tag']."';");
			AddNews("alliance","You left your Alliance (<b>".$USER['tag']."</b>)! Since you were the only member, the Alliance is now terminated!",$UID);
		}
		else
		{
			AddNews("alliance","<b>$RULERNAME of $PLANETNAME</b> has left your Alliance (<b>".$USER['tag']."</b>)!",$leader_id);
		}
	}
	Save_Msg("You have left your Alliance (<b>".$USER['tag']."</b>)!","green");
	Go();
}

// ACTION: terminate alliance
if (isset($_GET['action']) && isset($_GET['define']) && isset($_GET['really']) && $_GET['action']=="terminatealliance" && $_GET['define']==1 && $_GET['really']=="yes")
{
	// Delete the Alliance from the `alliances` table
	PSQ("DELETE FROM $TABLE[alliances] WHERE tag='".$USER['tag']."';");
	// Cycle through all users part of this alliance
	while ($kam = mysql_fetch_assoc(PSQ("SELECT id FROM $TABLE[users] WHERE tag='".$USER['tag']."' AND id!=$UID;")))
	{
		// Send every user of this Alliance a MSG that it has been terminated
		AddNews("alliance","Your Alliance leader terminated the Alliance (".$USER['tag']."). All members were kicked.",$kam['id']);
	}
	// And finally: Kick all members of this Alliance, including the owner
	PSQ("UPDATE $TABLE[users] SET tag='' WHERE tag='".$USER['tag']."';");

	// Add this piece of info to this user's NEWS
	AddNews("alliance","You terminated your alliance (".$USER['tag']."). All members were kicked. The alliance does not exist anymore...",$UID,1);
	// And add it to the general logbook
	Logbook("alliance","Terminated by #$UID");

	Save_Msg("Your Alliance was terminated!","green");
	Go();
}

if (isset($_POST['newalliance']) && isset($_POST['tag']) && $_POST['tag']!="[TAG]" && strlen($_POST['tag'])>2 && strlen($_POST['name'])>7 && isset($_POST['name']) && $_POST['name']!="[name]")
{
	$r = PSQ("SELECT * FROM $TABLE[alliances] WHERE tag='".$_POST['tag']."' OR name='".$_POST['name']."'");
	if (!mysql_num_rows($r))
	{
		$garbage = "tag".substr(md5(time()),0,7);
		$tag = goedmaken($_POST['tag']);
		$name = goedmaken($_POST['name']);
		$sql = "INSERT INTO $TABLE[alliances] (tag,pwd,name,leader_id) VALUES ('$tag','$garbage','$name','$UID')";
		PSQ($sql);
		@mail($USER['email'],"PornStars - New Alliance","Pornstars\n\n\nYou have created a new alliance:\n\nName = $name\nTag = $tag\nPassword = $garbage\n\nI wish you goodluck with creating a successful alliance...\nPornStars","From: Pornstars <".$_SERVER['HTTP_HOST'].">");
		AddNews("Alliance","You have made an Alliance. The tag is <b>$tag</b><br>The password to join the alliance is: <b>$garbage</b><br>You are now the proud owner of an alliance!",$UID,1);
		PSQ("UPDATE $TABLE[users] SET tag='$tag' WHERE id='$UID'");
		Logbook("alliance","New Alliance.<br>SQL: ".$sql);

		Save_Msg("Alliance created. The password is $garbage. You might get it in your email.","green");
		Go();
	}
	else
	{
		Save_Msg("There is already an alliance with this tag or name!","green");
		Go();
	}
}

if (isset($_GET['action']) && $_GET['action']=="refreshtagpwd")
{
	$garbage = "tag".substr(md5(time()),0,7);
	PSQ("UPDATE $TABLE[alliances] SET pwd='$garbage' WHERE leader_id='$UID'") or die(mysql_error());

	Go();
}

if (isset($_POST['joinalliance']) && isset($_POST['pwd']) && $_POST['pwd']!="[password]")
{
	$r = PSQ("SELECT * FROM $TABLE[alliances] WHERE pwd='".trim($_POST['pwd'])."';");
	if (mysql_num_rows($r))
	{
		$i = mysql_fetch_assoc($r);
		if ($i['leader_id'] == $UID)
		{
			Save_Msg("You cannot join your own alliance again!","red");
			Go();
		}
		PSQ("UPDATE $TABLE[users] SET tag='".$i['tag']."' WHERE id='$UID'");

		$garbage = "tag".substr(md5(time()),0,7);
		PSQ("UPDATE $TABLE[alliances] SET pwd='$garbage' WHERE leader_id='".$i['leader_id']."' AND tag='".$i['tag']."';");
		AddNews("Alliance","<b>".$USER['rulername']." of ".$USER['planetname']." (".$USER['x'].":".$USER['y'].")</b> has joined your Alliance.<br>The new password = <b>$garbage</b>.",$i['leader_id']);

		Logbook("alliance","Just joined <b>".$i['tag']."</b>], pwd = ".$_POST['pwd']);
		Save_Msg("You have joined an Alliance (<b>".$i['tag']."</b>)!","green");
		Go();
	}
	Save_Msg("Wrong Password!");
	Go();
}

_header();

?>
<center>
<table border=0 cellpadding=2 cellspacing=0 width="100%">
<tr><td class=header>Alliance Page</td></tr>
</table>
</center><br>
<?

if (strlen($USER['tag'])>=2)
{
	$ai = mysql_fetch_assoc(PSQ("SELECT * FROM $TABLE[alliances] WHERE tag='".$USER['tag']."';"));
	$members = PSQ("SELECT id,x,y,rulername,planetname,tag FROM $TABLE[users] WHERE tag='".$USER['tag']."';");
	$members2 = PSQ("SELECT id,x,y,rulername,planetname,tag FROM $TABLE[users] WHERE tag='".$USER['tag']."';");
	$nm = mysql_num_rows($members);
	$USER['tag'] = stripslashes($USER['tag']);
	if ($ai['leader_id'] == $UID)
	{
		echo "You are the leader of Alliance <b>".$ai['name']."</b> (Tag = ".$USER['tag'].")!<br><br>";
		echo "Password = <b>".$ai['pwd']."</b><br>\n";
		echo "<input type=button value=\"Refresh PWD\" OnClick=\"location='?action=refreshtagpwd';\"><br><br>\n";
		echo "Your alliance has $nm members, including you.<br>";
		Show_Alliance_Members($USER['tag'],$ai['leader_id']);
		if ($nm > 1)
		{
			echo "<br>If you dont like any or some of your members, this is how you can get rid of 'em:<br>";
			echo "<table border=0 cellpadding=0 cellspacing=0><form method=post name=kickmember><input type=hidden name=action value=kickmember><tr><td></td></tr></table>";
			echo "<select name=uid size=$nm style='overflow:auto;width:400px;'>";
			while ($ki = mysql_fetch_assoc($members))
			{
				echo ($ki['id'] != $UID) ? "<option value='".$ki['id']."'>(".$ki['x'].":".$ki['y'].") ".$ki['rulername']." of ".$ki['planetname']."\n" : "";
			}
			echo "</select><br><input type=submit value=\"Kick\"></form><br>";
			echo "<br>Hand over the leadership of this Alliance here...<br>";
			echo "<table border=0 cellpadding=0 cellspacing=0><form method=post name=handoverleadership><input type=hidden name=action value=handoverleadership><tr><td></td></tr></table>";
			echo "<select name=uid size=$nm style='overflow:auto;width:400px;'>";
			while ($li = mysql_fetch_assoc($members2))
			{
				echo ($li['id'] != $UID) ? "<option value='".$li['id']."'>(".$li['x'].":".$li['y'].") ".$li['rulername']." of ".$li['planetname']."\n" : "";
			}
			echo "</select><br><input type=submit value=\"Give Away\"></form><br>";
			echo "<br>If you want to leave this Alliance for whatever reason, you must first appoint a new leader! If you have:<br><form method=post name=leavealliance><input type=hidden name=action value=leavealliance><input type=hidden name=is_it_okay value=".(($ai['leader_id']!=$UID || $nm<2)?"1":"0")."><input type=hidden name=leader_is_last value=".(($nm<2)?"1":"0")."><input type=submit value=\"Leave Alliance\"></form>";
		}
		echo ($ai['leader_id']==$UID) ? "<br>If you want to terminate this Alliance and kick all Members:<br><form method=post name=terminatealliance><input type=hidden name=action value=terminatealliance><input type=submit value=\"Terminate Alliance\"></form><br><br>" : "";
	}
	else
	{
		echo "You are member of Alliance <b>".$ai['name']."</b> (Tag = ".$USER['tag'].")!<br><br>";
		Show_Alliance_Members($USER['tag'],$ai['leader_id']);
		echo "<br>If you want to leave this Alliance for whatever reason, click here:<br><form method=post name=leavealliance><input type=hidden name=action value=leavealliance><input type=hidden name=is_it_okay value=1><input type=submit value=\"Leave Alliance\"></form><br><br>";
	}
}
else
{
	?>
<table border=0 cellpadding=3 cellspacing=0>
<tr>
<form name=joinalliance method=post><input type=hidden name=joinalliance value=1>
<td align=right><b>Join An Alliance</td>
<td>
<input type=text name=pwd value="[password]" autocomplete=off style='width:200px;text-align:center;' OnClick="this.select();"> - Ask the alliance leader for the password</td>
</tr>
<tr>
<td></td>
<td><input type=submit value="-GO-"></td>
</form></tr>
<tr><td align=right>OR</td></tr>
<tr>
<form name=newalliance method=post><input type=hidden name=newalliance value=1>
<td align=right><b>Create New Alliance</td>
</tr>
<tr>
<td align=right>Alliance Name</td>
<td><input type=text name=name value="[name]" autocomplete=off maxlength=40 style='width:200px;text-align:center;' OnClick="this.select();"> - Minimum of 8 chars. You will be alliance leader</td>
</tr>
<tr>
<td align=right>Alliance TAG</td>
<td><input type=text name=tag value="[TAG]" autocomplete=off maxlength=8 style='width:200px;text-align:center;' OnClick="this.select();"> - Minimum of 3 chars.</td>
</tr>
<tr>
<td></td>
<td><input type=submit value="-GO-"></td>
</form></tr>
</table>
	<?php
}

_footer();

?>