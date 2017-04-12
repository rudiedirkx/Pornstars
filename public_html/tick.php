<?php

use rdx\ps\Planet;
use rdx\ps\Resource;
use rdx\ps\Ticker;

require 'inc.bootstrap.php';

// Resources
// R & D
// Production
// Fleet travel

$ticker = new Ticker;


// Resources
$ticker->setPlanets(Planet::all());
$ticker->setResources(Resource::all());
foreach ( $ticker->planets as $planet ) {
	foreach ( $ticker->resources as $resource ) {
		$planet->ticker->addResource($resource);
	}
}

// @todo Energy


// R & D
$db->update('planet_r_d', 'eta = eta - 1', 'eta > 0');


// Production


// Fleet travel


$g_prefs->update([
	'last_tick' => time(),
]);

print_r($db->queries);
