<?php

use rdx\ps\Planet;
use rdx\ps\Resource;
use rdx\ps\Ticker;
use rdx\ps\Unit;

require 'inc.bootstrap.php';
header('Content-type: text/plain; charset=utf-8');
$_time = microtime(1);

// Resources
// R & D
// Production
// Wave & energy decay
// Fleet travel
// Combat

$ticker = Ticker::instance();


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
$ticker->setUnits(Unit::all());
$db->update('planet_production', 'eta = eta - 1', 'eta > 0');
$ready = $db->fetch('
	SELECT planet_id, unit_id, SUM(amount) AS amount
	FROM planet_production
	WHERE eta = 0
	GROUP BY planet_id, unit_id
');
foreach ( $ready as $prod ) {
	$planet = $ticker->getPlanet($prod->planet_id);
	$planet->ticker->addProduction($prod->unit_id, $prod->amount);
}

$db->delete('planet_production', ['eta' => 0]);



// Fleet travel


$g_prefs->update([
	'last_tick' => time(),
	'tickcount = tickcount + 1',
]);

echo "\nTook " . round((microtime(1) - $_time) * 1000) . " ms\n\n";

echo "\n";
print_r($db->bad_queries());
echo "\n";
print_r($db->queries);
