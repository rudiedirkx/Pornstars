<?php

require_once('inc.config.php');
logincheck();


_header();


$arrGalaxy = db_select('galaxies', 'id = '.(int)$g_arrUser['galaxy_id']);

$iXCoord = isset($_GET['x']) ? (int)$_GET['x'] : (int)$arrGalaxy[0]['x'];
$iYCoord = isset($_GET['y']) ? (int)$_GET['y'] : (int)$arrGalaxy[0]['y'];

if ( $iXCoord != (int)$arrGalaxy[0]['x'] || $iYCoord != (int)$arrGalaxy[0]['y'] ) {
	$arrGalaxy = db_select('galaxies', 'x = '.$iXCoord.' AND y = '.$iYCoord);
}

$szPrevGalaxy = db_select_one('galaxies', 'concat(\'x=\',x,\'&y=\',y)', '((x = '.$iXCoord.' AND y < '.$iYCoord.') OR (x < '.$iXCoord.')) ORDER BY x DESC, y DESC');
$szNextGalaxy = db_select_one('galaxies', 'concat(\'x=\',x,\'&y=\',y)', '((x = '.$iXCoord.' AND y > '.$iYCoord.') OR (x > '.$iXCoord.')) ORDER BY x ASC, y ASC');

?>
<center>
<form method="get" action="?">
	<input type="button" value="&#171;&#171;" style="width:40;" <?php echo $szPrevGalaxy ? 'onclick="location=\'?'.$szPrevGalaxy.'\';"' : 'disabled="1"'; ?> />
	<input type="text" name="x" class="c" maxlength="3" value="<?php echo $iXCoord; ?>" style="width:50;" onclick="this.select();" />
	<input type="text" name="y" class="c" maxlength="3" value="<?php echo $iYCoord; ?>" style="width:50;" onclick="this.select();" />
	<input type="button" value="&#187;&#187;" style="width:40;" <?php echo $szNextGalaxy ? 'onclick="location=\'?'.$szNextGalaxy.'\';"' : 'disabled="1"'; ?> /><br />
	<input type="submit" value="visit" style="width:50;" />
</form>
<?php

if ( $arrGalaxy ) {
	$arrGalaxy = $arrGalaxy[0];

	$szTotalScore = nummertje(db_select_one('planets','sum(score)','galaxy_id = '.(int)$arrGalaxy['id']));
	$szTotalSize = nummertje(db_select_one('planets','sum(metal_asteroids+crystal_asteroids+uninitiated_asteroids)','galaxy_id = '.(int)$arrGalaxy['id']));

	?>
<table border="0" cellpadding="4" cellspacing="0" width="600" align="center">
<tr>
	<th colspan="7"><img src="<?php echo $arrGalaxy['picture'] ? $arrGalaxy['picture'] : 'images/death.jpg'; ?>" height="150" width="300" border="0" align="center" /></th>
</tr>
<tr>
	<td colspan="7" class="c"><b><?php echo $arrGalaxy['name']; ?></b> (<?php echo $arrGalaxy['x'].':'.$arrGalaxy['y']; ?>) - score: <b><?php echo $szTotalScore; ?></b>; size: <b><?php echo $szTotalSize; ?></b></td>
</tr>
<tr>
	<th>Tag</th>
	<th>Z</th>
	<th class="left">Ruler</th>
	<th class="left">Planet</th>
	<th class="right">Score</th>
	<th class="right">Size</th>
	<th><br /></th>
</tr>
<?php

$arrPlanets = db_fetch('SELECT *, (SELECT SUM(asteroids) FROM planet_resources WHERE planet_id = p.id) AS size FROM planets p WHERE p.galaxy_id = '.(int)$arrGalaxy['id'].' ORDER BY p.z ASC');

foreach ( $arrPlanets AS $arrPlanet ) {
	if ( (int)$arrGalaxy['gc_planet_id'] === (int)$arrPlanet['id'] ) {
		$szTxtColor = ' style="color:'.$showcolors['gc'].';"';
	}
	else if ( (int)$arrGalaxy['moc_planet_id'] === (int)$arrPlanet['id'] ) {
		$szTxtColor = ' style="color:'.$showcolors['moc'].';"';
	}
	else if ( (int)$arrGalaxy['mow_planet_id'] === (int)$arrPlanet['id'] ) {
		$szTxtColor = ' style="color:'.$showcolors['mow'].';"';
	}
	else if ( (int)$arrGalaxy['mof_planet_id'] === (int)$arrPlanet['id'] ) {
		$szTxtColor = ' style="color:'.$showcolors['mof'].';"';
	}
	else {
		$szTxtColor = '';
	}

	$szBgColor = (int)$arrPlanet['id'] === PLANET_ID ? ' bgcolor="#221111"' : '';

	echo '<tr'.$szBgColor.$szTxtColor.' class="bt">';
	echo '<th>'.( $arrPlanet['alliance_id'] ? db_select_one('alliances','tag','id = '.(int)$arrPlanet['alliance_id']) : '&nbsp;' ).'</th>';
	echo '<th>'.$arrPlanet['z'].'</th>';
	echo '<td><a'.$szTxtColor.' href="communication.php?x='.$arrGalaxy['x'].'&y='.$arrGalaxy['y'].'&z='.$arrPlanet['z'].'">'.$arrPlanet['rulername'].'</a></td>';
	echo '<td>'.$arrPlanet['planetname'].'</td>';
	echo '<td align="right">'.nummertje($arrPlanet['score']).'</td>';
	echo '<td align="right">'.nummertje($arrPlanet['size']+$arrPlanet['inactive_asteroids']).'</td>';
	echo '<td align="right"><a href="waves.php?x='.$arrGalaxy['x'].'&y='.$arrGalaxy['y'].'&z='.$arrPlanet['z'].'">scan</a></td>';
	echo '</tr>';
}

?>
</table>

	<?php
}
else {
	echo '<br />There is no galaxy at these coordinates!<br />';
}

?>

<br />
<br />

<table border=0 cellpadding=3 cellspacing=0>
<tr style="color:<?php echo $showcolors['gc']; ?>;">
	<td>This color</td>
	<td>=</td>
	<td>Galaxy Commander</td>
</tr>
<tr style="color:<?php echo $showcolors['mow']; ?>;">
	<td>This color</td>
	<td>=</td>
	<td>Minister of War</td>
</tr>
<tr style="color:<?php echo $showcolors['moc']; ?>;">
	<td>This color</td>
	<td>=</td>
	<td>Minister of Communication</td>
</tr>
<tr style="color:<?php echo $showcolors['mof']; ?>;">
	<td>This color</td>
	<td>=</td>
	<td>Minister of Finance</td>
</tr>
</table>

<br />

<?php

_footer();

?>