<?php

require_once('inc.config.php');
logincheck();

if ( !empty($_POST['message']) && ( !empty($_POST['parent_thread_id']) || !empty($_POST['title']) ) )
{
	$arrInsert = array(
		'parent_thread_id'	=> !empty($_POST['parent_thread_id']) ? (int)$_POST['parent_thread_id'] : null,
		'galaxy_id'			=> $g_arrUser['galaxy_id'],
		'utc_time'			=> time(),
		'title'				=> !empty($_POST['title']) ? trim($_POST['title']) : null,
		'message'			=> $_POST['message'],
		'creator_planet_id'	=> PLANET_ID,
	);
	if ( !db_insert('politics', $arrInsert) ) {
		Save_Msg('Could not save topic!', 'red');
	}
	$iThreadId = !empty($_POST['parent_thread_id']) ? $_POST['parent_thread_id'] : db_insert_id();
	Go('?id='.(int)$iThreadId);
}

/*else if ( isset($_GET['delete_id']) && $g_arrUser['moc_planet_id'] === PLANET_ID )
{
	$q = db_query("SELECT threadid FROM $TABLE[politics] WHERE id='".$_GET['tid']."' AND deleted='0';");
	if (!mysql_num_rows($q))
	{
		Save_Msg("This message does not exist!");
		Go();
	}
	if (mysql_result($q,0,'threadid')>0)
	{
		// Bericht is een antwoord, geen Thread (OF bestaat niet)
		db_query("UPDATE $TABLE[politics] SET deleted='1' WHERE id='".$_GET['tid']."';");

		Go("?id=".mysql_result($q,0,'threadid'));
	}
	else
	{
		// Bericht is Thread, dus alle antwoorden ook weggooien!
		db_query("UPDATE $TABLE[politics] SET deleted='1' WHERE (id='".$_GET['tid']."' OR threadid='".$_GET['tid']."');");

		Go();
	}
}*/

_header();

?>
<div class="header"><?php echo isset($_GET['id']) && count($t=db_fetch('SELECT p.id, c.rulername, c.planetname, p.utc_time, p.title, p.message FROM politics p, planets c WHERE c.id = p.creator_planet_id AND p.galaxy_id = '.(int)$g_arrUser['galaxy_id'].' AND (p.id = '.(int)$_GET['id'].' OR p.parent_thread_id = '.(int)$_GET['id'].') ORDER BY id ASC')) ? '<a href="'.BASEPAGE.'">' : ''; ?>Politics<?php echo !empty($t) ? '</a>' : ''; ?></div>

<br />

<?php

if ( !empty($t) )
{
	$arrTopics = $t;
	echo '<div>';
	foreach ( $arrTopics AS $arrTopic ) {
		echo '<br />';
		echo '<table border="0" cellpadding="3" cellspacing="0" width="600" style="background-color:#111;" align="center">';
		echo '<tr>';
		echo '<td><b>'.$arrTopic['rulername'].'</b><!-- of <b>'.$arrTopic['planetname'].'</b>--></td>';
		echo '<td align="right">'.strtolower(date("d-M-Y \a\\t H:i", $arrTopic['utc_time'])).'</td>';
		echo '</tr>';
		echo '<tr class="bt" valign="top">';
		echo '<td height="60" colspan="2">'.( trim($arrTopic['title']) ? '&quot;'.htmlspecialchars(trim($arrTopic['title'])).'&quot;<hr />' : '' ).nl2br(htmlspecialchars(trim($arrTopic['message']))).'<br></td>';
		echo '</tr>';
		if ( (int)$g_arrUser['moc_planet_id'] === PLANET_ID ) {
			echo '<td align=right><a href="?delete_id='.$arrTopic['id'].'">del</a></td>';
		}
		echo '<tr>';
		echo '<td colspan="2" align="right"><a href="#message">reply</a></td>';
		echo '</tr>';
		echo '</table>';
		echo '<br />';
	}
	echo '</div>';
}
else
{
	$arrTopics = db_fetch('SELECT p.id, p.title, p.utc_time, c.rulername, c.planetname, (SELECT COUNT(1) FROM politics WHERE parent_thread_id = p.id AND galaxy_id = '.(int)$g_arrUser['galaxy_id'].' AND is_deleted = \'0\') AS num_replies FROM politics p, planets c WHERE c.id = p.creator_planet_id AND p.galaxy_id = '.$g_arrUser['galaxy_id'].' AND parent_thread_id IS NULL AND p.is_deleted = \'0\' ORDER BY p.id DESC');

	echo '<table border="0" cellpadding="5" cellspacing="0" width="600" align="center">';
	echo '<tr class="bb">';
	echo '<th class="left">Title</th>';
	echo '<th>Poster</th>';
	echo '<th class="right">Date & Time</th>';
	echo '<th class="right">Replies</th>';
	if ( (int)$g_arrUser['moc_planet_id'] === PLANET_ID ) {
		echo '<th class="right">Action</th>';
	}
	echo '</tr>';
	foreach ( $arrTopics AS $arrTopic ) {
		echo '<tr class="bt">';
		echo '<td><a href=?id='.$arrTopic['id'].'>'.( trim($arrTopic['title']) ? htmlspecialchars($arrTopic['title']) : '---' ).'</a></td>';
		echo '<td align="center"><b>'.$arrTopic['rulername'].'</b><!-- of <b>'.$arrTopic['planetname'].'</b>--></td>';
		echo '<td class="right">'.strtolower(date("d-M-Y \a\\t H:i", $arrTopic['utc_time'])).'</td>';
		echo '<th class="right">'.$arrTopic['num_replies'].'</th>';
		if ( (int)$g_arrUser['moc_planet_id'] === PLANET_ID ) {
			echo '<td align=right><a href="?delete_id='.$arrTopic['id'].'">del</a></td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}

echo "<br>\n<br>\n\n";

$disabled_ornot = '';
if ( isset($_GET['id']) && !empty($t) && (int)$GAMEPREFS['galaxy_forum_wait_for_turn'] ) {
	if ( PLANET_ID === (int)db_select_one('politics', 'creator_planet_id', 'galaxy_id = '.(int)$g_arrUser['galaxy_id'].' AND (id = '.(int)$_GET['id'].' OR parent_thread_id = '.(int)$_GET['id'].') ORDER BY id DESC') ) {
		$disabled_ornot = ' disabled="1"';
	}
}

?>
<form method="post" action="">
<table border="0" cellpadding="4" cellspacing="0" width="600" align="center">
<tr>
	<th class="bb"><?php echo !empty($t) ? 'REPLY' : 'NEW POST'; ?></th>
</tr>
<?php echo !empty($t) ? '<input type="hidden" name="parent_thread_id" value="'.(int)$_GET['id'].'" />' : '<tr class="bt"><td class="c">Title:</td></tr><tr><td class="c"><input'.$disabled_ornot.' type="text" name="title" style="width:450px;" /></td></tr>'; ?>
<tr class="bt">
	<td class="c">Message:</td>
</tr>
<tr>
	<td class="c"><textarea<?php echo $disabled_ornot ;?> name="message" rows=8 style='overflow:auto;width:500;'><?php echo $disabled_ornot ? 'You cannot post the next message because the last one is already yours. You have to wait turn. This is a forum!' : ''; ?></textarea></td>
</tr>
<tr>
	<td class="c"><input<?php echo $disabled_ornot ;?> type="submit" value="Post" style="width:300px;"></td>
</tr>
</table>
</form>

<br />

<?php

_footer();

?>
