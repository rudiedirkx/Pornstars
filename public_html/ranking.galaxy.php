<?php

require_once('inc.config.php');
logincheck();

_header();

?>
<div><a href="ranking.planet.php">Planets</a> || <b>Galaxies</b> || <a href="ranking.alliance.php">Alliances</a></div>
<br />

<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>
	<th align="right">#.</th>
	<th>X&nbsp;:&nbsp;Y</th>
	<th align="left">Name</th>
	<th align="right">Score</th>
	<th align="right">Size</th>
	<th align="right"># planets</th>
</tr>
<?php

$arrGalaxies = db_fetch('
SELECT
	*,
	(SELECT SUM(score) FROM planets WHERE galaxy_id = g.id) AS score,
	(SELECT sum(asteroids) FROM planet_resources pr, planets p WHERE p.id = pr.planet_id AND galaxy_id = g.id)+(SELECT SUM(inactive_asteroids) FROM planets WHERE galaxy_id = g.id) AS size,
	(SELECT COUNT(1) FROM planets WHERE galaxy_id = g.id) AS num_planets
FROM
	galaxies g
ORDER BY
	score DESC,
	size DESC,
	x ASC,
	y ASC');
$r = 0;
foreach ( $arrGalaxies AS $arrGal )
{
	$szBgColor = $arrGal['x'] == $g_arrUser['x'] && $arrGal['y'] == $g_arrUser['y'] ? ' bgcolor="#221111"' : '';
	echo '<tr class="bt"'.$szBgColor.'>';
	echo '<td class="right">'.++$r.'</td>';
	echo '<td class="c"><a href="galaxy.php?x='.$arrGal['x'].'&y='.$arrGal['y'].'">'.$arrGal['x'].'&nbsp;:&nbsp;'.$arrGal['y'].'</a></td>';
	echo '<td><a href="galaxy.php?x='.$arrGal['x'].'&y='.$arrGal['y'].'">'.$arrGal['name'].'</a></td>';
	echo '<td class="right">'.nummertje($arrGal['score']).'</td>';
	echo '<td class="right">'.nummertje($arrGal['size']).'</td>';
	echo '<td class="right">'.$arrGal['num_planets'].'</td>';
	echo '</tr>';
}

?>
</table>

<br />

<?php

_footer();

?>