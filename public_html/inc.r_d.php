<?php

if ( isset($_GET['start'], $_GET['_token']) ) {
	validTokenOrFail('rd');

	$rd = $g_user->canRD($rdType, $_GET['start']);
	if ( !$rd ) {
		return accessFail('id');
	}

	try {
		$g_user->takeTransaction(function($g_user) use ($rd) {
			$costs = $rd->costs;
			$costs = array_column($costs, 'amount', 'id');

			// Take resources
			$g_user->takeResources($costs);
		});

		$rd->start($g_user);
	}
	catch ( NotEnoughException $ex ) {
		sessionError('Not enough: ' . $ex->getMessage());
	}

	return do_redirect();
}

_header();

$inProgress = $g_user->getRD($rdType);
// print_r($inProgress);

$available = $g_user->getAvailableRD($rdType);
// print_r($available);

?>

<style>
tr.done {
	color: green;
}
tr.doing {
	color: gold;
}
<? if ( $inProgress ): ?>
	tr.available {
		opacity: 0.6;
	}
<? endif ?>
</style>

<h1><?= html($pageTitle) ?></h1>

<table border="1">
	<tr>
		<th></th>
		<th>Name</th>
		<th>ETA</th>
		<th>Progress</th>
		<th>Action</th>
		<th>Costs</th>
	</tr>
	<? foreach ( $available AS $rd ): ?>
		<tr class="<?= $rd->status ?>">
			<td><?= $rd->id ?></td>
			<td>
				<?= html($rd->name) ?><br />
				<?= html($rd->explanation) ?><br>
				Requires <?= count($rd->requires_rd_ids) ?>.
				Required by <?= count($rd->required_by_rd_ids) ?>.
				<? if ( $rd->excludes_rd_ids ): ?>
					<span style="color: red">
						Excludes: <?= implode(', ', array_map('strval', $rd->excludes_rd)) ?>
					</span>
				<? endif ?>
			</td>
			<td><?= $rd->is_doing ? $rd->planet_eta : $rd->eta ?></td>
			<td><?= $rd->is_doing ? $rd->pct_done . ' %' : '' ?></td>
			<td>
				<? if ( !$rd->is_done && !$rd->is_doing && !$inProgress ): ?>
					<a href="?start=<?= $rd->id ?>&_token=<?= createToken('rd') ?>">start</a>
				<? endif ?>
			</td>
			<td nowrap><?= renderCostsVariant($rd->costs) ?></td>
		</tr>
	<? endforeach ?>
</table>

<br />

<?php

_footer();
