<?php

namespace rdx\ps;

class Ticker {

	static $instance;

	protected $rd_results;
	protected $power_map;

	public $planets;
	public $units;

	public function setPlanets( array $planets ) {
		$this->planets = $planets;

		// @todo Preload R & D
		// @todo Preload Power
	}

	public function setUnits( array $units ) {
		$this->units = $units;
	}

	public function getPlanet( $id ) {
		return $this->planets[$id];
	}

	public function getUnit( $id ) {
		return $this->units[$id];
	}

	public function getPowerMap() {
		if ( !$this->power_map ) {
			global $db;

			$this->power_map = $db->select_fields('d_all_units', 'id, power', 'T = ?', ['power']);
		}

		return $this->power_map;
	}

	public function getRDResultsByTypeAndRD( $type, array $rd ) {
		if ( !$this->rd_results ) {
			global $db;

			$results = $db->select('d_r_d_results', "enabled = '1' ORDER BY o");

			$this->rd_results = [];
			foreach ( $results as $result ) {
				$this->rd_results[$result->type][$result->done_r_d_id][] = $result;
			}
		}

		if ( isset($this->rd_results[$type]) ) {
			$results = [];
			foreach ( $this->rd_results[$type] as $rdId => $typeResults ) {
				if ( isset($rd[$rdId]) ) {
					$results = array_merge($results, $typeResults);
				}
			}

			return $results;
		}

		return [];
	}

	public function updateScores() {
		global $db;

		$db->execute('
			UPDATE planets
			SET score =
				0.01 * (			/*SHIPS IN FLEETS*/
					SELECT
						IFNULL(SUM(
							s.amount *
							(
								SELECT
									(SELECT SUM(amount) FROM d_unit_costs WHERE unit_id = u.id)
								FROM
									d_all_units u
								WHERE
									s.unit_id = u.id
							)
						),0)
					FROM
						ships_in_fleets s,
						fleets f
					WHERE
						s.fleet_id = f.id AND
						f.owner_planet_id = planets.id
				) +
				0.01 * (				/*DEFENCE ON PLANETS*/
					SELECT
						IFNULL(SUM(
							dop.amount *
							(
								SELECT
									(SELECT SUM(amount) FROM d_unit_costs WHERE unit_id = u.id)
								FROM
									d_all_units u
								WHERE
									dop.unit_id = u.id
							)
						),0)
					FROM
						defence_on_planets dop
					WHERE
						dop.planet_id = planets.id
				) +
				0.01 * (				/*WAVES ON PLANETS*/
					SELECT
						IFNULL(SUM(
							wop.amount *
							(
								SELECT
									(SELECT SUM(amount) FROM d_unit_costs WHERE unit_id = u.id)
								FROM
									d_all_units u
								WHERE
									wop.unit_id = u.id
							)
						),0)
					FROM
						waves_on_planets wop
					WHERE
						wop.planet_id = planets.id
				) +
				150 * (SELECT SUM(asteroids) FROM planet_resources WHERE planet_id = planets.id) +
				ROUND( 0.002 * (SELECT SUM(amount) FROM planet_resources WHERE planet_id = planets.id) );
		');
	}

	public function makeBattles( array &$fleets ) {
		$battles = [];
		foreach ( $fleets as $i => $fleet ) {
			if ( $fleet->travel_eta == 0 && $fleet->action_eta > 0 && $fleet->action != 'return' ) {
				isset($battles[$fleet->destination_planet_id]) or $battles[$fleet->destination_planet_id] = new Battle($fleet->destination_planet);
				$battles[$fleet->destination_planet_id]->addFleet($fleet);
				unset($fleets[$i]);
			}
		}

		return $battles;
	}

	public function fightBattle( Battle $battle ) {
echo "BATTLE AT $battle->location\n";
		$returning = [];
		foreach ([$battle->defending, $battle->attacking] as $fleets) {
			foreach ($fleets as $fleet) {
echo "- [$fleet->action] $fleet->owner_planet's $fleet (".($fleet->action_eta-1)." left)\n";
				$fleet->update([
					'action_eta' => $fleet->action_eta - 1,
				]);

				// @todo Fight?

				if ($fleet->getTotalShips() == 0) {
echo "-- ^ has been destroyed\n";
					$fleet->update([
						'action' => null,
						'travel_eta' => 0,
						'action_eta' => 0,
						'destination_planet_id' => null,
					]);
				}
				elseif ($fleet->action_eta == 0) {
echo "-- ^ is done & returning\n";
					$fleet->update([
						'action' => 'return',
						'travel_eta' => $fleet->calculateEtaTo($fleet->destination_planet),
					]);
				}
			}
		}
echo "\n";
	}

	public function moveFleets( array $fleets ) {
		foreach ( $fleets as $fleet ) {
			if ( $fleet->travel_eta == 1 && $fleet->action == 'return' ) {
echo "$fleet->owner_planet's $fleet returned home\n";
				$fleet->update([
					'travel_eta' => 0,
					'action_eta' => 0,
					'action' => null,
					'destination_planet_id' => null,
				]);
			}
			elseif ( $fleet->travel_eta > 0 ) {
echo "$fleet->owner_planet's $fleet traveled to $fleet->action (".($fleet->travel_eta-1)." left)\n";
				$fleet->update([
					'travel_eta' => $fleet->travel_eta - 1,
				]);
			}
		}
	}

	static public function instance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
