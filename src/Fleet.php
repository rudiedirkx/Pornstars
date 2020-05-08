<?php

namespace rdx\ps;

use FleetMissionException;
use rdx\ps\Planet;

class Fleet extends Model {

	const ETA_GALAXY = 5;
	const ETA_CLUSTER = 10;
	const ETA_UNIVERSE = 15;

	static protected $table = 'fleets';

	/**
	 * Static
	 */

	static public function planetDistanceEta( Planet $from, Planet $to ) {
		if ( $from->galaxy_id == $to->galaxy_id ) {
			return self::ETA_GALAXY;
		}

		if ( $from->x == $to->x ) {
			return self::ETA_CLUSTER;
		}

		return self::ETA_UNIVERSE;
	}

	/**
	 * Getters
	 */

	public function get_is_working() {
		return $this->travel_eta == 0 && $this->action_eta > 0;
	}

	public function get_is_home() {
		return !$this->fleetname || ( !$this->action && !$this->travel_eta );
	}

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
		return $this->getTotalShips();
	}

	public function get_ships() {
		global $db;
		return $this->ships = $db->fetch_by_field('
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

	public function calculateEtaTo( Planet $destination ) {
		$distanceEta = self::planetDistanceEta($this->owner_planet, $destination);
		$shipEta = $this->ships_eta;
		return $this->owner_planet->ticker->rdResultTravelEta($distanceEta + $shipEta);
	}

	public function getTotalShips() {
		return Unit::countReduce($this->ships, 'planet_amount');
	}

	public function validateReturn( array &$mission ) {
		// Always fine?
	}

	public function executeReturn( array $mission ) {
		$travelEta = max(1, $this->calculateEtaTo($this->destination_planet) - $this->travel_eta);

		// Return fleet
		$this->update([
			'action' => 'return',
			'travel_eta' => $travelEta,
			'action_eta' => 0,
			// 'activated' => 0,
		]);

		return "Turned around fleet $this";
	}

	public function validateDestroy( array &$mission ) {
		// Always fine?
	}

	public function executeDestroy( array $mission ) {
		global $db;

		// Destroy all ships
		$db->update('ships_in_fleets', ['amount' => 0], ['fleet_id' => $this->id]);

		// Reset fleet
		$this->update([
			'action' => null,
			'travel_eta' => 0,
			'action_eta' => 0,
			'destination_planet_id' => null,
			// 'activated' => 0,
		]);

		return "Destroyed fleet $this";
	}

	public function validateAttack( array &$mission ) {
		$this->validateMoveTroops($mission);

		// @todo Check newbie status & score difference
	}

	public function executeAttack( array $mission ) {
		$this->executeMoveTroops($mission);
		$destination = $mission['destination'];
		return "Fleet $this is now attacking $destination ($destination->pretty_coordinates)";
	}

	public function validateDefend( array &$mission ) {
		$this->validateMoveTroops($mission);
	}

	public function executeDefend( array $mission ) {
		$this->executeMoveTroops($mission);
		$destination = $mission['destination'];
		return "Fleet $this is now defending $destination ($destination->pretty_coordinates)";
	}

	protected function validateMoveTroops( array &$mission ) {
		$destination = Planet::fromCoordinates($mission['x'], $mission['y'], $mission['z']);
		if ( !$destination || $destination->id == $this->owner_planet_id ) {
			throw new FleetMissionException($this, "Invalid destination");
		}

		if ( empty($mission['ticks']) ) {
			throw new FleetMissionException($this, "You must enter an action time");
		}

		$mission['destination'] = $destination;
	}

	protected function executeMoveTroops( array $mission ) {
		$destination = $mission['destination'];
		$eta = $this->calculateEtaTo($destination);

		$powerUsage = $this->owner_planet->ticker->rdResultTravelCosts($this->ships_power);
		$this->owner_planet->takeResources([$this->owner_planet->power_resource->id => $powerUsage]);

		$this->update([
			'action' => $mission['action'],
			'destination_planet_id' => $destination->id,
			'travel_eta' => $eta,
			'action_eta' => $mission['ticks'],
			'activated' => 0,
		]);
	}

	public function moveShips( $shipId, $amount, self $to ) {
		global $db;

		if ( !isint($amount) || $amount < 1 || $amount > $this->ships[$shipId]->planet_amount ) {
			$amount = $this->ships[$shipId]->planet_amount;
		}

		$db->update('ships_in_fleets', "amount = amount - $amount", [
			'fleet_id' => $this->id,
			'unit_id' => $shipId,
			"amount >= $amount",
		]);
		if ( $db->affected_rows() > 0 ) {
			$db->update('ships_in_fleets', "amount = amount + $amount", [
				'fleet_id' => $to->id,
				'unit_id' => $shipId,
			]);
		}
	}

	public function __toString() {
		return $this->name;
	}

}
