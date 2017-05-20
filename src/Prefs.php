<?php

namespace rdx\ps;

class Prefs extends Model {

	static protected $table = 'prefs';

	/**
	 * Getters
	 */

	public function get_fleets() {
		return explode(',', $this->fleetnames);
	}

	public function get_admins() {
		return explode(',', $this->admin_planet_ids);
	}

	/**
	 * Static
	 */

	public function sendEmail( $to, $subject, $body ) {
		$fromName = $this->gamename;
		$fromAddress = 'pornstars@' . str_replace('www.', '', str_replace('pornstars.', '', $_SERVER['HTTP_HOST']));

		return mail($to, $subject, $body, implode("\r\n", [
			"From: $fromName <$fromAddress>",
			"Content-type: text/plain; charset=utf-8",
			"X-sender: " . basename($_SERVER['SCRIPT_NAME']),
		]));
	}

}
