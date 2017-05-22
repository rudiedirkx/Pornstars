<?php

require 'inc.bootstrap.php';

logincheck();

// @todo Donate to galaxy fund
// @todo MoF: Donate from galaxy fund to planet

// Order units
if ( isset($_POST['order_units'], $_POST['_token']) ) {
	validTokenOrFail('production');

	addProductions($_POST['order_units'], 'power');

	return do_redirect();
}

// Activate asteroids
if ( isset($_POST['activate_asteroids']) && is_array($_POST['activate_asteroids']) ) {
	validTokenOrFail('asteroids');

	$activate = array_intersect_key($_POST['activate_asteroids'], $g_user->resources);
	$activate = array_filter($activate, 'isint');

	$activated = 0;
	foreach ( $activate as $rid => $amount ) {
		$amount = min($g_user->maxPowerForAsteroids(), $amount);
		if ( $amount == 0 ) continue;

		$costs = $g_user->activateAsteroidsCosts($amount);

		$g_user->takeTransaction(function($g_user) use ($db, &$activated, $rid, $costs, $amount) {
			// Take resources
			$g_user->takeResources([$g_user->power_resource->id => $costs]);

			// Take inactive asteroids
			$g_user->takeProperties(['inactive_asteroids' => $amount]);

			// Add active asteroids
			$db->update('planet_resources', 'asteroids = asteroids + ' . (int) $amount, [
				'planet_id' => $g_user->id,
				'resource_id' => $rid,
			]);

			$activated += $amount;
		}, false);
	}

	sessionSuccess('Activated ' . nummertje($activated) . ' asteroids');
	return do_redirect();
}

_header();

?>
<h1>Resources</h1>

<form method="post" action>
	<input type="hidden" name="_token" value="<?= createToken('asteroids') ?>" />
	<table>
		<tr>
			<th colspan="3">Asteroids</th>
			<th colspan="4">Income per tick</th>
		</tr>
		<tr>
			<th>Type</th>
			<th>Amount</th>
			<th>Activate</th>
			<th>Planet-</th>
			<th>Asteroid-</th>
			<th>Bonus-</th>
			<th>Total</th>
		</tr>
		<? foreach ( $g_user->resources as $resource ): ?>
			<tr>
				<td><?= html($resource->resource) ?></td>
				<td>
					<? if ( !$resource->is_power ): ?>
						<?= nummertje($resource->asteroids) ?>
					<? endif ?>
				</td>
				<td>
					<? if ( !$resource->is_power ): ?>
						<input type="number" name="activate_asteroids[<?= $resource->id ?>]" />
					<? endif ?>
				</td>
				<td>?</td>
				<td>
					<? if ( !$resource->is_power ): ?>
						?
					<? endif ?>
				</td>
				<td>?</td>
				<td><?= nummertje($g_user->ticker->getIncome($resource)) ?></td>
			</tr>
		<? endforeach ?>
		<tr>
			<td>Inactive</td>
			<td><?= nummertje($g_user->inactive_asteroids) ?></td>
			<td><button>Activate</button></td>
			<td colspan="4">
				The next 'roid will cost <?= nummertje($g_user->next_asteroid_costs) ?> power.
				You can activate <?= nummertje($g_user->maxPowerForAsteroids()) ?>.
			</td>
		</tr>
	</table>
</form>

<h2>Power</h2>

<?= getProductionForm('power') ?>
<br />

<h2>Production progress</h2>
<?= getProductionList('power') ?>
<br />

<h2>Galaxy fund</h2>

@todo Donate to fund
@todo MoF: Donate from galaxy fund to planet

<?php

_footer();
