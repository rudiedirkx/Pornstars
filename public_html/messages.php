<?php

use rdx\ps\Mail;

require 'inc.bootstrap.php';

logincheck();

_header();

$messages = Mail::all('to_planet_id = ? ORDER BY id DESC', [$g_user->id]);

?>
<style>
table.new * {
	border-color: green;
}
</style>

<h1>Planetary Mail (<?= count($messages) ?>)</h1>

<? foreach ( $messages as $message ): ?>
	<table class="<?= $message->seen ? '' : 'new' ?>">
		<tr>
			<td><?= html($message->from_planet) ?></td>
			<td><?= date('Y-m-d H:i', $message->utc_sent) ?> / <?= $message->myt_sent ?></td>
		</tr>
		<tr>
			<td colspan="2"><?= nl2br(html($message->message)) ?></td>
		</tr>
		<tr>
			<td>reply</td>
			<td></td>
		</tr>
	</table>
	<?
	$message->seen or $message->update(['seen' => 1]);
endforeach ?>

<?php

_footer();
