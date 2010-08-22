<?php

require_once('inc.config.php');
logincheck();


$iPlanetsInGalaxy = db_count('planets', 'galaxy_id = '.$g_arrUser['galaxy_id']);
$iVotesNeeded = floor($iPlanetsInGalaxy/2)+1;

$arrGalaxy = db_select('galaxies', 'id = '.$g_arrUser['galaxy_id']);
$arrGalaxy = $arrGalaxy[0];


// Elect MoW
if ( isset($_POST['mow_planet_id']) )
{
	if ( (int)$arrGalaxy['gc_planet_id'] === PLANET_ID ) {
		$iPlanetId = empty($_POST['mow_planet_id']) || !db_count('planets', 'id = '.(int)$_POST['mow_planet_id'].' AND galaxy_id = '.(int)$arrGalaxy['id']) ? 'NULL' : (int)$_POST['mow_planet_id'];
		if ( 'NULL' === $iPlanetId || !db_count('galaxies', 'id = '.(int)$arrGalaxy['id'].' AND (mof_planet_id = '.$iPlanetId.' OR moc_planet_id = '.$iPlanetId.' OR gc_planet_id = '.$iPlanetId.')') ) {
			db_update('galaxies', 'mow_planet_id = '.$iPlanetId, 'id = '.(int)$arrGalaxy['id']);
		}
	}
	Go();
}

// Elect MoC
else if ( isset($_POST['moc_planet_id']) )
{
	if ( (int)$arrGalaxy['gc_planet_id'] === PLANET_ID ) {
		$iPlanetId = empty($_POST['moc_planet_id']) || !db_count('planets', 'id = '.(int)$_POST['moc_planet_id'].' AND galaxy_id = '.(int)$arrGalaxy['id']) ? 'NULL' : (int)$_POST['moc_planet_id'];
		if ( 'NULL' === $iPlanetId || !db_count('galaxies', 'id = '.(int)$arrGalaxy['id'].' AND (mof_planet_id = '.$iPlanetId.' OR mow_planet_id = '.$iPlanetId.' OR gc_planet_id = '.$iPlanetId.')') ) {
			db_update('galaxies', 'moc_planet_id = '.$iPlanetId, 'id = '.(int)$arrGalaxy['id']);
		}
	}
	Go();
}

// Elect MoF
else if ( isset($_POST['mof_planet_id']) )
{
	if ( (int)$arrGalaxy['gc_planet_id'] === PLANET_ID ) {
		$iPlanetId = empty($_POST['mof_planet_id']) || !db_count('planets', 'id = '.(int)$_POST['mof_planet_id'].' AND galaxy_id = '.(int)$arrGalaxy['id']) ? 'NULL' : (int)$_POST['mof_planet_id'];
		if ( 'NULL' === $iPlanetId || !db_count('galaxies', 'id = '.(int)$arrGalaxy['id'].' AND (moc_planet_id = '.$iPlanetId.' OR mow_planet_id = '.$iPlanetId.' OR gc_planet_id = '.$iPlanetId.')') ) {
			db_update('galaxies', 'mof_planet_id = '.$iPlanetId, 'id = '.(int)$arrGalaxy['id']);
		}
	}
	Go();
}

// Update galaxy values
else if ( isset($_POST['name'], $_POST['picture'], $_POST['gc_message']) )
{
	if ( (int)$arrGalaxy['gc_planet_id'] === PLANET_ID ) {
		unset($_POST['id'], $_POST['x'], $_POST['y']);
		db_update('galaxies', $_POST, 'id = '.(int)$arrGalaxy['id']);
	}
	Go();
}

// Vote for GC
else if ( isset($_POST['vote']) )
{
	if ( empty($_POST['vote']) ) {
		db_update('planets', 'voted_for_planet_id = NULL', 'id = '.PLANET_ID);
	}
	else if ( db_count('planets', 'id = '.(int)$_POST['vote'].' AND galaxy_id = '.(int)$arrGalaxy['id']) ) {
		db_update('planets', 'voted_for_planet_id = '.(int)$_POST['vote'], 'id = '.PLANET_ID);
	}
	Make_Gc($arrGalaxy['id']);
	Go();
}

