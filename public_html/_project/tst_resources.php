<?php

require_once('../inc.connect.php');

echo '<pre>';

db_update('planet_resources', 'amount = 500000', 'planet_id = 1');

// PRE
echo 'Pre: ';
print_r(db_select_fields('d_resources r, planet_resources p', 'resource_id,amount', 'p.resource_id = r.id AND p.planet_id = 1'));
echo "\n";
/**/
exit;
/**/

$arrUpdates = array(1 => 300000, 2 => 50, 3 => 50);
function db_transaction_update($f_arrUpdates, $f_szIfField, $f_szUpdateField) {
	db_query("BEGIN;");
	$szIfClause = '__N__';
	$szIfClause0 = 'IF('.$f_szIfField.'=__X__,__Y__,__N__)';
	foreach ( $f_arrUpdates AS $x => $y ) {
		$szIfClause = str_replace('__N__', str_replace('__X__', $x, str_replace('__Y__', $y, $szIfClause0)), $szIfClause);
	}
	$szIfClause = str_replace('__N__', '0', $szIfClause);
	db_query('UPDATE planet_resources SET '.$f_szUpdateField.' = '.$f_szUpdateField.' - '.$szIfClause.' WHERE '.$f_szUpdateField.' >= '.$szIfClause.' AND planet_id = 1;');
	if ( count($f_arrUpdates) === (int)db_affected_rows() ) {
		db_query("COMMIT;");
		return true;
	}
	db_query("ROLLBACK;");
	return false;
}
var_dump(db_transaction_update($arrUpdates, 'resource_id', 'amount'));
echo "\n";


// POST
echo 'Post: ';
print_r(db_select_fields('d_resources r, planet_resources p', 'resource_id,amount', 'p.resource_id = r.id AND p.planet_id = 1'));

?>
