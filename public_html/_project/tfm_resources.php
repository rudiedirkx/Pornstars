<?php

require_once('../inc.connect.php');

echo '<pre>';

$arrResourceIds = db_select_fields('d_resources', 'LOWER(resource),id');
print_r($arrResourceIds);

/** R & D **
$arrRD = db_select('d_r_d_available');
foreach ( $arrRD AS $rd ) {
	foreach ( $arrResourceIds AS $szResource => $iResource ) {
		if ( isset($rd[$szResource]) && 0 < $rd[$szResource] ) {
			var_dump(db_update('d_r_d_costs', 'amount = '.(int)$rd[$szResource], 'resource_id = '.$iResource.' AND r_d_id = '.(int)$rd['id']));
		}
	}
}
/**/

/** Units **
$arrUnits = db_select('d_all_units');
foreach ( $arrUnits AS $u ) {
	foreach ( $arrResourceIds AS $szResource => $iResource ) {
		if ( isset($u[$szResource]) && 0 < $u[$szResource] ) {
			var_dump(db_update('d_unit_costs', 'amount = '.(int)$u[$szResource], 'resource_id = '.$iResource.' AND unit_id = '.(int)$u['id']));
		}
	}
}
/**/

?>