<?php

use rdx\ps\Alliance;

require 'inc.bootstrap.php';

logincheck();

$alliances = Alliance::all();

$otherMembers = $g_user->alliance ? array_filter($g_user->alliance->planets, function($planet) {
	global $g_user;
	return $planet->id != $g_user->alliance->leader_planet_id;
}) : [];

// CREATE
if ( isset($_POST['new_name'], $_POST['new_tag']) ) {
	validTokenOrFail('alliance');

	if ( $g_user->alliance ) {
		return accessFail('alliance');
	}

	$pwd = rand_string();

	$id = Alliance::insert([
		'name' => trim($_POST['new_name']),
		'tag' => trim($_POST['new_tag']),
		'pwd' => password_hash($pwd, PASSWORD_DEFAULT),
		'leader_planet_id' => $g_user->id,
	]);

	$g_user->update(['alliance_id' => $id]);

	sessionSuccess("Alliance created. Password = '$pwd'");

	return do_redirect();
}

// JOIN
if ( isset($_POST['join'], $_POST['pwd']) ) {
	validTokenOrFail('alliance');

	if ( !$g_user->alliance && isset($alliances[ $_POST['join'] ]) ) {
		$alliance = $alliances[ $_POST['join'] ];
		if ( $alliance->checkPassword($_POST['pwd']) ) {
			$g_user->update([
				'alliance_id' => $alliance->id,
			]);
		}
		else {
			sessionWarning('Wrong password');
		}
	}

	return do_redirect();
}

// TERMINATE
if ( isset($_POST['terminate']) ) {
	validTokenOrFail('alliance');

	if ( $g_user->alliance && $g_user->alliance->leader_planet_id == $g_user->id ) {
		$g_user->alliance->delete();
	}

	return do_redirect();
}

// REFRESH PASSWORD
if ( isset($_POST['refreshpwd']) ) {
	validTokenOrFail('alliance');

	if ( $g_user->alliance && $g_user->alliance->leader_planet_id == $g_user->id ) {
		$pwd = rand_string();

		$g_user->alliance->update([
			'pwd' => password_hash($pwd, PASSWORD_DEFAULT),
		]);

		sessionSuccess("New password = '$pwd'");
	}

	return do_redirect();
}

// APPOINT NEW LEADER
if ( isset($_POST['appoint']) ) {
	validTokenOrFail('alliance');

	if ( $g_user->alliance && $g_user->alliance->leader_planet_id == $g_user->id ) {
		if ( isset($otherMembers[ $_POST['appoint'] ]) ) {
			$g_user->alliance->update([
				'leader_planet_id' => $_POST['appoint'],
			]);
		}
	}

	return do_redirect();
}

// KICK MEMBER
if ( isset($_POST['kick']) ) {
	validTokenOrFail('alliance');

	if ( $g_user->alliance && $g_user->alliance->leader_planet_id == $g_user->id ) {
		if ( isset($otherMembers[ $_POST['kick'] ]) ) {
			$otherMembers[ $_POST['kick'] ]->update([
				'alliance_id' => null,
			]);
		}
	}

	return do_redirect();
}

// LEAVE
if ( isset($_POST['leave']) ) {
	validTokenOrFail('alliance');

	if ( $g_user->alliance && $g_user->alliance->leader_planet_id != $g_user->id ) {
		$g_user->update([
			'alliance_id' => null,
		]);
	}

	return do_redirect();
}

_header();

?>
<h1>Alliance</h1>

<?php

if ( $g_user->alliance ) {

	$leader = $g_user->alliance->leader_planet_id == $g_user->id;
	$status = $leader ? "the leader" : "a member";

	echo "<p>You are {$status} of <em>" . html($g_user->alliance) . "</em> (" . html($g_user->alliance->tag) . ").</p>";
	echo "<p>Your alliance has " . count($g_user->alliance->planets) . " members, including you:</p>";
	echo '<pre>' . html(print_r(array_map('strval', $g_user->alliance->planets), 1)) . '</pre>';

	if ( $leader ) {
		?>
		<form autocomplete="off" method="post">
			<input type="hidden" name="_token" value="<?= createToken('alliance') ?>" />

			<p>
				Kick member:
				<select name="kick"><?= html_options($otherMembers) ?></select>
				<button>Kick</button>
			</p>
		</form>

		<br />

		<form autocomplete="off" method="post">
			<input type="hidden" name="_token" value="<?= createToken('alliance') ?>" />

			<p>
				New leader:
				<select name="appoint"><?= html_options($otherMembers) ?></select>
				<button>Appoint new leader</button>
			</p>
		</form>

		<br />

		<form autocomplete="off" method="post">
			<input type="hidden" name="_token" value="<?= createToken('alliance') ?>" />
			<input type="hidden" name="refreshpwd" value="1" />

			<p><button>Refresh password</button></p>
		</form>

		<br />

		<form autocomplete="off" method="post">
			<input type="hidden" name="_token" value="<?= createToken('alliance') ?>" />
			<input type="hidden" name="terminate" value="1" />

			<p><button>Terminate alliance</button></p>
		</form>
		<?php
	}
	else {
		?>
		<form autocomplete="off" method="post">
			<input type="hidden" name="_token" value="<?= createToken('alliance') ?>" />
			<input type="hidden" name="leave" value="1" />

			<p><button>Leave alliance</button></p>
		</form>
		<?php
	}
}
else {
	?>
	<h2>Join alliance</h2>
	<form autocomplete="off" method="post">
		<input type="hidden" name="_token" value="<?= createToken('alliance') ?>" />

		<p>Join <select name="join"><?= html_options($alliances) ?></select></p>
		<p>Password: <input name="pwd" /></p>
		<p><button>Join</button></p>
	</form>

	<br />

	<h2>New alliance</h2>
	<form autocomplete="off" method="post">
		<input type="hidden" name="_token" value="<?= createToken('alliance') ?>" />

		<table>
			<tr>
				<th>Name</th>
				<td><input name="new_name" /></td>
			</tr>
			<tr>
				<th>Tag</th>
				<td><input name="new_tag" /></td>
			</tr>
			<tr>
				<td colspan="2"><button>Create</button></td>
			</tr>
		</table>
	</form>
	<?php
}

_footer();
