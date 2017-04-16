<?php

use rdx\ps\Galaxy;
use rdx\ps\Planet;

require 'inc.bootstrap.php';

function getClassRecord($type, $id) {
	switch ($type) {
		case 'planets':
			return Planet::find($id);
		case 'galaxies':
			return Planet::find($id);
	}
}

if ( isset($_POST['check'], $_POST['mode']) && $_POST['mode'] == "login" ) {
	if ( validateAdminPassword($_POST['pwd']) ) {
		$_SESSION['ADMIN'] = $_POST['pwd'];
	}

	return do_redirect();
}

if ( !isset($_SESSION['ADMIN']) || !validateAdminPassword($_SESSION['ADMIN']) ) {
	?>
<title><?= $GAMENAME ?></title>
<form method=post>
	<input type="hidden" name="check" value="1" />
	<input type="hidden" name="mode" value="login" />
	<input type="password" name="pwd" />

	<button>Login</button>
</form>
	<?php

	exit;
}

else if ( isset($_POST['gamename']) ) {
	$g_prefs->update($_POST);

	return do_redirect();
}

?>
<html>

<head>
<title><?php echo $GAMENAME; ?></title>
<link rel="stylesheet" href="css/styles.css" />
<script src="/ajax_1_3_1.js"></script>
<script src="/general_1_2_7.js"></script>
<style>
body {
	margin: 0;
}
input[type="number"] {
	width: 4em;
}
</style>
<script>
var g_szTable = '<?php echo isset($_GET['tbl']) ? $_GET['tbl'] : 'planets'; ?>';
</script>
</head>

<body>

<table border="0" cellpadding="5" cellspacing="0" height="100%">
	<tr valign="middle" height="40">
		<td width="50%">
			<div align="center">
				<select onchange="document.getElementById(g_szTable).style.display='none';document.getElementById(this.value).style.display='';g_szTable=this.value;">
					<option>planets</option>
					<option<?php echo isset($_GET['tbl']) && 'galaxies' == $_GET['tbl'] ? ' selected="1"' : ''; ?>>galaxies</option>
				</select>
			</div>
			<div id="planets" hidden>
				<select onchange="if(this.value){document.location='?tbl=planets&pk=id&id='+this.value;}" style="width:100%;">
					<option value="">-- planets</option>
					<?php
					$planets = Planet::all();
					foreach ( $planets AS $planet ) {
						echo '<option value="' . $planet->id . '">' . $planet . '</option>';
					}
					?>
				</select>
			</div>
			<div id="galaxies" hidden>
				<select onchange="if(this.value){document.location='?tbl=galaxies&pk=id&id='+this.value;}" style="width:100%;">
					<option value="">-- galaxies</option>
					<?php
					$galaxies = Galaxy::all();
					foreach ( $galaxies AS $galaxy ) {
						echo '<option value="' . $galaxy->id . '">' . $galaxy . '</option>';
					}
					?>
				</select>
			</div>
			<script>document.getElementById(g_szTable).hidden = false</script>
		</td>
		<td width=20 bgcolor=#dddddd rowspan=2>&nbsp;&nbsp;&nbsp;<br /></td>
		<td width=50%>Gameprefs</td>
	</tr>
	<tr valign=top>
		<td>
			<?php

			if ( isset($_GET['tbl'], $_GET['pk'], $_GET[$_GET['pk']]) && $object = getClassRecord($_GET['tbl'], $_GET[ $_GET['pk'] ]) ) {
				echo '<pre>';
				echo html(print_r($object), 1);
				echo '</pre>';
			}

			?>
		</td>
		<td>
			<form method="post" action="comp.php">
				<table border="1">
					<tr>
						<td>Game name</td>
						<td><input name="gamename" value="<?= html($g_prefs->gamename) ?>" /></td>
					</tr>
					<tr>
						<td>Ticker time</td>
						<td><input type="number" name="tickertime" value="<?= $g_prefs->tickertime ?>" /> sec</td>
					</tr>
					<tr>
						<td>Ticking?</td>
						<td><input type="checkbox" name="ticker_on" <?= $g_prefs->ticker_on ? 'selected' : ''; ?> /></td>
					</tr>
					<tr>
						<td>Admin message</td>
						<td><textarea cols="60" rows="6" name="general_adminmsg"><?= html($g_prefs->general_adminmsg) ?></textarea></td>
					</tr>
					<tr>
						<td>Galaxy size</td>
						<td><input type="number" name="planets_per_galaxy" value="<?= $g_prefs->planets_per_galaxy ?>" /> planets</td>
					</tr>
					<tr>
						<td>Outgoing fleets</td>
						<td><input type="number" name="num_outgoing_fleets" value="<?= $g_prefs->num_outgoing_fleets ?>" /> fleets</td>
					</tr>
					<tr>
						<td>Fleet names</td>
						<td><input name="fleetnames" value="<?= html($g_prefs->fleetnames) ?>" /></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button>Opslaan</button></td>
					</tr>
				</table>
			</form>

			<br />
			<br />

			<a href="multitracker.php">MultiTracker</a>
		</td>
	</tr>
</table>
