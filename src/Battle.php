<?php

namespace rdx\ps;

class Battle {

	public $allUnits = [];
	public $zeroUnits = [];

	public $location;
	public $homeFleets = [];

	public $attackingFleets = [];
	public $attackingUnits = [];
	public $attackersKilled = [];

	public $defendingFleets = [];
	public $defendingUnits = [];
	public $defendersKilled = [];

	public function __construct( array $allUnits, Planet $location ) {
		$this->allUnits = array_filter($allUnits, function(Unit $unit) {
			return in_array($unit->T, ['ship', 'defence']);
		});
		$this->location = $location;

		$this->zeroUnits = $this->allUnitsZero();
		$this->attackingUnits = $this->defendingUnits = $this->zeroUnits;

		$this->addHomeFleets();
		$this->addDefendingUnits($location->defences);
	}

	public function fight() {
		// If everybody shoots ALL their targets at the same time (not using target_priority):
echo "\nDefenders shoot\n";
		$this->attackersKilled = $this->shootAtPriorities($this->defendingUnits, $this->attackingUnits);
echo "\nAttackers shoot\n";
		$this->defendersKilled = $this->shootAtPriorities($this->attackingUnits, $this->defendingUnits);

		$this->updateFleetNumbers($this->homeFleets, $this->defendersKilled);
		$this->updateDefenceNumbers($this->location->defences, $this->defendersKilled);
		$this->updateFleetNumbers($this->defendingFleets, $this->defendersKilled);
		$this->updateFleetNumbers($this->attackingFleets, $this->attackersKilled);
	}

	public function addFleet( Fleet $fleet ) {
		if ($fleet->action == 'attack') {
			$this->attackingFleets[] = $fleet;
			$this->addAttackingUnits($fleet->ships);
		}
		elseif ($fleet->action == 'defend') {
			$this->defendingFleets[] = $fleet;
			$this->addDefendingUnits($fleet->ships);
		}
		elseif (!$fleet->action) {
			$this->homeFleets[] = $fleet;
			$this->addDefendingUnits($fleet->ships);
		}
	}

	public function prepareRatios() {
		foreach ($this->attackingFleets as $fleet) {
			foreach ($fleet->ships as $id => $info) {
				$info->contributionRatio = $this->attackingUnits[$id] ? $info->planet_amount / $this->attackingUnits[$id] : 0;
			}
		}

		foreach ($this->defendingFleets as $fleet) {
			foreach ($fleet->ships as $id => $info) {
				$info->contributionRatio = $this->defendingUnits[$id] ? $info->planet_amount / $this->defendingUnits[$id] : 0;
			}
		}

		foreach ($this->homeFleets as $fleet) {
			foreach ($fleet->ships as $id => $info) {
				$info->contributionRatio = $this->defendingUnits[$id] ? $info->planet_amount / $this->defendingUnits[$id] : 0;
			}
		}

		foreach ($this->location->defences as $info) {
			$info->contributionRatio = 1;
		}
	}

	public function getAllActiveFleets() {
		return array_merge($this->defendingFleets, $this->attackingFleets);
	}

	protected function executeSql($sql) {
		global $db;
		$db->execute($sql);
	}

	protected function updateDefenceNumbers(array $units, array $killed) {
echo "\nPlanet defence\n";
		foreach ($units as $unit) {
			if ($unit->planet_amount > 0 && $killed[$unit->id] > 0) {
				$take = ceil(1 * $killed[$unit->id]);
echo "^ loses $take $unit\n";
				$this->executeSql("UPDATE defence_on_planets SET amount = GREATEST(0, amount - $take) WHERE planet_id = {$this->location->id} AND unit_id = {$unit->id}");
			}
		}
	}

	protected function updateFleetNumbers(array $fleets, array $killed) {
		foreach ($fleets as $fleet) {
echo "\n$fleet->owner_planet's $fleet ($fleet->action)\n";
			foreach ($fleet->ships as $ship) {
				if ($ship->planet_amount > 0 && $killed[$ship->id] > 0) {
					$take = ceil($ship->contributionRatio * $killed[$ship->id]);
echo "^ loses $take $ship\n";
					$this->executeSql("UPDATE ships_in_fleets SET amount = GREATEST(0, amount - $take) WHERE fleet_id = {$fleet->id} AND unit_id = {$ship->id}");
				}
			}
		}
	}

	protected function allUnitsZero() {
		return array_combine(array_keys($this->allUnits), array_fill(0, count($this->allUnits), 0));
	}

	protected function shootAtPriorities(array $shootingUnits, array $receivingUnits) {
		$killed = $this->allUnitsZero();

		foreach ($shootingUnits as $shootingId => $amount) {
			foreach ($this->allUnits[$shootingId]->kill_ratios as $gettingShotId => $ratio) {
				if (($receivingUnits[$gettingShotId] ?? 0) > 0) {
					if ($receivingUnits[$gettingShotId] > $killed[$gettingShotId]) {
echo "{$amount} {$this->allUnits[$shootingId]} shoot at {$receivingUnits[$gettingShotId]} {$this->allUnits[$gettingShotId]}\n";
						$killed[$gettingShotId] += abs($ratio) * $amount;
						break;
					}
					else {
echo "- {$this->allUnits[$shootingId]} don't shoot at {$this->allUnits[$gettingShotId]} bc already dead\n";
					}
				}
			}
		}

		foreach ($killed as $id => $amount) {
			$killed[$id] = min($amount, $receivingUnits[$id] ?? 0);
		}

		return $killed;
	}

	protected function shootAtEverybody(array $shootingUnits, array $receivingUnits) {
		$killed = $this->allUnitsZero();

		foreach ($shootingUnits as $shootingId => $amount) {
			foreach ($this->allUnits[$shootingId]->kill_ratios as $gettingShotId => $ratio) {
				$killed[$gettingShotId] += abs($ratio) * $amount;
			}
		}

		foreach ($killed as $id => $amount) {
			$killed[$id] = min($amount, $receivingUnits[$id] ?? 0);
		}

		return $killed;
	}

	protected function addHomeFleets() {
		foreach ($this->location->fleets as $fleet) {
			$this->addFleet($fleet);
		}
	}

	protected function addAttackingUnits( array $units ) {
		foreach ($units as $id => $info) {
			$this->addUnits('attackingUnits', $id, $info->planet_amount);
		}
	}

	protected function addDefendingUnits( array $units ) {
		foreach ($units as $id => $info) {
			$this->addUnits('defendingUnits', $id, $info->planet_amount);
		}
	}

	protected function addUnits( $collection, $id, $amount ) {
		isset($this->{$collection}[$id]) or $this->{$collection}[$id] = 0;
		$this->{$collection}[$id] += $amount;
	}

}
