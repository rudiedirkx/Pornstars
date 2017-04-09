<?php

require_once('inc.config.php');
logincheck();

if ( isset($_POST['x'], $_POST['y'], $_POST['z'], $_POST['message']) )
{
	$iPlanetId = db_select_one('galaxies g, planets p', 'p.id', 'p.galaxy_id = g.id AND g.x = '.(int)$_POST['x'].' AND g.y = '.(int)$_POST['y'].' AND p.z = '.(int)$_POST['z']);
	if ( false === $iPlanetId ) {
		exit(json_encode(array(
			array('msg', 'Planet not found!'),
		)));
	}
	if ( !db_insert('mail', array(
		'to_planet_id'		=> $iPlanetId,
		'from_planet_id'	=> PLANET_ID,
		'utc_sent'			=> time(),
		'myt_sent'			=> $GAMEPREFS['tickcount'],
		'message'			=> $_POST['message'],
	)) ) {
		exit(json_encode(array(
			array('msg', 'Mail delivery failed!'),
		)));
	}
	exit(json_encode(array(
		array('msg', 'Mail sent!'),
	)));
}

_header();

?>
<div class="header">Communication</div>

<br />

Send mail:<br />
<form action="communication.php" method="post" onsubmit="return postForm(this,H);" autocomplete="off">
<table border="0" cellpadding="2" cellspacing="0">
<tr valign="top">
	<td>To:</td>
	<td>
		<input type="text" id="recip_x" name="x" style="width:30;text-align:center;" value="<?php echo isset($_GET['x']) ? (int)$_GET['x'] : 'X'; ?>" onfocus="this.select();"> :
		<input type="text" id="recip_y" name="y" style="width:30;text-align:center;" value="<?php echo isset($_GET['y']) ? (int)$_GET['y'] : 'Y'; ?>" onfocus="this.select();"> :
		<input type="text" id="recip_z" name="z" style="width:30;text-align:center;" value="<?php echo isset($_GET['z']) ? (int)$_GET['z'] : 'Z'; ?>" onfocus="this.select();">
		<select onchange="var c=this.value.split(',');if(c.join('')){$('recip_x').value=c[0];$('recip_y').value=c[1];$('recip_z').value=c[2];}"><option value="">--</option><?php $arrPlanets = db_select_fields('planets p, galaxies g', "concat(g.x,',',g.y,',',p.z),concat(rulername,' of ',planetname)", 'p.galaxy_id = g.id AND p.id != '.PLANET_ID.' AND p.galaxy_id = '.$g_arrUser['galaxy_id']); foreach ( $arrPlanets AS $c => $p ) { echo '<option value="'.$c.'">'.$p.'</option>'; } ?></select>
	</td>
</tr>
<tr valign="top">
	<td>Text:</td>
	<td><textarea name="message" cols="50" rows="7"></textarea></td>
</tr>
<tr>
	<td></td>
	<td><input type=submit value="Send"></td>
</tr>
</table>
</form>

<br />

<?php

_footer();

?>
