<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Fleet extends Model {

	static protected $table = 'fleets';

	/**
	 * Getters
	 */

	public function get_available_actions() {
		$actions = [];
		if ( $this->action ) {
			$actions['destroy'] = 'Destroy';

			if ( $this->action != 'return' ) {
				$actions['return'] = 'Return';
			}
		}

		if ( !$this->action && $this->fleetname ) {
			$actions['attack'] = 'Attack';
			$actions['defend'] = 'Defend';
		}

		return $actions;
	}

	public function get_ships_eta() {
		return array_reduce($this->ships, function($eta, $ship) {
			return $ship->planet_amount ? max($eta, $ship->move_eta) : $eta;
		}, 0);
	}

	public function get_ships_power() {
		return array_reduce($this->ships, function($power, $ship) {
			return $power + $ship->planet_amount * $ship->power;
		}, 0);
	}

	public function get_name() {
		global $FLEETNAMES;
		return $FLEETNAMES[$this->fleetname];
	}

	public function get_total_ships() {
		return Unit::countReduce($this->ships, 'planet_amount');
	}

	public function get_ships() {
		global $db;
		return $db->fetch_by_field('
			SELECT u.*, f.amount AS planet_amount
			FROM d_all_units u
			JOIN planet_r_d rd ON rd.r_d_id = u.r_d_required_id AND rd.planet_id = ? AND eta = 0
			JOIN ships_in_fleets f ON f.unit_id = u.id AND f.fleet_id = ?
		', 'id', [
			'params' => [$this->owner_planet_id, $this->id],
			'class' => Unit::class,
		])->all();
	}

	public function get_destination_planet() {
		if ( $this->destination_planet_id ) {
			return Planet::find($this->destination_planet_id);
		}
	}

	public function get_owner_planet() {
		return Planet::find($this->owner_planet_id);
	}

	/**
	 * Logic
	 */

	public function __toString() {
		return 'Fleet [' . $this->fleetname . ']';
	}

}
