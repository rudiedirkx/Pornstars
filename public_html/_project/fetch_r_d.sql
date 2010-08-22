SELECT
	a.*
FROM
	d_r_d_available a
WHERE
	T = 'r' AND
	(
		a.id NOT IN
		(
			SELECT
				r_d_id
			FROM
				d_r_d_requires
		) OR
		a.id IN
		(
			SELECT
				r.r_d_id
			FROM
				d_r_d_requires r
			JOIN
				r_d_per_planet p
					ON p.r_d_id = r.r_d_requires_id
			WHERE
				p.eta = 0 AND
				p.planet_id = 1
			GROUP BY
				r.r_d_id
			HAVING
				COUNT(1) >= (SELECT COUNT(1) FROM d_r_d_requires WHERE r_d_id = r.r_d_id)
			ORDER BY
				r.r_d_id ASC
		)
	) AND
	(
		a.id NOT IN
		(
			SELECT
				r_d_excludes_id
			FROM
				d_r_d_excludes
		) OR
		(
			/* no exclusion of a.id is `used` */
			1
		)
	)
	AND a.id NOT IN (SELECT r_d_id FROM r_d_per_planet WHERE planet_id = 1 AND eta = 0)
ORDER BY
	a.id ASC;