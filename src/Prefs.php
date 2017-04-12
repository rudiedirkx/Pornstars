<?php

namespace rdx\ps;

class Prefs extends Model {

	static protected $table = 'prefs';

	public function get_admins() {
		return explode(',', $this->admin_planet_ids);
	}

}
