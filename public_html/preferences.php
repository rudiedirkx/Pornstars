<?php

require 'inc.bootstrap.php';

logincheck();

if ( isset($_POST['current_password'], $_POST['new_password']) ) {
	if ( $g_user->checkPassword($_POST['current_password']) ) {
		$g_user->update([
			'password' => $_POST['new_password'],
		]);
		sessionSuccess('Password changed');
	}
	else {
		sessionError('Current password is wrong');
	}

	return do_redirect();
}

_header();

?>

<h1>Preferences</h1>

<form method="post" action>
	<table>
		<? /* <tr>
			<th>Race</th>
			<td><?= $g_user->race ?></td>
		</tr> */ ?>
		<tr>
			<th>Email</th>
			<td><?= $g_user->email ?></td>
		</tr>
		<tr>
			<td colspan="2"><br /></td>
		</tr>
		<tr>
			<th></th>
			<td>Change Your Password:</td>
		</tr>
		<tr>
			<th>Current Password</th>
			<td><input name="current_password" type="password" required /></td>
		</tr>
		<tr>
			<th>New Password</th>
			<td><input name="new_password" type="password" required /></td>
		</tr>
		<tr>
			<th></th>
			<td><button>Change password</button></td>
		</tr>
	</table>
</form>

<?php

_footer();
