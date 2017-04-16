<?php

namespace rdx\ps;

class Alliance extends Model {

	static protected $table = 'alliances';

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

	public function get_planets() {
		global $db;
		return $db->select_by_field('planets', 'id', 'alliance_id = ? ORDER BY z', [$this->id], ['class' => Planet::class])->all();
	}

	/**
	 * Logic
	 */

	public function checkPassword( $password ) {
		return password_verify($password, $this->pwd);
	}

	public function __toString() {
		return $this->name;
	}

}
