<?php

namespace rdx\ps;

use rdx\ps\Planet;

class ResearchDevelopment extends Model {

	static protected $table = 'd_r_d_available';

	/**
	 * Static
	 */

	static public function _allQuery() {
		return '1 ORDER BY o';
	}

	/**
	 * Getters
	 */

	public function get_excluded_by_rd() {
		return array_intersect_key(self::all(), $this->excluded_by_rd_ids);
	}

	public function get_excludes_rd() {
		return array_intersect_key(self::all(), $this->excludes_rd_ids);
	}

	public function get_required_by_rd() {
		return array_intersect_key(self::all(), $this->required_by_rd_ids);
	}

	public function get_requires_rd() {
		return array_intersect_key(self::all(), $this->requires_rd_ids);
	}

	public function get_status() {
		return $this->is_done ? 'done' : ($this->is_doing ? 'doing' : 'available');
	}

	public function get_order_score() {
		if ( $this->is_doing ) {
			return $this->id;
		}
		elseif ( !$this->is_done ) {
			return $this->id + 100;
		}
		return 10000 - $this->o;
	}

	public function get_is_done() {
		return $this->planet_eta !== null && $this->planet_eta == 0;
	}

	public function get_is_doing() {
		return $this->planet_eta !== null && $this->planet_eta > 0;
	}

	public function get_excluded_by_rd_ids() {
		global $db;
		return $db->select_fields('d_r_d_excludes', 'r_d_id, r_d_id', ['r_d_excludes_id' => $this->id]);
	}

	public function get_excludes_rd_ids() {
		global $db;
		return $db->select_fields('d_r_d_excludes', 'r_d_excludes_id, r_d_excludes_id', ['r_d_id' => $this->id]);
	}

	public function get_required_by_rd_ids() {
		global $db;
		return $db->select_fields('d_r_d_requires', 'r_d_id, r_d_id', ['r_d_requires_id' => $this->id]);
	}

	public function get_requires_rd_ids() {
		global $db;
		return $db->select_fields('d_r_d_requires', 'r_d_requires_id, r_d_requires_id', ['r_d_id' => $this->id]);
	}

	public function get_pct_done() {
		if ( $this->planet_eta !== null ) {
			return round(($this->eta - $this->planet_eta) / $this->eta * 100);
		}
	}

	/**
	 * Logic
	 */

	public function start( Planet $planet ) {
		global $db;

		$db->insert('planet_r_d', [
			'planet_id' => $planet->id,
			'r_d_id' => $this->id,
			'eta' => $this->eta,
		]);
	}

	public function planetHasRequireds( Planet $planet ) {
		$planetRD = $planet->finished_rd;
		foreach ( $this->requires_rd_ids as $id ) {
			if ( !isset($planetRD[$id]) ) {
// echo "No access to # {$this->id} because planet lacks # {$id}\n";
				return false;
			}
		}

// echo "No invalid requiredness for  # {$this->id}\n";
		return true;
	}

	public function planetHasExcludeds( Planet $planet ) {
		$planetRD = $planet->finished_rd + $planet->doing_rd;
		foreach ( $this->excluded_by_rd_ids as $id ) {
			if ( isset($planetRD[$id]) ) {
// echo "No access to # {$this->id} because planet chose # {$id}\n";
				return true;
			}
		}

// echo "No invalid excludedness for  # {$this->id}\n";
		return false;
	}

	public function __toString() {
		return $this->name;
	}

}