_header();

?>
<div class="header">Galactic Affairs</div>

<br />

<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr class="bb">
	<th>Z</th>
	<th class="left">Ruler & Planet</th>
	<th class="left">Voted on</th>
	<th>Votes</th>
</tr>
<?php

$i = $gc = $mow = $moc = $mof = 0;
$szOptions = '';
$arrOptions = array();
$arrPlanets = db_select('planets', 'galaxy_id = '.$g_arrUser['galaxy_id'].' ORDER BY z ASC');
foreach ( $arrPlanets AS $r )
{
	$iVotesForPlanet = (int)db_count('planets', 'galaxy_id = '.$g_arrUser['galaxy_id'].' AND voted_for_planet_id IS NOT NULL AND voted_for_planet_id = '.$r['id']);

	if ( $r['id'] === $arrGalaxy['gc_planet_id'] ) {
		$szPositionColor = ' style="color:'.$showcolors['gc'].';"';
	}
	else if ( $r['id'] === $arrGalaxy['mow_planet_id'] ) {
		$szPositionColor = ' style="color:'.$showcolors['mow'].';"';
	}
	else if ( $r['id'] === $arrGalaxy['moc_planet_id'] ) {
		$szPositionColor = ' style="color:'.$showcolors['moc'].';"';
	}
	else if ( $r['id'] === $arrGalaxy['mof_planet_id'] ) {
		$szPositionColor = ' style="color:'.$showcolors['mof'].';"';
	}
	else {
		$szPositionColor = '';
	}

	$szVotedOnPlanet = !is_null($r['voted_for_planet_id']) ? db_select_one('planets','CONCAT(rulername,\' of \',planetname)','id = '.(int)$r['voted_for_planet_id']) : '-';

	echo '<tr class="bt">';
	echo '<th>'.$r['z'].'</th>';
	echo '<td'.$szPositionColor.'>'.$r['rulername'].' of '.$r['planetname'].'</td>';
	echo '<td>'.$szVotedOnPlanet.'</td>';
	echo '<td class="c">'.$iVotesForPlanet.'</td>';
	echo '</tr>';

	$arrOptions[(int)$r['id']] = $r['rulername'].' of '.$r['planetname'];
	$szOptions .= '<option value="'.$r['id'].'">'.$r['rulername'].' of '.$r['planetname'].'</option>';
}

?>
</table>

<br />

<center>
<form method="post" action="">
<select name="vote" style="width:300px;">
<option value="">Choose a GC</option>
<option value="">--</option>
<?php
foreach ( $arrOptions AS $iPlanet => $szPlanet ) {
	echo '<option'.( $iPlanet === (int)$g_arrUser['voted_for_planet_id'] ? ' selected="1"' : '' ).' value="'.$iPlanet.'">'.$szPlanet.'</option>';
}
?>
</select>
<input type="submit" value="Vote" style="width:100px;">
</form>

<?php

echo 'There are '.$iPlanetsInGalaxy.' users in this galaxy<br />';
echo 'You need '.$iVotesNeeded.' votes to become Galaxy Commander<br />';
echo '<br />';

