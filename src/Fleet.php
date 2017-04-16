<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Fleet extends Model {

	static protected $table = 'fleets';

	/**
	 * Getters
	 */

	public function get_name() {
		global $FLEETNAMES;
		return $FLEETNAMES[$this->fleetname];
	}

	public function get_total_ships() {
		return Unit::countReduce($this->ships, 'planet_amount');
	}

	public function get_ships() {
		global $db;
		return $db->fetch('
			SELECT u.*, f.amount AS planet_amount
			FROM d_all_units u
			JOIN planet_r_d rd ON rd.r_d_id = u.r_d_required_id AND rd.planet_id = ? AND eta = 0
			JOIN ships_in_fleets f ON f.unit_id = u.id AND f.fleet_id = ?
		', [
			'params' => [$this->owner_planet_id, $this->id],
			'class' => Unit::class,
		])->all();
	}

	public function get_destination_planet() {
		if ( $this->destination_planet_id ) {
			return Planet::find($this->destination_planet_id);
		}
	}

}
