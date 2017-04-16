<?php

require 'inc.bootstrap.php';

logincheck();

$galaxy = $g_user->galaxy;

$iPlanetsInGalaxy = count($galaxy->planets);
$iVotesNeeded = max(2, floor($iPlanetsInGalaxy / 2) + 1);

$ministers = array_reduce($galaxy->planets, function($list, $planet) use ($galaxy) {
	return $galaxy->gc_planet_id == $planet->id ? $list : $list + [$planet->id => (string) $planet];
}, []);

// Update galaxy
if ( isset($_POST['galaxy_name'], $_POST['galaxy_message'], $_POST['mow'], $_POST['moc'], $_POST['mof']) ) {
	$minister_or_nil = function($id) use ($ministers) {
		return isset($ministers[(int) $id]) ? (int) $id : null;
	};

	$galaxy->update([
		'name' => trim($_POST['galaxy_name']),
		'gc_message' => trim($_POST['galaxy_message']),
		'mow_planet_id' => $minister_or_nil($_POST['mow']),
		'moc_planet_id' => $minister_or_nil($_POST['moc']),
		'mof_planet_id' => $minister_or_nil($_POST['mof']),
	]);

	return do_redirect();
}

// Vote for GC
else if ( isset($_POST['vote']) ) {
	$planets = $galaxy->planets;
	if ( isset($planets[ $_POST['vote'] ]) ) {
		$g_user->update(['voted_for_planet_id' => $_POST['vote']]);

		$gc = 0;
		foreach ( $planets as $planet ) {
			if ( $planet->votes_for_gc >= $iVotesNeeded ) {
				$gc = $planet->id;
				break;
			}
		}

		if ( $gc != $galaxy->gc_planet_id ) {
			$oldGC = $galaxy->gc;

			$galaxy->update([
				'gc_planet_id' => $gc ?: null,
				'moc_planet_id' => null,
				'mow_planet_id' => null,
				'mof_planet_id' => null,
			]);

			// @todo Galactic news for GC change

			if ( $gc ) {
				sessionSuccess("Your vote changed the GC to <em>" . html($planet) . "</em>!");
			}
			else {
				sessionWarning("Your vote removed <em>" . html($oldGC) . "</em> as GC!");
			}
		}
	}

	return do_redirect();
}

_header();

?>
<h1>Galactic Affairs</h1>

<form method="post" action>
	<table>
		<tr>
			<th width="10">Z</th>
			<th>Ruler &amp; Planet</th>
			<th>Votes</th>
			<th>Your vote</th>
		</tr>
		<? foreach ( $galaxy->planets as $planet ): ?>
			<tr>
				<td width="10"><?= $planet->z ?></td>
				<td class="<?= implode(' ', $planet->galaxy_roles) ?>"><?= $planet ?></td>
				<td><?= $planet->votes_for_gc ?></td>
				<td><input type="radio" name="vote" value="<?= $planet->id ?>" <?= $planet->id == $g_user->voted_for_planet_id ? 'checked' : '' ?> /></td>
			</tr>
		<? endforeach ?>
	</table>

	<p><button>Vote for GC</button></p>
</form>

<p>There are <?= $iPlanetsInGalaxy ?> users in this galaxy.</p>
<p>You need <?= $iVotesNeeded ?> votes to become Galaxy Commander.</p>

<? if ( $galaxy->gc ): ?>
	<p class="gc">Your Galactic Commander is <em><?= html($galaxy->gc) ?></em>.</p>
<? else: ?>
	<p class="gc">Your galaxy doesn't have a Galactic Commander!</p>
<? endif ?>

<? if ( $galaxy->mow ): ?>
	<p class="mow">Your Minister of War is <em><?= html($galaxy->mow) ?></em>.</p>
<? endif ?>

<? if ( $galaxy->moc ): ?>
	<p class="moc">Your Minister of Communication is <em><?= html($galaxy->moc) ?></em>.</p>
<? endif ?>

<? if ( $galaxy->mof ): ?>
	<p class="mof">Your Minister of Finance is <em><?= html($galaxy->mof) ?></em>.</p>
<? endif ?>

<? if ( $galaxy->gc_planet_id == $g_user->id ): ?>
	<h2>You are the GC:</h2>

	<form method="post" action>
		<table>
			<tr>
				<th>Galaxy name</th>
				<td><input name="galaxy_name" value="<?= html($galaxy->name) ?>" /></td>
			</tr>
			<tr>
				<th>Galactic message</th>
				<td><textarea name="galaxy_message" rows="6" cols="60"><?= html($galaxy->gc_message) ?></textarea></td>
			</tr>
			<tr>
				<th class="mow">Minister of War</th>
				<td><select name="mow"><?= html_options($ministers, $galaxy->mow_planet_id, '--') ?></select></td>
			</tr>
			<tr>
				<th class="moc">Minister of Communication</th>
				<td><select name="moc"><?= html_options($ministers, $galaxy->moc_planet_id, '--') ?></select></td>
			</tr>
			<tr>
				<th class="mof">Minister of Finance</th>
				<td><select name="mof"><?= html_options($ministers, $galaxy->mof_planet_id, '--') ?></select></td>
			</tr>
			<tr>
				<td colspan="2"><button>Save</button></td>
			</tr>
		</table>
	</form>

<? endif ?>

<?php

_footer();
