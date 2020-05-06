<?php

use rdx\ps\Planet;
use rdx\ps\Ticker;
use rdx\ps\Unit;

require 'inc.bootstrap.php';
// header('Content-type: text/plain; charset=utf-8');
$_time = microtime(1);


$ticker = Ticker::instance();


// Resources
$ticker->setPlanets(Planet::all());
foreach ( $ticker->planets as $planet ) {
	foreach ( $planet->resources as $resource ) {
		$planet->ticker->addResource($resource);
	}
}


// R & D
$db->update('planet_r_d', 'eta = eta - 1', 'eta > 0');


// Skills
$db->update('skill_training', 'eta = eta - 1', 'eta > 0');
$done = $db->select('skill_training', 'eta = 0');
foreach ( $done as $skill ) {
	$db->update('planet_skills', 'value = value + 1', [
		'planet_id' => $skill->planet_id,
		'skill_id' => $skill->skill_id,
	]);
}
$db->delete('skill_training', ['eta' => 0]);


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
// @todo


// Combat
// @todo


// Score
$ticker->updateScores();


$g_prefs->update([
	'last_tick' => time(),
	'tickcount = tickcount + 1',
]);

echo "\nTook " . round((microtime(1) - $_time) * 1000) . " ms\n\n";

if (empty($_GET['ajax'])) {
	dump($db->bad_queries());
	dump($db->queries);
}
