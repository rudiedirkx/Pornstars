<?php

namespace rdx\ps;

use NotEnoughException;
use rdx\ps\Alliance;
use rdx\ps\Fleet;
use rdx\ps\Galaxy;
use rdx\ps\Mail;
use rdx\ps\News;
use rdx\ps\PlanetTicker;
use rdx\ps\ResearchDevelopment;
use rdx\ps\Resource;
use rdx\ps\Skill;
use rdx\ps\Ticker;
use rdx\ps\Unit;

class Planet extends Model {

	const NEW_PLANET_ASTEROIDS = 3;
	const NEW_PLANET_RESOURCES = 2000;

	static protected $table = 'planets';

	/**
	 * Getters
	 */

	public function get_power_amount() {
		return (float) $this->power_resource->amount;
	}

	public function get_power_resource() {
		foreach ( $this->resources as $resource ) {
			if ( $resource->is_power ) {
				return $resource;
			}
		}
	}

	public function get_next_asteroid_costs() {
		return nextRoidCosts($this->total_asteroids);
	}

	public function get_alliance_tag() {
		if ( $this->alliance ) {
			return $this->alliance->tag;
		}
	}

	public function get_alliance() {
		if ( $this->alliance_id ) {
			return $this->alliance = Alliance::find($this->alliance_id);
		}
	}

	public function get_galaxy_roles() {
		$roles = [];
		$this->galaxy->gc_planet_id == $this->id and array_push($roles, 'gc');
		$this->galaxy->moc_planet_id == $this->id and array_push($roles, 'moc');
		$this->galaxy->mow_planet_id == $this->id and array_push($roles, 'mow');
		$this->galaxy->mof_planet_id == $this->id and array_push($roles, 'mof');
		return $roles;
	}

	public function get_votes_for_gc() {
		global $db;
		return $db->count('planets', 'galaxy_id = ? AND voted_for_planet_id = ?', [$this->galaxy_id, $this->id]);
	}

	public function get_asteroid_scans() {
		foreach ( $this->waves as $wave ) {
			if ( $wave->T == 'roidscan' ) {
				return $wave;
			}
		}
	}

	public function get_num_asteroid_scans() {
		if ( $this->asteroid_scans ) {
			return (int) $this->asteroid_scans->planet_amount;
		}

		return 0;
	}

	public function get_wave_amps() {
		foreach ( $this->waves as $wave ) {
			if ( $wave->T == 'amp' ) {
				return (int) $wave->planet_amount;
			}
		}

		return 0;
	}

	public function get_wave_blockers() {
		foreach ( $this->waves as $wave ) {
			if ( $wave->T == 'blocker' ) {
				return (int) $wave->planet_amount;
			}
		}

		return 0;
	}

	public function get_training_skills() {
		global $db;
		return $this->training_skills = $db->select_fields('skill_training', 'skill_id, skill_id', 'eta > 0 AND planet_id = ?', [$this->id]);
	}

	public function get_doing_rd_ids() {
		global $db;
		return $this->doing_rd_ids = $db->select_fields('planet_r_d', 'r_d_id, r_d_id', 'eta > 0 AND planet_id = ?', [$this->id]);
	}

	public function get_finished_rd_ids() {
		global $db;
		return $this->finished_rd_ids = $db->select_fields('planet_r_d', 'r_d_id, r_d_id', 'eta = 0 AND planet_id = ?', [$this->id]);
	}

	public function get_num_fleets() {
		global $NUM_OUTGOING_FLEETS;
		return $this->num_fleets = $this->ticker->rdResultFleets($NUM_OUTGOING_FLEETS);
	}

	public function get_fleets() {
		$fleets = Fleet::all('owner_planet_id = ? AND fleetname <= ? ORDER BY fleetname', [$this->id, $this->num_fleets]);
		return $this->fleets = array_reduce($fleets, function($fleets, $fleet) {
			return $fleets + [$fleet->fleetname => $fleet];
		}, []);
	}

	public function get_total_asteroids() {
		return array_reduce($this->resources, function($total, $resource) {
			return $total + $resource->asteroids;
		}, $this->inactive_asteroids);
	}

	public function get_total_defences() {
		return Unit::countReduce($this->defences, 'planet_amount');
	}

	public function get_defences() {
		return $this->defence = $this->getUnits('defence');
	}

	public function get_waves() {
		return $this->waves = $this->getUnits('wave');
	}

	public function get_power() {
		return $this->power = $this->getUnits('power');
	}

	public function get_total_ships() {
		return Unit::countReduce($this->ships, 'planet_amount');
	}

