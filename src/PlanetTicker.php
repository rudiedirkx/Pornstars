<?php

namespace rdx\ps;

use rdx\ps\Planet;
use rdx\ps\Resource;

class PlanetTicker {

	protected $planet;

	public function __construct( Ticker $ticker, Planet $planet ) {
		$this->ticker = $ticker;
		$this->planet = $planet;
	}

	public function getIncome( Resource $resource ) {
		if ( $resource->is_power ) {
			$income = $this->powerIncome();
		}
		else {
			$income = $this->asteroidIncome($resource);
		}

		$income = $this->rdResultIncome($resource, $income);
		return $income;
	}

	public function addResource( Resource $resource ) {
		$income = $this->getIncome($resource);

		if ( $income > 0 ) {
			$this->planet->addResource($resource->id, $income);
		}
	}

	public function powerIncome() {
		global $db;

		$powerMap = $this->ticker->getPowerMap();
		$planetPower = $db->select_fields('power_on_planets', 'power_id, amount', 'planet_id = ?', [$this->planet->id]);

		$power = 0;
		foreach ( $planetPower as $id => $amount ) {
			$power += $powerMap[$id] * $amount;
		}

		return 0;
	}

	public function asteroidIncome( Resource $resource ) {
		return 500 * $resource->asteroids;
	}

	public function rdResultIncome( Resource $resource, $base ) {
		$rdFinished = $this->planet->finished_rd;
		$rdResults = $this->ticker->getRDResultsByTypeAndRD("income:{$resource->id}", $rdFinished);

		foreach ( $rdResults as $result ) {
			switch ( $result->unit ) {
				case 'real':
					$base += $result->change;
					break;

				case 'pct':
					$base *= $result->change;
					break;
			}
		}

		return $base;
	}

}
