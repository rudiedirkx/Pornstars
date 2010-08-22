<?php

require_once('inc.config.php');
logincheck();

_header();

?>
<div><b>Planets</b> || <a href="ranking.galaxy.php">Galaxies</a> || <a href="ranking.alliance.php">Alliances</a></div>
<br />

<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>
	<th align="right">#.</th>
	<th nowrap="1">X : Y : Z</th>
	<th>Tag</th>
	<th align="left">Rulername</th>
	<th align="left">Planetname</th>
	<th align="right">Score</th>
	<th align="right">Size</th>
</tr>
<?php

$arrPlanets = db_fetch('
SELECT
	*,
	(SELECT COUNT(1)+1 FROM planets WHERE score > p.score) AS rank,
	(SELECT SUM(asteroids) FROM planet_resources WHERE planet_id = p.id) AS size
FROM
	galaxies g,
	planets p
WHERE
	g.id = p.galaxy_id AND
	0 <= score
ORDER BY
	score DESC,
	p.id ASC
LIMIT 100;
');
foreach ( $arrPlanets AS $arrPlanet ) {
	$szTxtColor = '';
	$szBgColor = (int)$arrPlanet['id'] === PLANET_ID ? ' bgcolor="#221111"' : '';

	echo '<tr'.$szTxtColor.' class="bt"'.$szBgColor.'>';
	echo '	<td align="right">'.$arrPlanet['rank'].'.</td>';
	echo '	<td nowrap="1" align="center"><a'.$szTxtColor.' href="galaxy.php?x='.$arrPlanet['x'].'&y='.$arrPlanet['y'].'">'.$arrPlanet['x'].' : '.$arrPlanet['y'].' : '.$arrPlanet['z'].'</a></td>';
	$arrAlliance = !is_null($arrPlanet['alliance_id']) ? db_select('alliances', 'id = '.(int)$arrPlanet['alliance_id']) : array();
	echo '	<td align="center">'.( $arrAlliance ? $arrAlliance[0]['tag'] : '&nbsp;' ).'</td>';
	echo '	<td><a'.$szTxtColor.' href="communication.php?x='.$arrPlanet['x'].'&y='.$arrPlanet['y'].'&z='.$arrPlanet['z'].'">'.$arrPlanet['rulername'].'</a></td>';
	echo '	<td>'.$arrPlanet['planetname'].'</td>';
	echo '	<td align="right">'.nummertje($arrPlanet['score']).'</td>';
	echo '	<td align="right">'.nummertje($arrPlanet['size']+$arrPlanet['inactive_asteroids']).'</td>';
//	echo '	<td align="right">-</td>';
	echo '</tr>';
}

?>
</table>

<br />

<?php

_footer();

?>