<?php

namespace rdx\ps;

use rdx\ps\Planet;

class Thread extends Model {

	static protected $table = 'politics';

	/**
	 * Static
	 */

	public function findReadable( $id, Planet $planet ) {
		if ( $id ) {
			if ( $thread = self::find($id) ) {
				if ( !$thread->parent_thread_id && $thread->galaxy_id == $planet->galaxy_id ) {
					return $thread;
				}
			}
		}
	}

	/**
	 * Getters
	 */

	public function get_posts() {
		return self::all('parent_thread_id = ? ORDER BY id ASC', [$this->id]);
	}

	public function get_creator_planet() {
		return Planet::find($this->creator_planet_id);
	}

}
