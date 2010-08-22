<?php

require_once('inc.config.php');
logincheck();

if ( isset($_POST['journal']) ) {
	db_update('planets', array('journal' => trim($_POST['journal'])), 'id = '.PLANET_ID);
	Go();
}

_header();

?>
<div class="header">Personal Journal</div>

<br />

<form method="post" action="">
<textarea name="journal" rows="20" style="width:100%;"><?php echo htmlspecialchars($g_arrUser['journal']); ?></textarea><br />
<br />
<input type="submit" value="SAVE" style="width:40%">
</form>

<br />

<?php

_footer();

?>