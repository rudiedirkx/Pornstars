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

	return do_redirect(null);
}

if ( !isset($_SESSION['ADMIN']) || !validateAdminPassword($_SESSION['_ADMIN']) ) {
	?>
<title><?= $GAMENAME ?></title>
<form method=post>
	<input type=hidden name=check value=1 />
	<input type=hidden name=mode value=login />
	<input type=password name=pwd />

	<input type=submit value=Login />
</form>
	<?php

	exit;
}

else if ( isset($_POST['pk'], $_POST['tbl'], $_POST[$_POST['pk']], $_POST['changes']) ) {
	if ( isset($_POST['changes']['password']) ) {
		$_POST['changes']['password'] = md5($_POST[$_POST['pk']] . ':' . $_POST['changes']['password']);
	}

	$arrFields = explainTbl($_POST['tbl']);

	foreach ( (array)$_POST['changes'] AS $szField => $mvChange ) {
		$szField = strtolower($szField);
		switch ( $arrFields[$szField]['type'] )
		{
			case 'int':
			case 'float':
				$f = $arrFields[$szField]['type'].'val';
				if ( empty($mvChange) && $arrFields[$szField]['null'] ) {
					$c = 'NULL';
				}
				else if ( substr(trim($mvChange),0,1) == '-' || substr(trim($mvChange),0,1) == '+' || substr(trim($mvChange),0,1) == '/' || substr(trim($mvChange),0,1) == '*' ) {
					$c = $szField . substr(trim($mvChange),0,1) . $f(substr(trim($mvChange),1));
				}
				else {
					$c = $f(trim($mvChange));
				}
				$c = '`' . $szField . '` = ' . $c;
			break;

			default:
			case 'enum':
			case 'string':
			case 'text':
				if ( empty($mvChange) && $arrFields[$szField]['null'] ) {
					$c = 'NULL';
				}
				else {
					$c = "'" . addslashes($mvChange) . "'";
				}
				$c = '`' . $szField . '` = ' . $c;
			break;
		}
#var_dump($c);
		db_update($_POST['tbl'], $c, '`'.$_POST['pk'].'` = '.(int)$_POST[$_POST['pk']]);
	}
#exit;
	Go('?tbl='.$_POST['tbl'].'&pk='.$_POST['pk'].'&'.$_POST['pk'].'='.(int)$_POST[$_POST['pk']]);
	exit;
}


else if ( isset($_POST['mode']) && $_POST['mode'] == 'prefs' )
{
	$arrTblGamePrefs = db_fetch('EXPLAIN prefs;');
	$arrUpdate = array();
	foreach ( $arrTblGamePrefs AS $v )
	{
		$f = $v['Field'];
		if ( 'id' == $f || 'tickcount' == $f || 'last_tick' == $f ) {
			continue;
		}
		$t = $v['Type'];
		switch ( strtolower(reset(explode('(', $t))) )
		{
			case 'enum':
				$arrUpdate[$f] = (string)(int)isset($_POST[$f]);
			break;
			default:
				if ( isset($_POST[$f]) ) {
					$arrUpdate[$f] = $_POST[$f];
				}
			break;
		}
	}
	if ( !empty($_POST['autologout']) ) {
		db_update('planets', 'unihash = \'\'');
		$arrUpdate['autologout'] = '0';
	}
	db_update('prefs', $arrUpdate, 'id = '.(int)$GAMEPREFS['id']);
	Go();
}

?>
<html>

<head>
<title><?php echo $GAMENAME; ?></title>
<link rel="stylesheet" href="css/styles.css" />
<script src="/ajax_1_3_1.js"></script>
<script src="/general_1_2_7.js"></script>
<style>
html,body,table,input,select,textarea { background-color:white;color:#000000; }
body { margin:0; }
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
						<td><textarea name="general_adminmsg"><?= html($g_prefs->general_adminmsg) ?></textarea></td>
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
