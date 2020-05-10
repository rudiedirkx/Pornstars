<?php

use rdx\ps\Fleet;
use rdx\ps\Planet;
use rdx\ps\Ticker;
use rdx\ps\Unit;

require 'inc.bootstrap.php';
echo '<style>body { white-space: pre-line; }</style>';
$_time = microtime(1);
$debug = empty($_GET['ajax']);


$ticker = Ticker::instance($debug);


// Missing ships & defences
$ticker->ensureZeroShips();
$ticker->ensureZeroDefences();


$db->begin();


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


// Fleet movement & combat
$fleets = Fleet::all("action IS NOT NULL");
if ($debug) {
	echo "Active fleets:\n";
	dump($fleets);
}

$battles = $ticker->makeBattles($fleets);
foreach ($battles as $battle) {
	$ticker->fightBattle($battle);
}

$ticker->moveFleets($fleets);


// Score
$ticker->updateScores();


$g_prefs->update([
	'last_tick' => time(),
	'tickcount = tickcount + 1',
]);

$db->commit();



echo "\nTook " . round((microtime(1) - $_time) * 1000) . " ms\n\n";

if ( $debug ) {
	// dump($db->bad_queries());
	dump($db->queries);
}
