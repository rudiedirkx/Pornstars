<?php

require 'inc.bootstrap.php';

logincheck();

if ( isset($_POST['journal']) ) {
	$g_user->update(['journal' => trim($_POST['journal'])]);

	return do_redirect();
}

_header();

?>
<h1>Personal Journal</h1>

<form method="post" action>
	<p><textarea name="journal" rows="10" style="width: 100%"><?= html($g_user->journal) ?></textarea></p>
	<p><button>Save</button></p>
</form>

<?php

_footer();
