<?php

require 'inc.bootstrap.php';

require_once('inc.shipstats.php');

_header();

$rs = db_select('d_resources', '1 ORDER BY id ASC');

?>

<style type="text/css">
.bt td {
	border-top : solid 3px black;
}
</style>

<table border="0" cellpadding="5" cellspacing="1" width="100%">
<tr bgcolor="#aaaaaa" style="color:black;">
	<th colspan="7" style="border-right:solid 1px black;">Unit</td>
	<th colspan="<?php echo count($rs)+1; ?>" style="border-right:solid 1px black;">Production</td>
	<th colspan="2">Action</td>
</tr>
<tr bgcolor="#aaaaaa" style="color:black;">
	<th width="10"></th>
	<th align="right">Name</th>
	<th align="center">Short</th>
	<th align="center">Type</th>
	<th align="center" title="Primary Target (value is `Short`)">PT</th>
	<th align="center" title="Secondary Target (value is `Short`)">T2</th>
	<th align="center" style="border-right:solid 1px black;" title="Tertiary Target (value is `Short`)">T3</th>
	<?php foreach ( $rs AS $r ) {
		echo '<th align="right">'.$r['resource'].'</th>'."\n";
	} ?>
	<th align="right" style="border-right:solid 1px black;">ETA</th>
	<th align="right">Fuel/tick</th>
	<th align="right">Min. ETA</th>
</tr>
<?php

$c = array('#333333', '#222222');
$i = $t = 0;
$arrFighterUnits = db_select_by_field('d_all_units', 'id', 'T IN (\'ship\', \'defence\') ORDER BY T ASC, o ASC');
foreach ( $arrFighterUnits AS $iUnitId => &$arrUnit ) {

	$arrUnit['targets'] = array_values(db_select_fields('d_combat_stats', 'receiving_unit_id,receiving_unit_id', 'shooting_unit_id = '.(int)$arrUnit['id'].' ORDER BY target_priority ASC LIMIT 3'));

	$szType = 'defence' === $arrUnit['T'] ? 'DEF' : ( $arrUnit['is_stealth'] ? 'STE' : ( !$arrUnit['is_offensive'] ? 'NLF' : 'NOR' ) );
	$szTypes = array(
		'NOR'	=> 'Normal',
		'NLF'	=> 'Non-lethal',
		'STE'	=> 'Stealth',
		'DEF'	=> 'Defensive',
	);
	echo '<tr'.( 0 < $i && $t !== $arrUnit['T'] ? ' class="bt"' : '' ).' bgcolor="'.$c[$i++%2].'">';
	echo '<td align="right" width="10">'.$arrUnit['id'].'</td>';
	echo '<td align="right">'.$arrUnit['unit'].'</td>';
	echo '<td align="center">'.strtolower($arrUnit['unit_short']).'</td>';
	echo '<td align="center" title="'.$szTypes[$szType].'">'.$szType.'</td>';
	echo '<td align="center">'.( isset($arrUnit['targets'][0]) ? strtolower($arrFighterUnits[$arrUnit['targets'][0]]['unit_short']) : '-' ).'</td>';
	echo '<td align="center">'.( isset($arrUnit['targets'][1]) ? strtolower($arrFighterUnits[$arrUnit['targets'][1]]['unit_short']) : '-' ).'</td>';
	echo '<td align="center" style="border-right:solid 1px black;">'.( isset($arrUnit['targets'][2]) ? strtolower($arrFighterUnits[$arrUnit['targets'][2]]['unit_short']) : '-' ).'</td>';
	foreach ( db_fetch('SELECT c.*, r.resource FROM d_resources r LEFT JOIN d_unit_costs c ON r.id = c.resource_id AND c.unit_id = '.$arrUnit['id'].' WHERE 1 ORDER BY r.id ASC') AS $costs ) {
		echo '<td align="right" title="'.$costs['resource'].'">'.nummertje($costs['amount']).'</td>';
	}
	echo '<td align="right" style="border-right:solid 1px black;">'.$arrUnit['build_eta'].'</td>';
	echo '<td align="right">'.nummertje($arrUnit['fuel']).'</td>';
	echo '<td align="right">'.( 'DEF' != $szType ? $arrUnit['move_eta'] : '-' ).'</td>';
	echo '</tr>';
	$t = $arrUnit['T'];
	unset($arrUnit);
}

?>
</table>

<br />
<br />

<b>READ THIS</b><br />
- The ETA in the statistics table is in your galaxy and defending! (minimum of eta)<br />
<!--&nbsp;&nbsp;- ETA outside your galaxy is always 5 ticks more.<br />
&nbsp;&nbsp;- ETA attacking is always <?php echo ($GAMEPREFS['military_defend_ticks']-$GAMEPREFS['military_attack_ticks']); ?> ticks more.<br />-->
