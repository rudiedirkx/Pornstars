<?php

namespace rdx\ps;

use rdx\ps\Planet;

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

			$results = $db->select('d_r_d_results', '1 ORDER BY o');

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

	static public function instance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
