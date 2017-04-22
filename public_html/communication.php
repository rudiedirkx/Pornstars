<?php

use rdx\ps\Mail;
use rdx\ps\Planet;

require 'inc.bootstrap.php';

logincheck();

if ( isset($_POST['x'], $_POST['y'], $_POST['z'], $_POST['message']) ) {
	$recipient = Planet::fromCoordinates($_POST['x'], $_POST['y'], $_POST['z']);
	if ( !$recipient ) {
		sessionError('Unknown recipient planet');
		return do_redirect();
	}

	Mail::insert([
		'to_planet_id'		=> $recipient->id,
		'from_planet_id'	=> $g_user->id,
		'utc_sent'			=> time(),
		'myt_sent'			=> $g_prefs->tickcount,
		'message'			=> trim($_POST['message']),
	]);

	sessionSuccess('Message sent to <em>' . html($recipient) . '</em>');
	return do_redirect();
}

_header();

?>
<h1>Communication</h1>

<form action method="post" autocomplete="off">
	<table>
		<tr>
			<td>To:</td>
			<td>
				<input type="number" name="x" value="<?= (int) @$_GET['x'] ?: '' ?>" placeholder="x" /> :
				<input type="number" name="y" value="<?= (int) @$_GET['y'] ?: '' ?>" placeholder="y" /> :
				<input type="number" name="z" value="<?= (int) @$_GET['z'] ?: '' ?>" placeholder="z" />
			</td>
		</tr>
		<tr>
			<td>Text:</td>
			<td><textarea name="message" cols="60" rows="6"></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td><button>Send</button></td>
		</tr>
	</table>
</form>

<?php

_footer();
