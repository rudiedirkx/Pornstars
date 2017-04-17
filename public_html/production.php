<?php

require 'inc.bootstrap.php';

logincheck();

if ( isset($_POST['order_units'], $_POST['_token']) ) {
	validTokenOrFail('production');

	addProductions($_POST['order_units'], 'ship', 'defence');

	return do_redirect();
}

_header();

?>
<h1>Production</h1>

<h2>Order new</h2>
<?= getProductionForm('ship', 'defence') ?>

<br />

<h2>Production progress</h2>
<?= getProductionList('ship', 'defence') ?>

<?php

_footer();
