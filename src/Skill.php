<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Skill extends Model {

	static protected $table = 'd_skills';

	/**
	 * Getters
	 */

	public function get_pct_done() {
		if ( $this->planet_eta !== null ) {
			return round(($this->eta - $this->planet_eta) / $this->eta * 100);
		}
	}

	public function get_status() {
		return $this->is_doing ? 'doing' : 'available';
	}

	public function get_is_doing() {
		return $this->planet_eta !== null && $this->planet_eta > 0;
	}

	public function get_eta() {
		return pow(5 + (int) $this->planet_value, 2);
	}

	/**
	 * Logic
	 */

	public function train( Planet $planet ) {
		global $db;

		$db->insert('skill_training', [
			'planet_id' => $planet->id,
			'skill_id' => $this->id,
			'eta' => $this->eta,
		]);
	}

}
