<?php

namespace rdx\ps;

use rdx\ps\Planet;

class RequiredSkill extends Model {

	static protected $table = 'd_skills_per_r_d';

	/**
	 * Getters
	 */

	public function get_r_d() {
		return ResearchDevelopment::find($this->r_d_id);
	}

	public function get_skill() {
		return Skill::find($this->skill_id);
	}

	/**
	 * Logic
	 */

	public function __toString() {
		return "$this->skill $this->required_value";
	}

}
