<?php

require 'inc.bootstrap.php';

logincheck();

$inProgress = $g_user->training_skills;

if ( isset($_GET['start']) ) {

	validTokenOrFail('skill');

	$skills = $g_user->skills;
	if ( $inProgress || !isset($skills[ $_GET['start'] ]) ) {
		accessFail('id');
	}

	$skill = $skills[ $_GET['start'] ];
	$skill->train($g_user);

	return do_redirect();
}

_header();

?>

<style>
tr.doing {
	color: orange;
}
<? if ( $inProgress ): ?>
	tr.available {
		opacity: 0.6;
	}
<? endif ?>
</style>

<h1>Skills</h1>

<table>
	<tr>
		<th></th>
		<th>Name</th>
		<th>ETA</th>
		<th>Progress</th>
		<th>Action</th>
		<th>Current level</th>
	</tr>
	<? foreach ( $g_user->skills AS $skill ): ?>
		<tr class="<?= $skill->status ?>">
			<td><?= $skill->id ?></td>
			<td>
				<?= html($skill->skill) ?><br />
				<?= html($skill->explanation) ?>
			</td>
			<td><?= $skill->is_doing ? $skill->planet_eta : $skill->eta ?></td>
			<td><?= $skill->is_doing ? $skill->pct_done . ' %' : '' ?></td>
			<td>
				<? if ( !$skill->is_done && !$skill->is_doing && !$inProgress ): ?>
					<a href="?start=<?= $skill->id ?>&_token=<?= createToken('skill') ?>">train</a>
				<? endif ?>
			</td>
			<td><?= $skill->planet_value ?></td>
		</tr>
	<? endforeach ?>
</table>

<?php

_footer();
