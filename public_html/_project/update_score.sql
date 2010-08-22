UPDATE
	planets
SET
	score = 0.01 * (			/*SHIPS IN FLEETS*/
			SELECT
				IFNULL(SUM(
					s.amount *
					(
						SELECT
							u.metal+u.crystal+u.energy
						FROM
							d_all_units u,
							d_ships h
						WHERE
							h.all_units_id = u.id AND
							h.id = s.ship_id
					)
				),0)
			FROM
				ships_in_fleets s,
				fleets f
			WHERE
				s.fleet_id = f.id AND
				f.owner_planet_id = planets.id
		) +
		0.01 * (				/*DEFENCE ON PLANETS*/
			SELECT
				IFNULL(SUM(
					dop.amount *
					(
						SELECT
							u.metal+u.crystal+u.energy
						FROM
							d_all_units u,
							d_defence d
						WHERE
							d.all_units_id = u.id AND
							d.id = dop.defence_id
					)
				),0)
			FROM
				defence_on_planets dop
			WHERE
				dop.planet_id = planets.id
		) +
		0.01 * (				/*WAVES ON PLANETS*/
			SELECT
				IFNULL(SUM(
					wop.amount *
					(
						SELECT
							u.metal+u.crystal+u.energy
						FROM
							d_all_units u,
							d_waves w
						WHERE
							w.all_units_id = u.id AND
							w.id = wop.wave_id
					)
				),0)
			FROM
				waves_on_planets wop
			WHERE
				wop.planet_id = planets.id
		) +
		0.01 * (				/*POWER ON PLANETS*/
			SELECT
				IFNULL(SUM(
					pop.amount *
					(
						SELECT
							u.metal+u.crystal+u.energy
						FROM
							d_all_units u,
							d_power pw
						WHERE
							pw.all_units_id = u.id AND
							pw.id = pop.power_id
					)
				),0)
			FROM
				power_on_planets pop
			WHERE
				pop.planet_id = planets.id
		) +
		150 * (					/*ASTEROIDS*/
			metal_asteroids + crystal_asteroids
		) +
		ROUND( 0.002 * (		/*RESOURCES*/
			metal + crystal + energy
		));