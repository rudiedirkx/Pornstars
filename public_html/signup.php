<?php

require_once('inc.config.php');

if ( logincheck(false) ) {
	Go('./');
}

if ( !$GAMEPREFS['general_signup'] && !$_SESSION['ADMIN'] ) {
	$tickerstatus = ($GAMEPREFS['ticker_on']) ? "ON" : "OFF";
	$loginstatus = ($GAMEPREFS['general_login']) ? "" : "NOT";
	Save_Msg("<b>Signup is closed for the moment.</b><br>Ticker is <b>$tickerstatus</b>!<br>To login is <b>$loginstatus possible</b>!","red");
	Go("?changepage=index");
}

if ( isset($_POST['email'], $_POST['rulername'], $_POST['planetname'], $_POST['password'], $_POST['password2'], $_POST['race_id']) )
{
	if ( 4 > strlen($_POST['rulername']) ) {
		exit('Rulername too short!');
	}
	else if ( 4 > strlen($_POST['planetname']) ) {
		exit('Planetname too short!');
	}
	else if ( md5($_POST['password']) !== md5($_POST['password2']) ) {
		exit('Passwords don\'t match!');
	}
	else if ( db_count('planets', "rulername = '".addslashes($_POST['rulername'])."'") ) {
		exit('Rulername taken!');
	}
	else if ( db_count('planets', "planetname = '".addslashes($_POST['planetname'])."'") ) {
		exit('Planetname taken!');
	}
	else if ( db_count('planets', "email = '".addslashes($_POST['email'])."'") ) {
		exit('E-mail address taken!');
	}
	else if ( !db_count('d_races', 'id = '.(int)$_POST['race_id']) ) {
		exit('Invalid race!');
	}
	$arrInsert = array(
		'email'					=> $_POST['email'],
		'activationcode'		=> md5(microtime()),
		'rulername'				=> $_POST['rulername'],
		'planetname'			=> $_POST['planetname'],
		'uninitiated_asteroids'	=> 3,
		'race_id'				=> (int)$_POST['race_id'],
	);
	// Password, galaxy_id and z-coord come later
	$iPlanetsPerGalaxy = max(2, (int)$GAMEPREFS['planets_per_galaxy']);
	$bPlaced = false;
	for ( $x=1; !$bPlaced; $x++ ) {
		for ( $y=1; ($y<=$iPlanetsPerGalaxy && !$bPlaced); $y++ ) {
			for ( $z=1; ($z<=$iPlanetsPerGalaxy && !$bPlaced); $z++ ) {
				if ( !db_count('galaxies g, planets p', 'p.galaxy_id = g.id AND x = '.$x.' AND y = '.$y.' AND z = '.$z) ) {
					$bPlaced = true;
					$iGalaxyId = db_select_one('galaxies', 'id', 'x = '.$x.' AND y = '.$y);
					if ( false === $iGalaxyId ) {
						db_insert('galaxies', array('x' => $x, 'y' => $y));
						$iGalaxyId = db_insert_id();
						db_query('INSERT INTO galaxy_resources (galaxy_id, resource_id) SELECT '.$iGalaxyId.', r.id FROM d_resources r;');
					}
					$iZCoord = $z;
				}
			}
		}
	}
	$arrInsert['galaxy_id'] = $iGalaxyId;
	$arrInsert['z'] = $iZCoord;
	// Insert Planet
	if ( !db_insert('planets', $arrInsert) ) {
		exit(__LINE__.':'.db_error());
	}
	$iPlanetId = db_insert_id();
	// Update planet Password
	db_update('planets', 'password = MD5(CONCAT(id,\':'.addslashes($_POST['password']).'\'))', 'id = '.(int)$iPlanetId);

	// Insert resources
	db_query('INSERT INTO planet_resources (planet_id, resource_id, amount) SELECT '.$iPlanetId.', r.id, 2000 FROM d_resources r;');

	// Insert Fleets
	for ( $i=0; $i<=max($GAMEPREFS['num_outgoing_fleets'],1); $i++ ) {
		db_insert('fleets', array('owner_planet_id' => $iPlanetId, 'fleetname' => (string)$i));
		$iFleetId = db_insert_id();
		// Insert Ships
		db_query('INSERT INTO ships_in_fleets (fleet_id, amount, ship_id) SELECT '.$iFleetId.', 0, id FROM d_ships;');
	}

	// Insert Defence
	db_query('INSERT INTO defence_on_planets (planet_id, amount, defence_id) SELECT '.$iPlanetId.', 0, id FROM d_defence;');

	// Insert Power
	db_query('INSERT INTO power_on_planets (planet_id, amount, power_id) SELECT '.$iPlanetId.', 0, id FROM d_power;');

	// Insert Waves
	db_query('INSERT INTO waves_on_planets (planet_id, amount, wave_id) SELECT '.$iPlanetId.', 0, id FROM d_waves;');

	// Insert Skills
	db_query('INSERT INTO planet_skills (planet_id, skill_id, value) SELECT '.$iPlanetId.', id, 1 FROM d_skills;');

	// Send e-mail with activationcode and details
	$gamename0 = str_replace("www.", "", $_SERVER['HTTP_HOST']);
	$headers  = "From: PORNSTARS - NEW ACCOUNT <pornstars@".$gamename0.">\r\n";
	$headers .= "Return-Path: <pornstars@".$gamename0.">\r\n";
	$headers .= "X-Sender: <pornstars@".$gamename0.">\r\n";
//	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$szMessage = '
Hi,

you signed up for an account on PORNSTARS!
Your details:
PLANET ID = '.$iPlanetId.'
E-mail = '.$arrInsert['email'].'
Password = '.$_POST['password'].'
Rulername = '.$arrInsert['rulername'].'
Planetname = '.$arrInsert['planetname'].'

You can login with your e-mail address and password.
You can edit your password, and other preferences, in the section Preferences, after you login.

To be able to login, you\'re going to need to activate your account.
Your actination code = '.$arrInsert['activationcode'].'


Have fun playing.

Direct any questions to the Help section of the game or ask another player to contact an Admin in the game.
';
	if ( mail($arrInsert['email'], "PORNSTARS - NEW PLANET", $szMessage, $headers) ) {
		exit('If your e-mail was valid, you have mail! If not, or you can\'t find it, you are owner of planet # '.$iPlanetId.'. Contact an Admin!');
	}
	db_update('planets', 'activationcode = \'\'', 'id = '.(int)$iPlanetId);
	exit('Sending a mail failed!!! You might not be able to login now... In that case, contact an admin through the Help section! You are owner of planet # '.$iPlanetId.'!');
}

