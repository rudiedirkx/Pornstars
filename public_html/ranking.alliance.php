<?php

require_once('inc.config.php');
logincheck();

_header();

?>
<div><a href="ranking.planet.php">Planets</a> || <a href="ranking.galaxy.php">Galaxies</a> || <b>Alliances</b></div>
<br />

<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>
	<th align="right">#.</th>
	<th>Tag</th>
	<th align="left">Name</th>
	<th align="right">Score</th>
	<th align="right">Size</th>
	<th align="right"># planets</th>
</tr>
<?php

$arrAlliances = db_fetch('SELECT a.*, COUNT(1) AS num_planets, SUM(p.score) AS score, SUM(p.metal_asteroids+p.crystal_asteroids+p.energy_asteroids+p.uninitiated_asteroids) AS size FROM alliances a, planets p WHERE a.id = p.alliance_id GROUP BY a.id ORDER BY SUM(p.score) DESC');
$r = 0;
foreach ( $arrAlliances AS $arrAlliance )
{
	$szBgColor = $arrAlliance['id'] === $g_arrUser['alliance_id'] ? ' bgcolor="#221111"' : '';
	echo '<tr class="bt"'.$szBgColor.'>';
	echo '<td class="right">'.++$r.'</td>';
	echo '<td class="b c">'.$arrAlliance['tag'].'</td>';
	echo '<td>'.$arrAlliance['name'].'</td>';
	echo '<td class="right">'.nummertje($arrAlliance['score']).'</td>';
	echo '<td class="right">'.nummertje($arrAlliance['size']).'</td>';
	echo '<td class="right">'.nummertje($arrAlliance['num_planets']).'</td>';
	echo '</tr>';
}

?>
</table>

<br />

<?php

_footer();

?>