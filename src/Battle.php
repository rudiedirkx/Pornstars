<?php

namespace rdx\ps;

class Battle {

	public $location;
	public $attacking = [];
	public $defending = [];

	public function __construct( Planet $location ) {
		$this->location = $location;
	}

	public function addFleet( Fleet $fleet ) {
		if ($fleet->action == 'attack') {
			$this->attacking[] = $fleet;
		}
		elseif ($fleet->action == 'defend') {
			$this->defending[] = $fleet;
		}
	}

}
