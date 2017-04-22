<?php

use rdx\ps\Thread;

require 'inc.bootstrap.php';

logincheck();

// $moc = $g_user->id == $g_user->galaxy->moc_planet_id;

$thread = Thread::findReadable(@$_GET['thread'], $g_user);

// NEW THREAD
if ( isset($_POST['new_message'], $_POST['new_title']) ) {
	Thread::insert([
		'galaxy_id' => $g_user->galaxy_id,
		'created_on' => time(),
		'title' => trim($_POST['new_title']),
		'message' => trim($_POST['new_message']),
		'creator_planet_id' => $g_user->id,
	]);

	return do_redirect();
}

// REPLY
if ( $thread && isset($_POST['reply']) ) {
	Thread::insert([
		'parent_thread_id' => $thread->id,
		'galaxy_id' => $g_user->galaxy_id,
		'created_on' => time(),
		'title' => '',
		'message' => trim($_POST['reply']),
		'creator_planet_id' => $g_user->id,
	]);

	return do_redirect(null, ['thread' => $thread->id]);
}

_header();

// @todo MoC powers

?>
<h1><?= $thread ? html($thread->title) : 'Politics' ?></h1>

<? if ( $thread ):
	$posts = array_merge([$thread], $thread->posts);
	?>
	<? foreach ( $posts as $post ): ?>
		<? include 'inc.post.php' ?>
		<br />
	<? endforeach ?>

	<h2>Reply</h2>

	<form method="post" action>
		<p><textarea name="reply" required cols="60" rows="6"></textarea></p>
		<p><button>Reply</button></p>
	</form>
<? else: ?>
	<? foreach ( $g_user->galaxy->threads as $post ): ?>
		<? include 'inc.post.php' ?>
		<br />
	<? endforeach ?>

	<h2>New topic</h2>

	<form method="post" action>
		<p>Topic: <input name="new_title" required /></p>
		<p><textarea name="new_message" required cols="60" rows="6"></textarea></p>
		<p><button>Reply</button></p>
	</form>
<? endif ?>

<?php

_footer();
