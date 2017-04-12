<?php

namespace rdx\ps;

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

	static protected $table = 'planets';

	/**
	 * Getters
	 */

	public function get_doing_rd() {
		global $db;
		return $db->select_fields('planet_r_d', 'r_d_id, r_d_id', 'eta > 0 AND planet_id = ?', [$this->id]);
	}

	public function get_finished_rd() {
		global $db;
		return $db->select_fields('planet_r_d', 'r_d_id, r_d_id', 'eta = 0 AND planet_id = ?', [$this->id]);
	}

	public function get_fleets() {
		return Fleet::all('owner_planet_id = ?', [$this->id]);
	}

	public function get_total_asteroids() {
		return array_reduce($this->resources, function($total, $resource) {
			return $total + $resource->asteroids;
		}, $this->inactive_asteroids);
	}

	public function get_total_defences() {
		return array_reduce($this->defences, function($total, $unit) {
			return $total + $unit->planet_amount;
		}, 0);
	}

	public function get_defences() {

		// @todo Filter on R & D

		global $db;
		return $db->fetch('
			SELECT a.*, amount AS planet_amount
			FROM d_all_units a
			LEFT JOIN defence_on_planets p ON p.defence_id = a.id AND p.planet_id = ?
			WHERE a.T = ?
		', [
			'params' => [$this->id, 'defence'],
			'class' => Unit::class,
		])->all();
	}

	public function get_total_ships() {
		return array_reduce($this->ships, function($total, $unit) {
			return $total + $unit->planet_amount;
		}, 0);
	}

	public function get_ships() {

		// @todo Filter on R & D

		global $db;
		return $db->fetch('
			SELECT a.*, SUM(s.amount) AS planet_amount
			FROM d_all_units a
			LEFT JOIN ships_in_fleets s ON s.ship_id = a.id
			LEFT JOIN fleets f ON f.id = s.fleet_id AND f.owner_planet_id = ?
			WHERE a.T = ?
			GROUP BY a.id
		', [
			'params' => [$this->id, 'ship'],
			'class' => Unit::class,
		])->all();
	}

	public function get_skills() {
		global $db;
		return $db->fetch_by_field('
			SELECT a.*, p.value AS planet_value
			FROM d_skills a
			LEFT JOIN planet_skills p ON a.id = p.skill_id AND p.planet_id = ?
		', 'id', [
			'params' => [$this->id],
			'class' => Skill::class,
		])->all();
	}

	public function get_resources() {
		global $db;
		return $db->select('d_resources c, planet_resources p', 'c.id = p.resource_id AND p.planet_id = ?', [$this->id], ['class' => Resource::class])->all();
	}

	public function get_ranked() {
		global $db;
		return $db->count('planets', 'score > ?', [$this->score]) + 1;
	}

	public function get_researching() {
		return $this->getRD('r');
	}

	public function get_constructing() {
		return $this->getRD('d');
	}

	public function get_coordinates() {
		return [$this->galaxy->x, $this->galaxy->y, $this->galaxy->z];
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
		return Mail::all(['to_planet_id' => $this->id, 'seen' => 0]);
	}

	public function get_new_news() {
		return News::all(['planet_id' => $this->id, 'seen' => 0]);
	}

	public function get_galaxy() {
		return Galaxy::find($this->galaxy_id);
	}

	public function get_ticker() {
		return new PlanetTicker(Ticker::instance(), $this);
	}

	/**
	 * Logic
	 */

	public function canRD( $type, $id ) {
		$doing = $this->getRD($type);
		if ( $doing ) {
			return;
		}

		$available = $this->getAvailableRD($type);
		foreach ( $available as $rd ) {
			if ( $rd->id == $id ) {
				return $rd->planet_eta === null ? $rd : null;
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

		$available = array_filter($all, function($rd) use ($type) {
			return $rd->planetHasRequireds($this) && !$rd->planetHasExcludeds($this);
		});

		// @todo Skills
		// @todo Race

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

	public function addResource( $rid, $amount ) {
		global $db;
		return $db->update('planet_resources', 'amount = amount + ' . (int)$amount, [
			'planet_id' => $this->id,
			'resource_id' => $rid,
		]);
	}

	public function checkPassword( $password ) {
		return password_verify($password, $this->password);
	}

	public function __toString() {
		return "{$this->rulername} of {$this->planetname}";
	}

	/**
	 * Overrides
	 */

	protected function presave( array &$data ) {
		if ( isset($data['password']) ) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		}
	}

}
