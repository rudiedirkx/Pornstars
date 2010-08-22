<?php

require_once('inc.config.php');

if ( isset($_POST['subject'], $_POST['message']) && ( isset($_POST['from_planet_id'], $_POST['password']) || logincheck(false) ) ) {
	if ( !trim($_POST['message']) ) {
		exit('I\m not sending an empty message... Whatya wanna say!?');
	}
	if ( !logincheck(false) ) {
		if ( !db_count('planets', 'id = '.(int)$_POST['from_planet_id'].' AND password = MD5(CONCAT(id,\':'.addslashes($_POST['password']).'\')) AND (lastlogin > 0 OR activationcode != \'\')') ) {
			exit('This is not a valid planet id!');
		}
		$iFromPlanetId = (int)$_POST['from_planet_id'];
	}
	else {
		$iFromPlanetId = PLANET_ID;
	}
	$arrInsert = array(
		'from_planet_id'	=> $iFromPlanetId,
		'to_planet_id'		=> null,
		'utc_sent'			=> time(),
		'myt_sent'			=> (int)$GAMEPREFS['tickcount'],
		'message'			=> trim($_POST['subject'])."\n----------------------------------------------\n".trim($_POST['message']),
		'is_help_msg'		=> '1',
	);
	if ( db_insert('mail', $arrInsert) ) {
		exit('Message sent! You will be answered in game or by e-mail');
	}
	exit('Mail was NOT delivered! Try again later...');
}

_header();

?>
<br />
<br />

<form method="post" action="">
<table border="1" cellpadding="3" cellspacing="0" width="400" align="center">
<tr>
	<th colspan="2">HELP MESSAGE</th>
</tr>
<?php if ( !logincheck(false) ): ?>
<tr>
	<th width="40%">PLANET ID</th>
	<td><input type="text" name="from_planet_id" value="" size="40" /></td>
</tr>
<tr>
	<th width="40%">PASSWORD</th>
	<td><input type="password" name="password" value="" size="40" /></td>
</tr>
<?php endif; ?>
<tr>
	<th width="40%">SUBJECT</th>
	<td><input type="text" name="subject" value="" size="40" /></td>
</tr>
<tr>
	<td colspan="2"><textarea name="message" rows="7" cols="60"></textarea></td>
</tr>
<tr>
	<th colspan="2"><input type="submit" value="SEND" /></th>
</tr>
</table>
</form>
