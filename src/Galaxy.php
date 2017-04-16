<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Galaxy extends Model {

	static protected $table = 'galaxies';

	/**
	 * Getters
	 */

	public function get_total_asteroids() {
		return array_reduce($this->planets, function($total, $planet) {
			return $total + $planet->total_asteroids;
		}, 0);
	}

	public function get_score() {
		return array_reduce($this->planets, function($total, $planet) {
			return $total + $planet->score;
		}, 0);
	}

	public function get_coordinates() {
		return [$this->x, $this->y];
	}

	public function get_planets() {
		global $db;
		return $db->select_by_field('planets', 'id', 'galaxy_id = ? ORDER BY z', [$this->id], ['class' => Planet::class])->all();
	}

	public function get_gc() {
		if ( $this->gc_planet_id ) {
			return Planet::find($this->gc_planet_id);
		}
	}

	public function get_mow() {
		if ( $this->mow_planet_id ) {
			return Planet::find($this->mow_planet_id);
		}
	}

	public function get_moc() {
		if ( $this->moc_planet_id ) {
			return Planet::find($this->moc_planet_id);
		}
	}

	public function get_mof() {
		if ( $this->mof_planet_id ) {
			return Planet::find($this->mof_planet_id);
		}
	}

	/**
	 * Logic
	 */

	public function __toString() {
		return "{$this->x}:{$this->y}";
	}

	/**
	 * Static
	 */

	static public function fromCoordinates( $x, $y ) {
		return self::first(['x' => $x, 'y' => $y]);
	}

}
