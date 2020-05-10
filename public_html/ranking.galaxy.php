<?php

use rdx\ps\Galaxy;

require 'inc.bootstrap.php';

logincheck();

_header();

$galaxies = Galaxy::all('1 ORDER BY (SELECT SUM(score) FROM planets WHERE galaxy_id = galaxies.id) DESC');

?>
<h1>Ranking - galaxies</h1>

<div><a href="ranking.planet.php">Planets</a> || <b>Galaxies</b> || <a href="ranking.alliance.php">Alliances</a></div>
<br />

<table>
	<tr>
		<th align="right">#</th>
		<th>X&nbsp;:&nbsp;Y</th>
		<th>Name</th>
		<th>Score</th>
		<th>Size</th>
		<th># planets</th>
	</tr>
	<? $n = 0; foreach ( $galaxies as $galaxy ): ?>
		<tr>
			<td align="right"><?= ++$n ?></td>
			<td><a href="galaxy.php?x=<?= $galaxy->x ?>&y=<?= $galaxy->y ?>"><?= implode('&nbsp;:&nbsp;', $galaxy->coordinates) ?></a></td>
			<td><?= html($galaxy->name) ?></td>
			<td><?= nummertje($galaxy->score) ?></td>
			<td><?= nummertje($galaxy->total_asteroids) ?></td>
			<td><?= count($galaxy->planets) ?></td>
		</tr>
	<? endforeach ?>
</table>

<?php

_footer();
