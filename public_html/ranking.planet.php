<?php

use rdx\ps\Planet;

require 'inc.bootstrap.php';

logincheck();

_header();

$planets = Planet::all('1 ORDER BY score DESC');

?>
<h1>Ranking - planets</h1>

<div><b>Planets</b> || <a href="ranking.galaxy.php">Galaxies</a> || <a href="ranking.alliance.php">Alliances</a></div>
<br />

<table>
	<tr>
		<th>#</th>
		<th nowrap>X : Y : Z</th>
		<th>Tag</th>
		<th>Rulername</th>
		<th>Planetname</th>
		<th>Score</th>
		<th>Size</th>
	</tr>
	<? foreach ( $planets as $planet ): ?>
		<tr>
			<td>#</td>
			<td nowrap><a href="galaxy.php?x=<?= $planet->x ?>&y=<?= $planet->y ?>"><?= $planet->x ?> : <?= $planet->y ?></a> : <?= $planet->z ?></td>
			<td><?= html($planet->alliance_tag) ?></td>
			<td><?= html($planet->rulername) ?></td>
			<td><?= html($planet->planetname) ?></td>
			<td><?= nummertje($planet->score) ?></td>
			<td><?= nummertje($planet->total_asteroids) ?></td>
		</tr>
	<? endforeach ?>
</table>

<?php

_footer();
