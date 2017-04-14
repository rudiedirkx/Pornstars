<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Unit extends Model {

	static protected $table = 'd_all_units';

	static public $types = [
		'ship' => 'ship',
		'defence' => 'defence',
		'power' => 'power',
		'roidscan' => 'wave',
		'scan' => 'wave',
		'amp' => 'wave',
		'block' => 'wave',
	];

	static public $tables = [
		'ship' => 'd_ships',
		'defence' => 'd_defence',
		'power' => 'd_power',
		'wave' => 'd_waves',
	];

	static public $subtypes = [
		'sectorscan' => 'scan',
		'unitscan' => 'scan',
		'defencescan' => 'scan',
		'fleetscan' => 'scan',
		'newsscan' => 'scan',
		'productionscan' => 'scan',
		'politicalscan' => 'scan',
	];

	/**
	 * Static
	 */

	static public function typeToBase( $type ) {
		return self::$types[$type];
	}

	static public function baseToTypes( $fromBase ) {
		return array_keys(array_filter(self::$types, function($base, $type) use ($fromBase) {
			return $base == $fromBase;
		}, ARRAY_FILTER_USE_BOTH));
	}

	static public function basesToTypes( ...$fromBases ) {
		$types = [];
		foreach ( $fromBases as $base ) {
			$types = array_merge($types, self::baseToTypes($base));
		}
		return $types;
	}

	/**
	 * Getters
	 */

	public function get_costs() {

	}

	public function get_number_owmed() {
		switch ( $this->base_type ) {
			case 'wave':
				return $this->numberOnPlanet('waves_on_planets', $this->id, $this->planet_id);

			case 'defence':
				return $this->numberOnPlanet('defence_on_planets', $this->id, $this->planet_id);

			case 'power':
				return $this->numberOnPlanet('power_on_planets', $this->id, $this->planet_id);

			case 'ship':
				return $this->numberInFleets($this->id, $this->planet_id);
		}
	}

	public function get_base_type() {
		return self::typeToBase($this->T);
	}

	/**
	 * Logic
	 */

	public function produce( Planet $planet, $amount ) {
		global $db;
		return $db->insert('planet_production', [
			'planet_id' => $planet->id,
			'unit_id' => $this->id,
			'amount' => $amount,
			'eta' => $this->build_eta,
		]);
	}

	protected function numberOnPlanet( $table, $unitId, $planetId ) {
		global $db;
		return $db->select_one(
			$table,
			'amount',
			'unit_id = ? AND planet_id = ?',
			[$unitId, $planetId]
		);
	}

	protected function numberInFleets( $unitId, $planetId ) {
		global $db;
		return $db->select_one(
			'ships_in_fleets s, fleets f',
			'SUM(amount)',
			's.fleet_id = f.id AND f.owner_planet_id = ? AND s.unit_id = ?',
			[$planetId, $unitId]
		);
	}

}
