<?php

require_once('inc.config.php');

$ADMINPWD1 = "8007db9d948c88b6495a56fbe91b560f";
$ADMINPWD2 = "e3c2f9720cfc394a8975a1c87f3d949f";

function explainTbl($tbl) {
	$e = db_fetch('explain `'.$tbl.'`;');
	$f = array();
	foreach ( $e AS $v ) {
		$o = $d = null;
		$p = 'string';
		if ( strstr($v['Type'], 'int') ) {
			$p = 'int';
			$d = (int)substr($v['Type'], strpos($v['Type'], '(')+1);
		}
		else if ( substr($v['Type'],0,4) == 'enum' ) {
			$p = 'enum';
			$o = explode("','", trim(substr($v['Type'],4),"'()"));
		}
		else if ( strstr($v['Type'], 'text') ) {
			$p = 'text';
		}
		else if ( strstr($v['Type'], 'float') || strstr($v['Type'], 'double') ) {
			$p = 'float';
		}
		$t = array(
			'type'		=> $p,
			'null'		=> 'YES' === $v['Null'],
			'options'	=> $o,
			'value'		=> $d,
		);
		$f[strtolower($v['Field'])] = $t;
	}
	return $f;
}

if ( isset($_POST['check'], $_POST['mode']) && $_POST['mode'] == "login" ) {
	if (md5($_POST['pwd']) == $ADMINPWD1 || md5($_POST['pwd']) == $ADMINPWD2) {
		$_SESSION[$sessionname.'_ADMIN'] = $_POST['pwd'];
	}
	Go();
}

if ( !isset($_SESSION[$sessionname.'_ADMIN']) || (md5($_SESSION[$sessionname.'_ADMIN']) != $ADMINPWD1 && md5($_SESSION[$sessionname.'_ADMIN']) != $ADMINPWD2) )
{
?>
<title><?php echo $GAMENAME; ?></title>
<form method=post action=?>
<input type=hidden name=check value=1>
<input type=hidden name=mode value=login>
<input type=password name=pwd>

<input type=submit value=Login>
<?php
	die("</form>");
}

