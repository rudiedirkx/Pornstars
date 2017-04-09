<?php

require_once('inc.config.php');
logincheck();

if ( isset($_POST['order_units']) )
{
	addProductions('ship,defence', $_POST['order_units']);
	$arrJson = array(
		array('eval', "$('f_order_units').reset();"),
		array('html', 'div_productionlist', getProductionList('ship,defence')),
		array('msg', 'Productions added!'),
	);
	foreach ( db_select_fields('planet_resources', 'resource_id,amount', 'planet_id = '.PLANET_ID) AS $iResourceId => $iAmount ) {
		$arrJson[] = array('html', 'res_amount_'.$iResourceId, nummertje($iAmount));
	}
	exit(json_encode($arrJson));
}

_header();

?>
<div class="header">Production<?php if ( (int)$GAMEPREFS['havoc_production'] ) { echo ' (<b style="color:red;">HAVOC!</b>)'; } ?></div>

<br />

<?php echo getProductionForm('ship,defence'); ?>

<br />

<div id="div_productionlist">
<?php

echo getProductionList('ship,defence');

echo '</div><br />';


_footer();

?>
