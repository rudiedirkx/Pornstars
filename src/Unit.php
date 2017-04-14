<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Unit extends Model {

	static protected $table = 'd_all_units';

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
		switch ( $this->T ) {
			case 'amp':
			case 'block':
			case 'roidscan':
			case 'scan':
				return 'wave';

			case 'defence':
			case 'power':
			case 'ship':
				return $this->T;
		}
	}

	/**
	 * Logic
	 */

	public function produce( Planet $planet, $amount ) {
		global $db;
		// @todo ETA
		return $db->insert('planet_production', [
			'planet_id' => $planet->id,
			'unit_id' => $this->id,
			'amount' => $amount,
			'eta' => 1,
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