if ( !is_null($arrGalaxy['gc_planet_id']) && 1 == count($gc=db_select('planets', 'id = '.$arrGalaxy['gc_planet_id'].' AND galaxy_id = '.$arrGalaxy['id'])) ) {
	$gc = $gc[0];
	echo 'Your Galaxy Commander is <b style="color:'.$showcolors['gc'].';">'.$gc['rulername'].' of '.$gc['planetname'].'</b><br />';

	if ( !is_null($arrGalaxy['mow_planet_id']) && 1 == count($mow=db_select('planets', 'id = '.$arrGalaxy['mow_planet_id'].' AND galaxy_id = '.$arrGalaxy['id'])) ) {
		$mow = $mow[0];
		echo 'Your Minister of War is <b style="color:'.$showcolors['mow'].';">'.$mow['rulername'].' of '.$mow['planetname'].'</b><br />';
	}

	if ( !is_null($arrGalaxy['moc_planet_id']) && 1 == count($moc=db_select('planets', 'id = '.$arrGalaxy['moc_planet_id'].' AND galaxy_id = '.$arrGalaxy['id'])) ) {
		$moc = $moc[0];
		echo 'Your Minister of Communication is <b style="color:'.$showcolors['moc'].';">'.$moc['rulername'].' of '.$moc['planetname'].'</b><br />';
	}

	if ( !is_null($arrGalaxy['mof_planet_id']) && 1 == count($mof=db_select('planets', 'id = '.$arrGalaxy['mof_planet_id'].' AND galaxy_id = '.$arrGalaxy['id'])) ) {
		$mof = $mof[0];
		echo 'Your Minister of Finance is <b style="color:'.$showcolors['mof'].';">'.$mof['rulername'].' of '.$mof['planetname'].'</b><br />';
	}
}
else {
	echo 'Your galaxy has no Galaxy Commander!<br />';
}


if ( !is_null($arrGalaxy['gc_planet_id']) && PLANET_ID === (int)$arrGalaxy['gc_planet_id'] )
{
	?>
<br />
<hr>
<br />
<b>You are the GC</b><br />
<br />

<form method="post" action="">
Select your <font color="<?php echo $showcolors['moc']; ?>">Minister of COMMUNICATION</font>:<br />
<select name="moc_planet_id" style="width:300px;">
<option value="">Choose your MoC</option>
<option value="">--</option>
<?php
foreach ( $arrOptions AS $iPlanet => $szPlanet ) {
	if ( (int)$iPlanet !== PLANET_ID ) { echo '<option'.( $iPlanet === (int)$arrGalaxy['moc_planet_id'] ? ' selected="1"' : '' ).' value="'.$iPlanet.'">'.$szPlanet.'</option>'; }
}
?>
</select>
<input type="submit" value="Elect MoC" style="width:100px;">
</form>

<br />

<form method="post" action="">
Select your <font color="<?php echo $showcolors['mow']; ?>">Minister of WAR</font>:<br />
<select name="mow_planet_id" style="width:300px;">
<option value="">Choose your MoW</option>
<option value="">--</option>
<?php
foreach ( $arrOptions AS $iPlanet => $szPlanet ) {
	if ( (int)$iPlanet !== PLANET_ID ) { echo '<option'.( $iPlanet === (int)$arrGalaxy['mow_planet_id'] ? ' selected="1"' : '' ).' value="'.$iPlanet.'">'.$szPlanet.'</option>'; }
}
?>
</select>
<input type="submit" value="Elect MoW" style="width:100px;">
</form>

<br />

<form method="post" action="">
Select your <font color="<?php echo $showcolors['mof']; ?>">Minister of FINANCE</font>:<br />
<select name="mof_planet_id" style="width:300px;">
<option value="">Choose your MoF</option>
<option value="">--</option>
<?php
foreach ( $arrOptions AS $iPlanet => $szPlanet ) {
	if ( (int)$iPlanet !== PLANET_ID ) { echo '<option'.( $iPlanet === (int)$arrGalaxy['mof_planet_id'] ? ' selected="1"' : '' ).' value="'.$iPlanet.'">'.$szPlanet.'</option>'; }
}
?>
</select>
<input type="submit" value="Elect MoF" style="width:100px;">
</form>

<br />

<form method="post" action="">
Enter URL for galaxy picture<br />
<input type=text name="picture" value="<?php echo $arrGalaxy['picture']?>" style="width:400px;" maxlength="255" /><br />
<br />
Enter galaxy name:<br />
<input type=text name="name" value="<?php echo $arrGalaxy['name']?>" style="width:400px;" maxlength="40" /><br />
<br />
Enter GC-Message:<br />
<textarea name="gc_message" rows="5" style="width:400px;"><?php echo $arrGalaxy['gc_message']; ?></textarea><br />
<br />
<input type="submit" value="Save" style="width:100px;" />
</form>
	<?
}

echo '</center>';

_footer();

?>