	public function get_ships() {
		global $db;
		return $this->ships = $db->fetch_by_field('
			SELECT a.*, SUM(s.amount) AS planet_amount
			FROM d_all_units a
			JOIN planet_r_d rd ON rd.r_d_id = a.r_d_required_id AND rd.planet_id = ? AND eta = 0
			LEFT JOIN ships_in_fleets s ON s.unit_id = a.id
			LEFT JOIN fleets f ON f.id = s.fleet_id AND f.owner_planet_id = rd.planet_id
			WHERE a.T = ?
			GROUP BY a.id
			ORDER BY a.o
		', 'id', [
			'params' => [$this->id, 'ship'],
			'class' => Unit::class,
		])->all();
	}

	public function get_skills() {
		global $db;
		return $this->skills = $db->fetch_by_field('
			SELECT a.*, p.value AS planet_value, t.eta AS planet_eta
			FROM d_skills a
			LEFT JOIN planet_skills p ON a.id = p.skill_id AND p.planet_id = ?
			LEFT JOIN skill_training t ON t.skill_id = a.id AND t.planet_id = ?
		', 'id', [
			'params' => [$this->id, $this->id],
			'class' => Skill::class,
		])->all();
	}

	public function get_resources() {
		global $db;
		return $this->resources = $db->select_by_field('d_resources c, planet_resources p', 'id', 'c.id = p.resource_id AND p.planet_id = ?', [$this->id], ['class' => Resource::class])->all();
	}

	public function get_rankedth() {
		$suffix = ['th', 'st', 'nd', 'rd'];
		$rank = $this->ranked;
		$suffix = $suffix[substr($rank, -1)] ?? 'th';
		return "$rank$suffix";
	}

	public function get_ranked() {
		global $db;
		return $this->ranked = $db->count('planets', 'score > ?', [$this->score]) + 1;
	}

	public function get_researching() {
		return $this->researching = $this->getRD('r');
	}

	public function get_constructing() {
		return $this->constructing = $this->getRD('d');
	}

	public function get_coordinates() {
		return [$this->galaxy->x, $this->galaxy->y, $this->z];
	}

	public function get_pretty_coordinates() {
		return implode(':', $this->coordinates);
	}

	public function get_x() {
		return $this->galaxy->x;
	}

	public function get_y() {
		return $this->galaxy->y;
	}

	public function get_z() {
		return $this->galaxy->z;
	}

	public function get_new_mail() {
		return $this->new_mail = Mail::all(['to_planet_id' => $this->id, 'seen' => 0]);
	}

	public function get_new_news() {
		return $this->new_news = News::all(['planet_id' => $this->id, 'seen' => 0]);
	}

	public function get_galaxy() {
		return Galaxy::find($this->galaxy_id);
	}

	public function get_ticker() {
		return $this->ticker = new PlanetTicker(Ticker::instance(), $this);
	}

	/**
	 * Logic
	 */

	public function hasSkillsForRD(ResearchDevelopment $rd) {
		$have = array_column($this->skills, 'planet_value', 'id');

		foreach ($rd->requires_skills as $skill) {
			if ($skill->required_value > $have[$skill->skill_id]) {
				return false;
			}
		}

		return true;
	}

	public function maxPowerForAsteroids() {
		$this->__reget('resources');

		$haveRoids = $this->total_asteroids;
		$havePower = $this->power_amount;

		$needPower = 0;
		$newRoids = 0;
		while ( $havePower > $needPower ) {
			$newRoids++;
			$needPower += nextRoidCosts($haveRoids + $newRoids);
		}

		return $newRoids - 1;
	}

	public function maxResourcesFor( array $costs ) {
		$have = array_column($this->__reget('resources'), 'amount', 'id');
		$max = -1;

		foreach ( $costs as $rid => $amount ) {
			$can = floor($have[$rid] / $amount);
			$max = $max == -1 ? $can : min($max, $can);
		}

		return $max;
	}

	public function takeTransaction( callable $transaction, $rethrow = true ) {
		global $db;

		try {
			$db->begin();
			call_user_func($transaction, $this);
			$db->commit();
			return true;
		}
		catch ( Exception $ex ) {
			$db->rollback();
			if ( $rethrow ) {
				throw $ex;
			}

			return false;
		}
	}

	/**
	 * @throws NotEnoughException
	 */
	public function takeWaves( $id, $amount ) {
		global $db;

		$db->update('waves_on_planets', "amount = amount - $amount", [
			'unit_id' => $id,
			'planet_id' => $this->id,
			"amount >= $amount",
		]);
		if ( $db->affected_rows() < 1 ) {
			$resource = Unit::find($rid);
			throw new NotEnoughException("$resource");
		}

		$this->__reget('waves');
		$this->__reget('asteroid_scans');
		$this->__reget('num_asteroid_scans');
	}

	/**
	 * @throws NotEnoughException
	 */
	public function takeResources( array $resources, $quantity = 1 ) {
		global $db;

		foreach ( $resources as $rid => $amount ) {
			$amount *= $quantity;
			$db->update('planet_resources', "amount = amount - $amount", [
				'resource_id' => $rid,
				'planet_id' => $this->id,
				"amount >= $amount",
			]);
			if ( $db->affected_rows() < 1 ) {
				$resource = Resource::find($rid);
				throw new NotEnoughException("$resource");
			}
		}

		$this->__reget('resources');
	}

	/**
	 * @throws NotEnoughException
	 */
	public function takeProperties( array $properties ) {
		global $db;

		$updates = [];
		$conditions = ['id' => $this->id];
		foreach ( $properties as $property => $amount ) {
			$updates[] = "$property = $property - $amount";
			$conditions[] = "$property >= $amount";
		}

		$db->update(self::$table, $updates, $conditions);
		if ( $db->affected_rows() < 1 ) {
			throw new NotEnoughException(implode('/', array_keys($properties)));
		}

		$this->reload();
	}

	public function activateAsteroidsCosts( $amount ) {
		$have = $this->total_asteroids;

		$costs = 0;
		for ( $i = 1; $i <= $amount; $i++ ) {
			$costs += nextRoidCosts($have + $i);
		}

		return $costs;
	}

	public function createFleetScanReport( Planet $scanner ) {
		$fleets = $this->fleets;
		return array_reduce($this->fleets, function($list, $fleet) {
			return $list + [$fleet->name => array_reduce($fleet->ships, function($list, $unit) {
				return $list + [$unit->unit_plural => nummertje($unit->planet_amount)];
			}, [])];
		}, []);
	}

	public function createDefenceScanReport( Planet $scanner ) {
		// All their defences
		$defences = $this->defences;

		// Only the types we know exist
		$defences = array_intersect_key($defences, $scanner->defences);
		$defences = array_filter($defences, Unit::stealthFilter());

		return array_reduce($defences, function($list, $unit) {
			return $list + [$unit->unit_plural => nummertje($unit->planet_amount)];
		}, []);
	}

	public function createUnitScanReport( Planet $scanner ) {
		// All their ships
		$ships = $this->ships;

		// Only the types we know exist
		$ships = array_intersect_key($ships, $scanner->ships);
		$ships = array_filter($ships, Unit::stealthFilter());

		return array_reduce($ships, function($list, $unit) {
			return $list + [$unit->unit_plural => nummertje($unit->planet_amount)];
		}, []);
	}

	public function createSectorScanReport( Planet $scanner ) {
		$ships = array_filter($this->ships, Unit::stealthFilter());
		$defences = array_filter($this->defences, Unit::stealthFilter());

		return [
			'Score' => nummertje($this->score),
			'Asteroids' => nummertje($this->total_asteroids),
		] + array_reduce($this->resources, function($list, $resource) {
				return $list + [$resource->resource . ' asteroids' => nummertje($resource->asteroids)];
			}, [])
		+ [
			'Resources' => nummertje(Unit::countReduce($this->resources, 'amount')),
		] + array_reduce($this->resources, function($list, $resource) {
				return $list + [$resource->resource => nummertje($resource->amount)];
			}, [])
		+ [
			'Ships' => nummertje(Unit::countReduce($ships, 'planet_amount')),
			'Defences' => nummertje(Unit::countReduce($defences, 'planet_amount')),
		];
	}

	public function createScanReport( Unit $scan, Planet $scanner ) {
		$function = "create{$scan->subtype}Report";
		if ( is_callable($method = [$this, $function]) ) {
			return call_user_func($method, $scanner);
		}
	}

	public function getUnits( $baseType ) {
		global $db;

		$table = Unit::$planetTables[$baseType];
		$types = Unit::baseToTypes($baseType);

		return $db->fetch_by_field("
			SELECT a.*, p.amount AS planet_amount
			FROM d_all_units a
			JOIN planet_r_d rd ON rd.r_d_id = a.r_d_required_id AND rd.planet_id = ? AND eta = 0
			LEFT JOIN {$table} p ON p.unit_id = a.id AND p.planet_id = rd.planet_id
			WHERE a.T IN (?)
		", 'id', [
			'params' => [$this->id, $types],
			'class' => Unit::class,
		])->all();
	}

	public function canRD( $type, $id ) {
		$doing = $this->getRD($type);
		if ( $doing ) {
			return;
		}

		$available = $this->getAvailableRD($type);
		foreach ( $available as $rd ) {
			if ( $rd->id == $id ) {
				if ( $rd->planet_eta !== null ) return;
				if ( !$this->hasSkillsForRD($rd) ) return;
				return $rd;
			}
		}
	}

	public function getAvailableRD( $type ) {
		global $db;

		$all = $db->fetch('
			SELECT a.*, p.eta AS planet_eta
			FROM d_r_d_available a
			LEFT JOIN planet_r_d p ON a.id = p.r_d_id AND p.planet_id = ?
			WHERE a.T = ?
		', [
			'params' => [$this->id, $type],
			'class' => ResearchDevelopment::class,
		])->all();

		$available = array_filter($all, function($rd) {
			return $rd->planet_eta !== null || ($rd->planetHasRequireds($this) && !$rd->planetHasExcludeds($this));
		});

		// @todo Skills

		usort($available, function($a, $b) {
			return $a->order_score - $b->order_score;
		});

		return $available;
	}

	public function getRD( $type ) {
		global $db;
		return $db->fetch('
			SELECT a.*, p.eta AS planet_eta
			FROM d_r_d_available a
			JOIN planet_r_d p ON a.id = p.r_d_id AND p.eta > 0 AND a.T = ? AND p.planet_id = ?
		', [
			'params' => [$type, $this->id],
			'class' => ResearchDevelopment::class,
		])->first();
	}

	public function checkPassword( $password ) {
		return password_verify($password, $this->password);
	}

	public function __toString() {
		return "{$this->rulername} of {$this->planetname}";
	}

	/**
	 * Static
	 */

	static public function create( array $data ) {
		global $db, $g_prefs;

		list($galaxy, $z) = self::findAvailableCoordinates();

		$insert = [
			'email' => $data['email'],
			'password' => $data['password'],
			'rulername' => $data['rulername'],
			'planetname' => $data['planetname'],
			// 'race_id' => $data['race'],
			'activationcode' => rand_string(),
			'z' => $z,
			'galaxy_id' => $galaxy->id,
			'inactive_asteroids' => self::NEW_PLANET_ASTEROIDS,
			'journal' => '',
		];

		// Create planet
		$id = self::insert($insert);
		$planet = self::find($id);

		// Planet resources
		$db->query('
			INSERT INTO planet_resources (planet_id, resource_id, amount)
				SELECT ?, id, ? FROM d_resources
		', [$id, self::NEW_PLANET_RESOURCES]);

		// Planet skills
		$db->query('
			INSERT INTO planet_skills (planet_id, skill_id)
				SELECT ?, id FROM d_skills
		', [$id]);

		// Planet defences
		$db->query('
			INSERT INTO defence_on_planets (planet_id, unit_id)
				SELECT ?, id FROM d_defence
		', [$id]);

		// Planet power
		$db->query('
			INSERT INTO power_on_planets (planet_id, unit_id)
				SELECT ?, id FROM d_power
		', [$id]);

		// Planet waves
		$db->query('
			INSERT INTO waves_on_planets (planet_id, unit_id)
				SELECT ?, id FROM d_waves
		', [$id]);

		// Planet fleets
		foreach ( $g_prefs->fleets as $index => $name ) {
			$db->insert('fleets', ['owner_planet_id' => $id, 'fleetname' => $index]);
		}

		// Ships in fleets
		$db->query('
			INSERT INTO ships_in_fleets (fleet_id, unit_id)
				SELECT f.id, u.id FROM fleets f, d_ships u WHERE f.owner_planet_id = ?
		', [$id]);
	}

	static public function findAvailableCoordinates() {
		global $db, $g_prefs;

		$coords = $db->fetch('
			SELECT g.x, g.y, p.z
			FROM planets p, galaxies g
			WHERE g.id = p.galaxy_id
		')->all();
		$coords = array_map(function($planet) {
			return "{$planet->x}-{$planet->y}-{$planet->z}";
		}, $coords);

		for ( $x = 1; $x < 100; $x++ ) {
			for ( $y = 1; $y < 100; $y++ ) {
				for ( $z = 1; $z <= $g_prefs->planets_per_galaxy; $z++ ) {
					$coord = "{$x}-{$y}-{$z}";
					if ( !in_array($coord, $coords) ) {
						$galaxy = Galaxy::fromCoordinates($x, $y) ?: Galaxy::create($x, $y);
						return [$galaxy, $z];
					}
				}
			}
		}
	}

	static public function fromCoordinates( $x, $y, $z ) {
		if ( $galaxy = Galaxy::fromCoordinates($x, $y) ) {
			return self::first(['galaxy_id' => $galaxy->id, 'z' => $z]);
		}
	}

	/**
	 * Overrides
	 */

	static protected function presave( array &$data ) {
		if ( isset($data['password']) ) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		}
	}

}
