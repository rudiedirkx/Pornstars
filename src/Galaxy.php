<?php

namespace rdx\ps;

class Galaxy extends Model {

	static protected $table = 'galaxies';

	/**
	 * Getters
	 */

	public function get_gc() {
		if ( $this->gc_planet_id ) {
			return Planet::find($this->gc_planet_id);
		}
	}

	/**
	 * Logic
	 */

	public function __toString() {
		return "{$this->x}:{$this->y}";
	}

}
