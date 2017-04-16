<?php

use rdx\ps\Galaxy;

require 'inc.bootstrap.php';

logincheck();

_header();

$x = @$_GET['x'] ?: $g_user->galaxy->x;
$y = @$_GET['y'] ?: $g_user->galaxy->y;

$g_user->galaxy->ass = 234;
$galaxy = $g_user->galaxy->coordinates == [$x, $y] ? $g_user->galaxy : Galaxy::fromCoordinates($x, $y);

?>
<h1>Galaxy</h1>

<form method="get" action>
	<input type="number" name="x" value="<?= $x ?>" />
	<input type="number" name="y" value="<?= $y ?>" />
	<button>Visit</button>
</form>
<?php

if ( !$galaxy ) {
	echo '<p>There is no galaxy at these coordinates!</p>';
	return;
}

$score = array_reduce($galaxy->planets, function($total, $planet) {
	return $total + $planet->score;
}, 0);

$size = array_reduce($galaxy->planets, function($total, $planet) {
	return $total + $planet->total_asteroids;
}, 0);

?>
<h2><?= html($galaxy->name) ?></h2>

<table>
	<tr>
		<td colspan="7">
			Score: <?= nummertje($score) ?>
			&nbsp;
			Size: <?= nummertje($size) ?>
		</td>
	</tr>
	<tr>
		<th>Tag</th>
		<th>Z</th>
		<th>Ruler</th>
		<th>Planet</th>
		<th>Score</th>
		<th>Size</th>
		<th></th>
	</tr>
	<? foreach ( $galaxy->planets as $planet ):
		$roles = implode(' ', $planet->galaxy_roles);
		?>
		<tr title="<?= $roles ?>">
			<td><?= html($planet->alliace_tag) ?></td>
			<td><?= $planet->z ?></td>
			<td class="<?= $roles ?>"><?= html($planet->rulername) ?></td>
			<td class="<?= $roles ?>"><?= html($planet->planetname) ?></td>
			<td><?= nummertje($planet->score) ?></td>
			<td><?= nummertje($planet->total_asteroids) ?></td>
			<td><a href="waves.php?x=<?= $x ?>&y=<?= $y ?>&z=<?= $planet->z ?>">scan</a></td>
		</tr>
	<? endforeach ?>
</table>

<?php

_footer();
