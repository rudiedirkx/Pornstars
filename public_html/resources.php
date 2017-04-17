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

// Initiate asteroids
if ( isset($_POST['init_roids']) && is_array($_POST['init_roids']) ) {
	validTokenOrFail('asteroids');

	// @todo Check costs
	// @todo Pay costs
	// @todo Subtract inactive asteroids

	foreach ( $_POST['init_roids'] as $rid => $amount ) {
		$db->update('planet_resources', 'asteroids = asteroids + ' . (int) $amount, [
			'planet_id' => $g_user->id,
			'resource_id' => $rid,
		]);
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
			<th>Initiate</th>
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
						<input type="number" name="init_roids[<?= $resource->id ?>]" />
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
			<td><button>Initiate</button></td>
			<td colspan="4">
				Activating the next asteroid will cost ?,???
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
