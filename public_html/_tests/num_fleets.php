<?

include("../config.php");

/** CHECK NUM FLEETS **/
$a = PSQ("SELECT owner_id,owner_x,owner_y,COUNT(*) AS num FROM $TABLE[fleets] GROUP BY owner_id ASC");
while (list($owner_id,$owner_x,$owner_y,$num_fleets) = mysql_fetch_row($a))
{
	$num_fleets-=1;
	if ($num_fleets < $NUM_OUTGOING_FLEETS)
	{
		// Add one or more fleets for this $owner_id
		for ($i=$num_fleets+1;$i<=$NUM_OUTGOING_FLEETS;$i++)
		{
			PSQ("INSERT INTO $TABLE[fleets] (owner_id,owner_x,owner_y,fleetname) VALUES ('$owner_id','$owner_x','$owner_y','$i');");
		}
	}
	else if ($num_fleets > $NUM_OUTGOING_FLEETS)
	{
		// Remove one or more fleets of this $owner_id and transfer all ships to fleet 'base' (ONLY FLEETS NOT ACTIVE, so purpose is empty: '')
		$b = PSQ("SELECT id,infinitys,wraiths,warfrigs,astropods,cobras,destroyers,scorpions,antennas,(infinitys+wraiths+warfrigs+astropods+cobras+destroyers+scorpions+antennas) AS sum_ships FROM fleets WHERE owner_id='$owner_id' AND purpose='' ORDER BY fleetname DESC LIMIT ".($num_fleets-$NUM_OUTGOING_FLEETS).";");
		while (list($fleet_id,$infinitys,$wraiths,$warfrigs,$astropods,$cobras,$destroyers,$scorpions,$antennas,$sum_ships) = mysql_fetch_row($b))
		{
			if ($sum_ships)
			{
				// $infinitys,$wraiths,$warfrigs,$astropods,$cobras,$destroyers,$scorpions,$antennas toevoegen aan fleet Base voor Owner $owner_id
				PSQ("UPDATE $TABLE[fleets] SET infinitys=infinitys+$infinitys,wraiths=wraiths+$wraiths,warfrigs=warfrigs+$warfrigs,astropods=astropods+$astropods,cobras=cobras+$cobras,destroyers=destroyers+$destroyers,scorpions=scorpions+$scorpions,antennas=antennas+$antennas WHERE owner_id='$owner_id' AND fleetname='0';");
			}
			// En de vloot die teveel is weggooien
			PSQ("DELETE FROM $TABLE[fleets] WHERE id='$fleet_id';");
		}
	}
	else
	{
		echo "(fleets for owner $owner_id good ($num_fleets))<br>";
	}
}