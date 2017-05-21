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

	// Check inactive asteroids
	$totalActivate = array_sum($_POST['activate_asteroids']);
	if ( $totalActivate > $g_user->inactive_asteroids ) {
		sessionError('Not enough inactive asteroids');
		return do_redirect();
	}

	// Check resources
	$totalCosts = $g_user->activateAsteroidsCosts($totalActivate);
	if ( $totalCosts > $g_user->power_amount ) {
		sessionError('Not enough power on planet');
		return do_redirect();
	}

	try {
		$g_user->takeTransaction(function($g_user) use ($totalCosts, $totalActivate) {
			// Take resources
			$g_user->takeResources([$g_user->power_resource->id => $totalCosts]);

			// Take inactive asteroids
			$g_user->takeProperties(['inactive_asteroids' => $totalActivate]);
		});
	}
	catch ( NotEnoughException $ex ) {
		sessionError('Not enough: ' . $ex->getMessage());
		return do_redirect();
	}

	// Add active asteroids
	foreach ( $_POST['activate_asteroids'] as $rid => $amount ) {
		if ( isset($g_user->resources[$rid]) ) {
			$db->update('planet_resources', 'asteroids = asteroids + ' . (int) $amount, [
				'planet_id' => $g_user->id,
				'resource_id' => $rid,
			]);
		}
	}

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
				You can activate <?= nummertje($g_user->power_to_activate_asteroids) ?>.
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
