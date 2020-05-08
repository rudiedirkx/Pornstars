<?php

require 'inc.bootstrap.php';

logincheck();

$iNumMovingRows = 3;

$fleetsAtHome = array_filter($g_user->fleets, function($fleet) {
	return $fleet->is_home;
});

// MOVE SHIPS //
if ( isset($_POST['move']) ) {
	foreach ( $_POST['move'] as $move ) {
		if (empty($move['unit']) || !isint($move['from']) || !isint($move['to'])) {
			continue;
		}

		if ( !isset($fleetsAtHome[ $move['from'] ]) || !isset($fleetsAtHome[ $move['to'] ]) ) {
			continue;
		}

		$ships = $move['unit'] == 'all' ? array_keys($g_user->ships) : [ $move['unit'] ];
		foreach ( $ships as $sid ) {
			$fleetsAtHome[ $move['from'] ]->moveShips($sid, $move['amount'], $fleetsAtHome[ $move['to'] ]);
		}
	}

	return do_redirect();
}

// FLEET ACTION //
else if ( isset($_POST['mission']) ) {
	$missions = array_filter($_POST['mission'], function($mission) {
		return !empty($mission['action']);
	});

	try {
		$g_user->takeTransaction(function($g_user) use ($missions) {
			$messages = [];
			foreach ( $missions as $fleetname => $mission ) {
				if ( !isset($g_user->fleets[$fleetname]) ) {
					continue;
				}
				$fleet = $g_user->fleets[$fleetname];

				if ( !$fleet->available_actions || !isset($fleet->available_actions[ $mission['action'] ]) ) {
					continue;
				}

				$action = 'validate' . $mission['action'];
				call_user_func_array([$fleet, $action], [&$mission]);

				$action = 'execute' . $mission['action'];
				$messages[] = call_user_func([$fleet, $action], $mission);
			}

			if ( count($messages) ) {
				sessionSuccess(implode('<br>', $messages));
			}
		});
	}
	catch ( FleetMissionException $ex ) {
		sessionError('Invalid mission for [' . $ex->getFleet() . ']: ' . $ex->getMessage());
	}
	catch ( NotEnoughException $ex ) {
		sessionError('Not enough: ' . $ex->getMessage());
	}

	return do_redirect();
}

_header();

?>
<h1>Military</h1>

<?= getFleetMatrix($g_user, true) ?>

<h2>Fleet Management</h2>

<form method="post" action autocomplete="off">
	<table>
		<tr class="b">
			<td>Unit</td>
			<td>Amount</td>
			<td>From</td>
			<td>To</td>
		</tr>
		<? for ( $i = 0; $i < $iNumMovingRows; $i++ ): ?>
			<tr>
				<td>
					<select name="move[<?= $i ?>][unit]">
						<option value="">-- Unit</option>
						<option value="all">-- All units</option>
						<?= html_options($g_user->ships) ?>
					</select>
				</td>
				<td>
					<input type="number" name="move[<?= $i ?>][amount]" />
				</td>
				<td>
					<select name="move[<?= $i ?>][from]">
						<?= html_options($fleetsAtHome, '', '-- From') ?>
					</select>
				</td>
				<td>
					<select name="move[<?= $i ?>][to]">
						<?= html_options($fleetsAtHome, '', '-- To') ?>
					</select>
				</td>
			</tr>
		<? endfor ?>
		<tr>
			<td colspan="4">
				<button>Move Units</button>
			</td>
		</tr>
	</table>
</form>

<h2>Missions</h2>

<form method="post" action>
	<table>
		<? foreach ( $g_user->fleets as $fleet ): ?>
			<tr class="<?= html($fleet->action) ?>ing fleet">
				<th><?= html($fleet) ?></th>
				<td>
					<? if ( $fleet->action == 'return' ): ?>
						is returning from <?= $fleet->destination_planet ?> (ETA: <?= $fleet->travel_eta ?>)
					<? elseif ( in_array($fleet->action, ['attack', 'defend']) ): ?>
						<? if ( $fleet->is_working ): ?>
							is <?= $fleet->action ?>ing <?= $fleet->destination_planet ?> for <?= $fleet->action_eta ?> more ticks
						<? else: ?>
							is moving to <?= $fleet->action ?> <?= $fleet->destination_planet ?> (ETA: <?= $fleet->travel_eta ?>)
						<? endif ?>
					<? elseif ( $fleet->fleetname ): ?>
						is ready to be sent to
						<input class="coord" type="number"  name="mission[<?= $fleet->fleetname ?>][x]"/> :
						<input class="coord" type="number"  name="mission[<?= $fleet->fleetname ?>][y]"/> :
						<input class="coord" type="number"  name="mission[<?= $fleet->fleetname ?>][z]"/>
						for
						<input class="coord" type="number"  name="mission[<?= $fleet->fleetname ?>][ticks]"/>
						ticks
					<? else: ?>
						is fixed at home
					<? endif ?>
				</td>
				<td>
					<? if ( $fleet->available_actions ): ?>
						<select name="mission[<?= $fleet->fleetname ?>][action]">
							<?= html_options($fleet->available_actions, '', '--') ?>
						</select>
					<? endif ?>
				</td>
			</tr>
		<? endforeach ?>
		<tr>
			<td colspan="3">
				<button>Execute actions</button>
			</td>
		</tr>
	</table>
</form>

<?php

_footer();
