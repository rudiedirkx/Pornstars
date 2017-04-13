<?php

use rdx\ps\Planet;
use rdx\ps\Resource;
use rdx\ps\Ticker;

require 'inc.bootstrap.php';
header('Content-type: text/plain; charset=utf-8');
$_time = microtime(1);

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


// R & D
$db->update('planet_r_d', 'eta = eta - 1', 'eta > 0');


// Production


// Fleet travel


$g_prefs->update([
	'last_tick' => time(),
]);

echo "\nTook " . round((microtime(1) - $_time) * 1000) . " ms\n\n";

echo "\n";
print_r($db->bad_queries());
echo "\n";
print_r($db->queries);