else if ( isset($_POST['pk'], $_POST['tbl'], $_POST[$_POST['pk']], $_POST['changes']) )
{
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
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="http://games.jouwmoeder.nl/ajax_1_2_1.js"></script>
<script type="text/javascript" src="http://games.jouwmoeder.nl/general_1_2_5.js"></script>
<style>
html,body,table,input,select,textarea { background-color:white;color:#000000; }
body { margin:0; }
</style>
<script type="text/javascript">
<!--//
var g_szTable = '<?php echo isset($_GET['tbl']) ? $_GET['tbl'] : 'planets'; ?>';
//-->
</script>
</head>

<body>
<table border="0" cellpadding="5" cellspacing="0" height="100%">
<tr valign="middle" height="40"><td width="50%">
<div align="center"><select onchange="document.getElementById(g_szTable).style.display='none';document.getElementById(this.value).style.display='';g_szTable=this.value;"><option>planets</option><option<?php echo isset($_GET['tbl']) && 'galaxies' == $_GET['tbl'] ? ' selected="1"' : ''; ?>>galaxies</option></select></div>
<div id="planets" style="display:none;"><select onchange="if(this.value){document.location='?tbl=planets&pk=id&id='+this.value;}" style="width:100%;">
<option value="">-- planets</option>
<?php
$arrPlanets = db_select_fields('galaxies g, planets p', 'p.id,concat(p.id,\'. \',email,\' - \',rulername,\' of \',planetname,\' (\',x,\':\',y,\':\',z,\')\')', 'g.id = p.galaxy_id ORDER BY g.x ASC, g.y ASC, p.z ASC');
foreach ( $arrPlanets AS $k => $v ) {
	echo '<option value="'.$k.'">'.$v.'</option>';
}
?>
</select></div>
<div id="galaxies" style="display:none;"><select onchange="if(this.value){document.location='?tbl=galaxies&pk=id&id='+this.value;}" style="width:100%;">
<option value="">-- galaxies</option>
<?php
$arrGalaxies = db_select_fields('galaxies', 'id,concat(id,\'. \',name,\' (\',x,\':\',y,\')\')', '1 ORDER BY x ASC, y ASC');
foreach ( $arrGalaxies AS $k => $v ) {
	echo '<option value="'.$k.'">'.$v.'</option>';
}
?>
</select></div>
<script type="text/javascript">document.getElementById(g_szTable).style.display='';</script>
</td>
<td width=20 bgcolor=#dddddd rowspan=2>&nbsp;&nbsp;&nbsp;<br></td>
<td width=50%>Gameprefs</td>
</tr>
<tr valign=top>
<td>
<?php

if ( isset($_GET['tbl'], $_GET['pk'], $_GET[$_GET['pk']]) && 0 < count($arrRecords=db_fetch('SELECT * FROM `'.$_GET['tbl'].'` WHERE `'.addslashes($_GET['pk']).'` = '.(int)$_GET[$_GET['pk']].';')) )
{
	$arrRecord = $arrRecords[0];
	$arrFields = explainTbl($_GET['tbl']);

	?>
<form method="post" action="comp.php">
<input type="hidden" name="mode" value="edituser" />
<input type="hidden" name="tbl" value="<?php echo $_GET['tbl']; ?>" />
<input type="hidden" name="pk" value="<?php echo $_GET['pk']; ?>" />
<input type="hidden" name="<?php echo $_GET['pk']; ?>" value="<?php echo $arrRecord[$_GET['pk']]; ?>" />
<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr bgcolor="#dddddd" class="bt bb">
	<th width="30%">NAME</th>
	<th>VALUE</th>
	<th width="40%">CHANGE</th>
</tr>
<?php

$arrConstraints = array();
if ( 'planets' == $_GET['tbl'] ) {
	$arrConstraints['galaxy_id'] = $arrFields['galaxy_id']['null'] ? '<option value="" style="font-style:italic;">-- NULL</option>' : '';
	foreach ( db_select('galaxies', '1 ORDER BY x ASC, y ASC') AS $v ) {
		$arrConstraints['galaxy_id'] .= '<option'.( $v['id'] == $arrRecord['galaxy_id'] ? ' selected="1"' : '' ).' value="'.$v['id'].'">'.$v['id'].'. ('.$v['x'].':'.$v['y'].') '.$v['name'].'</option>';
	}
	$arrConstraints['voted_for_planet_id'] = $arrFields['voted_for_planet_id']['null'] ? '<option value="" style="font-style:italic;">-- NULL</option>' : '';
	foreach ( db_select('galaxies g, planets p', 'g.id = p.galaxy_id ORDER BY x ASC, y ASC, z ASC') AS $v ) {
		$arrConstraints['voted_for_planet_id'] .= '<option'.( $v['id'] == $arrRecord['voted_for_planet_id'] ? ' selected="1"' : '' ).' value="'.$v['id'].'">'.$v['id'].'. '.$v['rulername'].' of '.$v['planetname'].' ('.$v['x'].':'.$v['y'].':'.$v['z'].')</option>';
	}
	$arrConstraints['alliance_id'] = $arrFields['alliance_id']['null'] ? '<option value="" style="font-style:italic;">-- NULL</option>' : '';
	foreach ( db_select('alliances', '1 ORDER BY id ASC') AS $v ) {
		$arrConstraints['alliance_id'] .= '<option'.( $v['id'] == $arrRecord['alliance_id'] ? ' selected="1"' : '' ).' value="'.$v['id'].'">'.$v['id'].'. '.$v['name'].'</option>';
	}
}
else if ( 'galaxies' == $_GET['tbl'] ) {
	$fk = db_select('galaxies g, planets p', 'g.id = p.galaxy_id ORDER BY x ASC, y ASC, z ASC');
	$arrConstraints['gc_planet_id'] = $arrFields['gc_planet_id']['null'] ? '<option value="" style="font-style:italic;">-- NULL</option>' : '';
	$arrConstraints['moc_planet_id'] = $arrFields['moc_planet_id']['null'] ? '<option value="" style="font-style:italic;">-- NULL</option>' : '';
	$arrConstraints['mow_planet_id'] = $arrFields['mow_planet_id']['null'] ? '<option value="" style="font-style:italic;">-- NULL</option>' : '';
	$arrConstraints['mof_planet_id'] = $arrFields['mof_planet_id']['null'] ? '<option value="" style="font-style:italic;">-- NULL</option>' : '';
	foreach ( $fk AS $v ) {
		$arrConstraints['gc_planet_id'] .= '<option'.( $v['id'] == $arrRecord['gc_planet_id'] ? ' selected="1"' : '' ).' value="'.$v['id'].'">'.$v['id'].'. '.$v['rulername'].' of '.$v['planetname'].' ('.$v['x'].':'.$v['y'].':'.$v['z'].')</option>';
		$arrConstraints['moc_planet_id'] .= '<option'.( $v['id'] == $arrRecord['moc_planet_id'] ? ' selected="1"' : '' ).' value="'.$v['id'].'">'.$v['id'].'. '.$v['rulername'].' of '.$v['planetname'].' ('.$v['x'].':'.$v['y'].':'.$v['z'].')</option>';
		$arrConstraints['mow_planet_id'] .= '<option'.( $v['id'] == $arrRecord['mow_planet_id'] ? ' selected="1"' : '' ).' value="'.$v['id'].'">'.$v['id'].'. '.$v['rulername'].' of '.$v['planetname'].' ('.$v['x'].':'.$v['y'].':'.$v['z'].')</option>';
		$arrConstraints['mof_planet_id'] .= '<option'.( $v['id'] == $arrRecord['mof_planet_id'] ? ' selected="1"' : '' ).' value="'.$v['id'].'">'.$v['id'].'. '.$v['rulername'].' of '.$v['planetname'].' ('.$v['x'].':'.$v['y'].':'.$v['z'].')</option>';
	}
}

$n = 0;
foreach ( $arrRecord AS $naam => $waarde ) {
	$naam = strtolower($naam);
	if ( isset($notallowed[strtolower($naam)]) ) {
		continue;
	}
	echo '<tr class="bb">';
	echo '<td class="b">'.$naam.'</td>';
	echo '<td>';
	if ( is_null($waarde) ) {
		echo '<i>NULL</i>';
	}
	else if ( 'int' == $arrFields[$naam]['type'] ) {
		echo 10 === $arrFields[$naam]['value'] ? date('d-M-Y H:i:s', $waarde).' ('.$waarde.')' : number_format($waarde, 0, '.', ' ');
	}
	else {
		echo '' === $waarde ? '<br />' : ( 47 < strlen($waarde) ? '<span title="'.htmlspecialchars($waarde).'">'.htmlspecialchars(substr($waarde,0,47)).'...</span>' : htmlspecialchars($waarde) );
	}
	echo '</td>';
	echo '<td>';
	if ( $_GET['pk'] == $naam ) {
		echo '<br />';
	}
	else if ( isset($arrConstraints[$naam]) ) {
		echo '<select onchange="this.setAttribute(\'name\',this.getAttribute(\'seminame\'));" seminame="changes['.$naam.']" style="width:100%;">'.$arrConstraints[$naam].'</select>';
	}
	else if ( 'enum' == $arrFields[$naam]['type'] ) {
		echo '<select onchange="this.setAttribute(\'name\',this.getAttribute(\'seminame\'));" seminame="changes['.$naam.']">';
		if ( $arrFields[$naam]['null'] ) {
			echo '<option value="" style="font-style:italic;">-- NULL</option>';
		}
		foreach ( (array)$arrFields[$naam]['options'] AS $o ) {
			echo '<option'.( (string)$waarde === (string)$o ? ' selected="1"' : '' ).' value="'.$o.'">'.$o.'</option>';
		}
		echo '</select>';
	}
	else if ( 'text' == $arrFields[$naam]['type'] ) {
		echo '<textarea onchange="this.setAttribute(\'name\',this.getAttribute(\'seminame\'));" seminame="changes['.$naam.']" style="width:100%;" rows="5"></textarea>';
	}
	else {
		echo '<input onchange="this.setAttribute(\'name\',this.getAttribute(\'seminame\'));" type="text" seminame="changes['.$naam.']" style="width:100%;">';
	}
	echo '</td>';
	echo '</tr>';
}

?>
<tr class="">
	<td colspan="3" align="center"><input type="submit" value="SAVE" /></td>
</tr>
</table>
</form>
	<?php
}
else
{
	echo "<table border=0 cellpadding=3 cellspacing=0 width=600><tr><td>No records found!</td></tr></table>";
}

?>
</td>
<td>
<form method="post" action="comp.php">
<input type="hidden" name="mode" value="prefs" />
<table border="1" cellpadding="4" cellspacing="0" width="100%">
<?php

$arrTblGamePrefs = db_fetch('EXPLAIN prefs;');
#$t = flip2darray($arrTblGamePrefs);
#asort($t['Field']);
#$arrTblGamePrefs = flip2darray($t);
foreach ( $arrTblGamePrefs AS $v )
{
	$f = $v['Field'];
	$szDisabled = '';
	if ( 'id' == $f || 'tickcount' == $f || 'last_tick' == $f ) {
#		$szDisabled = ' disabled="1"';
#		continue;
	}
	$t = $v['Type'];

	echo '<tr class="bt">';
	echo '	<td width="50">'.$f.'</td>';
	echo '	<td';
	if ( 'id' == $f ) {
		echo '>'.$GAMEPREFS[$f];
	}
	else {
		switch ( strtolower(reset(explode('(', $t))) )
		{
			case 'enum':
				echo ' style="padding-left:0;"><input'.$szDisabled.' name="'.$f.'" type="checkbox" value="1"'.( '1' === $GAMEPREFS[$f] ? ' checked="1"' : '' ).' />';
			break;
			case 'text':
				echo '><textarea'.$szDisabled.' seminame="'.$f.'" rows="6" style="width:100%;" onchange="this.name=this.getAttribute(\'seminame\');">'.htmlspecialchars($GAMEPREFS[$f]).'</textarea>';
			break;
			default:
				echo '><input'.$szDisabled.' seminame="'.$f.'" style="width:100%;" type="text" value="'.$GAMEPREFS[$f].'" onchange="this.name=this.getAttribute(\'seminame\');" />';
			break;
		}
	}
	echo '</td>';
	echo '</tr>';
}

?>
<tr class="bt">
	<td colspan=2><input type=submit value="Opslaan"></td>
</tr>
</table>
</form>

<br />
<br />
<?

if (isset($_GET['reset']) && $_GET['reset']==1024 && isset($_GET['reset_number']) && $_GET['reset_number']==ceil($_GET['reset']/12))
{
?>
<form name=resetgame method=post action=comp.php>
<input type=hidden name=rcheck value=1><input type=hidden name=mode value=reset>
Your Admin inGame MSG:<br>
<textarea name=adminmsg rows=5 style='width:100%'><?=$GAMEPREFS['general_adminmsg']?></textarea><br>
<br>
<br>
<input type=submit value="RESET & SET GAME">

</form>
<?
}
else
{
	echo "<input disabled=\"1\" type=button value=\"!!RESET GAME!!\" onclick=\"document.location='?reset=1024&reset_number=86'\">";
}

?>
<br>
<br>
<a href="multitracker.php">MultiTracker</a>
</td></tr>
</table>
