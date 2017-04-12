<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Fleet extends Model {

	static protected $table = 'fleets';

	/**
	 * Getters
	 */

	public function get_total_ships() {
		global $db;
		return $db->fetch_one('
			SELECT SUM(amount) AS total
			FROM ships_in_fleets
			WHERE fleet_id = ?
		', 'total', [$this->id]);
	}

	public function get_destination_planet() {
		if ( $this->destination_planet_id ) {
			return Planet::find($this->destination_planet_id);
		}
	}

}
