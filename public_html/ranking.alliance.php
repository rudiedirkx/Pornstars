<?php

use rdx\ps\Alliance;

require 'inc.bootstrap.php';

logincheck();

_header();

$alliances = Alliance::all('1 ORDER BY (SELECT SUM(score) FROM planets WHERE alliance_id = alliances.id) DESC');

?>
<h1>Ranking - alliances</h1>

<div><a href="ranking.planet.php">Planets</a> || <a href="ranking.galaxy.php">Galaxies</a> || <b>Alliances</b></div>
<br />

<table>
	<tr>
		<th>#</th>
		<th>Tag</th>
		<th>Name</th>
		<th>Score</th>
		<th>Size</th>
		<th># planets</th>
	</tr>
	<? foreach ( $alliances as $alliance ): ?>
		<tr>
			<td>#</td>
			<td><?= html($alliance->tag) ?></td>
			<td><a href="alliance.php?id=<?= $alliance->id ?>"><?= html($alliance->name) ?></a></td>
			<td><?= nummertje($alliance->score) ?></td>
			<td><?= nummertje($alliance->total_asteroids) ?></td>
			<td><?= count($alliance->planets) ?></td>
		</tr>
	<? endforeach ?>
</table>

<?php

_footer();