echo $indextitel;

$arrRaces = db_select_fields('d_races', 'id,race', '1 ORDER BY id ASC');

?>
<html>

<head>
<title><?php echo $GAMENAME; ?></title>
<link rel="stylesheet" type="text/css" href="css/styles.css">
<script type="text/javascript" src="general_1_2_6.js"></script>
<script type="text/javascript" src="ajax_1_3_1.js"></script>
</head>

<body bgcolor="black">

<center>
<br>

<?php include 'inc.message.php'; ?>

<?php echo (!$GAMEPREFS['general_signup'])?"<font color=red><b>SIGNUP HAS BEEN DISABLED</b></font><br>":""?>

<br />
<b>SIGNUP</b><br />

<br />
<br />

<form method="post" action="signup.php" autocomplete="off" onsubmit="return postForm(this, function(a){var t=a.responseText;alert(t);});">
<table border="0" cellpadding="3" cellspacing="0">
<tr>
	<td colspan="3">E-mail</td>
</tr>
<tr>
	<td><input type="text" name="email" style="width:200px;" maxlength="255" /></td>
	<td>&nbsp;</td>
	<td>:: you're gonna need this one to verify your account</td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
	<td colspan="3">Password</td>
</tr>
<tr>
	<td><input type="password" name="password" style="width:200px;" /></td>
	<td>&nbsp;</td>
	<td></td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
	<td colspan="3">Repeat</td>
</tr>
<tr>
	<td><input type="password" name="password2" style="width:200px;" /></td>
	<td>&nbsp;</td>
	<td>:: for security</td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
	<td colspan="3">Rulername</td>
</tr>
<tr>
	<td><input type="text" name="rulername" style="width:200px;" maxlength="30" /></td>
	<td>&nbsp;</td>
	<td>:: no weird characters, spaces are allowed</td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
	<td colspan="3">Planetname</td>
</tr>
<tr>
	<td><input type="text" name="planetname" style="width:200px;" maxlength="30" /></td>
	<td>&nbsp;</td>
	<td>:: no weird characters, spaces are allowed</td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
	<td colspan="3">Race</td>
</tr>
<tr>
	<td><select name="race_id" size="<?php echo count($arrRaces); ?>" style="width:200px;"><?php foreach ( $arrRaces AS $iRace => $szRace ) { echo '<option value="'.$iRace.'">'.$szRace.'</option>'; } ?></select></td>
	<td>&nbsp;</td>
	<td>:: you can't change this anymore</td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>
<tr>
	<td><input type="submit" value="sign up" style="width:200px;" /></td>
</tr>
</table>
</form>

</body>

</html>
<?php

unset($_SESSION['ps_msg']);

?>
