<?php

namespace rdx\ps;

use rdx\ps\Planet;
use rdx\ps\Resource;

class PlanetTicker {

	protected $ticker;
	protected $planet;
	protected $fleets;

	public function __construct( Ticker $ticker, Planet $planet ) {
		global $db;

		$this->ticker = $ticker;
		$this->planet = $planet;

		$this->fleets = array_values($db->select_fields('fleets', 'id', 'owner_planet_id = ? ORDER BY fleetname ASC', [$planet->id]));
	}

	public function addProduction( $unitId, $amount ) {
		$unit = $this->ticker->getUnit($unitId);
		switch ( $unit->base_type ) {
			case 'ship':
				return $this->addShipsToFleet($unitId, $amount);

			case 'wave':
				return $this->addUnitsToPlanet('waves_on_planets', $unitId, $amount);

			case 'defence':
				return $this->addUnitsToPlanet('defence_on_planets', $unitId, $amount);

			case 'power':
				return $this->addUnitsToPlanet('power_on_planets', $unitId, $amount);
		}
	}

	public function addShipsToFleet( $unitId, $amount ) {
		global $db;
		return $db->update('ships_in_fleets', 'amount = amount + ' . (int) $amount, [
			'fleet_id' => $this->fleets[0],
			'unit_id' => $unitId,
		]);
	}

	public function addUnitsToPlanet( $table, $unitId, $amount ) {
		global $db;
		return $db->update($table, 'amount = amount + ' . (int) $amount, [
			'planet_id' => $this->planet->id,
			'unit_id' => $unitId,
		]);
	}

	public function getIncome( Resource $resource ) {
		$income = [];

		if ( $resource->is_power ) {
			$income['asteroid'] = $this->powerIncome();
		}
		else {
			$income['asteroid'] = $this->asteroidIncome($resource);
		}

		$total = $this->rdResultIncome($resource, $income['asteroid']);
		$income['bonus'] = $total - $income['asteroid'];

		return $income;
	}

	public function addResource( Resource $resource ) {
		$income = array_sum($this->getIncome($resource));

		if ( $income > 0 ) {
			global $db;
			return $db->update('planet_resources', 'amount = amount + ' . (int) $income, [
				'planet_id' => $this->planet->id,
				'resource_id' => $resource->id,
			]);
		}
	}

	public function powerIncome() {
		global $db;

		$powerMap = $this->ticker->getPowerMap();
		$planetPower = $db->select_fields('power_on_planets', 'unit_id, amount', 'planet_id = ?', [$this->planet->id]);

		$power = 0;
		foreach ( $planetPower as $id => $amount ) {
			$power += $powerMap[$id] * $amount;
		}

		return $power;
	}

	public function asteroidIncome( Resource $resource ) {
		return 500 * $resource->asteroids;
	}

	public function rdResultIncome( Resource $resource, int $base ) {
		$rdFinished = $this->planet->finished_rd_ids;
		$rdResults = $this->ticker->getRDResultsByTypeAndRD("income:{$resource->id}", $rdFinished);

		return $this->applyRdResults($base, $rdResults);
	}

	public function rdResultRdEta( int $base ) : int {
		$rdFinished = $this->planet->finished_rd_ids;
		$rdResults = $this->ticker->getRDResultsByTypeAndRD('r_d_eta', $rdFinished);

		return ceil($this->applyRdResults($base, $rdResults));
	}

	public function rdResultRdCosts( array $costs ) : array {
		$rdFinished = $this->planet->finished_rd_ids;
		$rdResults = $this->ticker->getRDResultsByTypeAndRD('r_d_costs', $rdFinished);

		return array_map(function(Resource $cost) use ($rdResults) {
			$cost->amount = max(0, ceil($this->applyRdResults($cost->amount, $rdResults)));
			return $cost;
		}, $costs);
	}

	public function rdResultTravelEta( int $base ) : int {
		$rdFinished = $this->planet->finished_rd_ids;
		$rdResults = $this->ticker->getRDResultsByTypeAndRD('travel_eta', $rdFinished);

		return ceil($this->applyRdResults($base, $rdResults));
	}

	public function rdResultTravelCosts( int $base ) : int {
		$rdFinished = $this->planet->finished_rd_ids;
		$rdResults = $this->ticker->getRDResultsByTypeAndRD('power_usage', $rdFinished);

		return ceil($this->applyRdResults($base, $rdResults));
	}

	protected function applyRdResults( int $base, array $results ) {
		foreach ( $results as $result ) {
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
