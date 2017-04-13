<?php

namespace rdx\ps;

class Resource extends Model {

	static protected $table = 'd_resources';

	/**
	 * Getters
	 */

	public function get_display_number() {
		if ( !$this->is_power ) {
			return (int) $this->asteroids;
		}

		if ( $this->planet_id ) {
			global $db;
			return (int) $db->fetch_one('
				SELECT SUM(amount)
				FROM power_on_planets
				WHERE planet_id = ?
			', 'units', [$this->planet_id]);
		}
	}

}
