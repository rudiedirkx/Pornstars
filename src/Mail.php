<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Mail extends Model {

	static protected $table = 'mail';

	/**
	 * Getters
	 */

	public function get_from_planet() {
		return Planet::find($this->from_planet_id);
	}

}
