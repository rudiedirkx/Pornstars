<?php

if ( isset($_GET['mode']) && $_GET['mode'] == "toggle" ) {
	$t = '1' == $g_arrUser['show_all_r_d'] ? '0' : '1';
	db_update('planets', "show_all_r_d = '".$t."'", 'id = '.PLANET_ID);
	Go();
}

$arrSkills = db_fetch_fields('SELECT s.id, IFNULL((SELECT value FROM planet_skills WHERE skill_id = s.id AND planet_id = '.PLANET_ID.'),0) AS value FROM d_skills s ORDER BY s.id ASC;');

if ( isset($_GET['r_d_id']) ) {
	// Make sure that R&D exists and none other is busy
	if ( !count($rd=db_select('d_r_d_available', 'id = '.(int)$_GET['r_d_id'].' AND T = \''.$szType.'\' LIMIT 1')) || db_count('d_r_d_available a, planet_r_d p', 'a.id = p.r_d_id AND p.planet_id = '.PLANET_ID.' AND p.eta <> 0 AND a.T = \''.$rd[0]['T'].'\'') ) {
		exit(json::encode(array(
			array('msg', 'Invalid ID!'),
		)));
	}
	$rd = $rd[0];

	// check requirements
	if ( !db_count('d_r_d_available a', 'a.id = '.(int)$rd['id'].' AND T = \''.$szType.'\' AND
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
					planet_r_d p
						ON p.r_d_id = r.r_d_requires_id
				WHERE
					p.eta = 0 AND
					p.planet_id = '.PLANET_ID.'
				GROUP BY
					r.r_d_id
				HAVING
					COUNT(1) >= (SELECT COUNT(1) FROM d_r_d_requires WHERE r_d_id = r.r_d_id)
				ORDER BY
					r.r_d_id ASC
			)
		) AND
		( 0 < (SELECT COUNT(1) FROM d_r_d_per_race WHERE r_d_id = a.id AND race_id = '.$g_arrUser['race_id'].') ) AND
		a.id NOT IN (SELECT r_d_id FROM planet_r_d WHERE planet_id = '.PLANET_ID.' AND eta = 0)') )
	{
		exit(json::encode(array(
			array('msg', 'Requirements error!'),
		)));
	}


	// check exclusions
	if ( 0 < count($e=db_select_fields('d_r_d_excludes e', 'r_d_id,r_d_id', 'r_d_excludes_id = '.(int)$rd['id'])) ) {
		foreach ( $e AS $_eId ) {
			if ( 0 < (int)db_count('planet_r_d','planet_id = '.PLANET_ID.' AND r_d_id = '.(int)$_eId.'') ) {
				exit(json::encode(array(
					array('msg', 'Exclusions error!'),
				)));
			}
		}
	}

	// check race enabled
	if ( !db_count('d_r_d_per_race', 'race_id = '.$g_arrUser['race_id'].' AND r_d_id = '.(int)$rd['id']) ) {
		exit(json::encode(array(
			array('msg', 'R&D not included in Race Spectrum!'),
		)));
	}

	// check skills
	$arrRequiredSkills = db_select_fields('d_skills s, d_skills_per_r_d r', 's.id,r.required_value', 'r.skill_id = s.id AND r.r_d_id = '.(int)$rd['id'].' AND 0 < required_value');
	foreach ( $arrRequiredSkills AS $iSkill => $iValue ) {
		if ( (float)$iValue > (float)$arrSkills[$iSkill] ) {
		exit(json::encode(array(
			array('msg', 'You\'re not skilled enough yet!'),
		)));
		}
	}

	$arrCosts = db_select_fields('d_r_d_costs', 'resource_id,amount', '0 < amount AND r_d_id = '.(int)$rd['id']);
	foreach ( $arrCosts AS &$c ) {
		$c = max(0, applyRDChange('r_d_costs', (int)$c));
		unset($c);
	}

	if ( $HAVOC_RESDEV ) {
		$rd['eta'] = max(0, (int)$HAVOC_RESDEV_ETA);
	}
	else {
		$rd['eta'] = applyRDChange('r_d_eta',  (int)$rd['eta']);
	}

	if ( 0 === array_sum($arrCosts) || db_transaction_update($arrCosts, 'resource_id', 'amount') ) {
		$arrInsert = array('r_d_id' => (int)$rd['id'], 'planet_id' => PLANET_ID, 'eta' => (int)$rd['eta']);
		db_insert( 'planet_r_d', $arrInsert );
		// SUCCESS
		exit(json::encode(array(
			array('eval', 'document.location.reload();'),
		)));
	}

	// FAILED
	exit(json::encode(array(
		array('msg', 'You probably don\'t have enough resources!'),
	)));

} // END if ( isset($_GET['r_d_id']) )

_header();

?>

<div class="header"><?php echo $szName; if ( $HAVOC_RESDEV ) { echo ' (<b style="color:red;">HAVOC!</b>)'; } ?></div>

<br />

<a href="?mode=toggle">Toggle View</a><br />

<table border="0" bordercolor="#dddddd" cellspacing="0" cellpadding="3" width="100%" class="widecells">
<tr id="titelbalk">
	<th></th>
	<th width="100%" class="left">Name</th>
	<th align="center">ETA</th>
	<th align="right">Progress</th>
	<th align="center">Action</th>
	<th align="right">Costs</th>
	<th align="right">Skills</th>
</tr>
<?php

$bInProgess = 0 < db_count('planet_r_d p, d_r_d_available a', 'p.planet_id = '.PLANET_ID.' AND a.id = p.r_d_id AND a.T = \''.$szType.'\' AND 0 < p.eta');

$arrPlanetRD = db_select_fields( 'planet_r_d', 'r_d_id,eta', 'planet_id = '.PLANET_ID );

$arrRD = db_fetch('
SELECT
	a.*
FROM
	d_r_d_available a
WHERE
	T = \''.$szType.'\' AND
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
				planet_r_d p
					ON p.r_d_id = r.r_d_requires_id
			WHERE
				p.eta = 0 AND
				p.planet_id = '.PLANET_ID.'
			GROUP BY
				r.r_d_id
			HAVING
				COUNT(1) >= (SELECT COUNT(1) FROM d_r_d_requires WHERE r_d_id = r.r_d_id)
			ORDER BY
				r.r_d_id ASC
		)
	) AND
	(0 < (SELECT COUNT(1) FROM d_r_d_per_race WHERE r_d_id = a.id AND race_id = '.$g_arrUser['race_id'].'))
	'.( !$g_arrUser['show_all_r_d'] ? ' AND a.id NOT IN (SELECT r_d_id FROM planet_r_d WHERE planet_id = '.PLANET_ID.' AND eta = 0)' : '' ).'
ORDER BY
	a.id ASC;
');
foreach ( $arrRD AS $r )
{
	// exclusion demands
	if ( 0 < count($e=db_select_fields('d_r_d_excludes e', 'r_d_id,r_d_id', 'r_d_excludes_id = '.(int)$r['id'])) ) {
		foreach ( $e AS $_eId ) {
			if ( 0 < (int)db_count('planet_r_d','planet_id = '.PLANET_ID.' AND r_d_id = '.(int)$_eId.'') ) {
				continue 2;
			}
		}
	}

	// race enabled
#	if ( !db_count('d_r_d_per_race', 'race_id = '.$g_arrUser['race_id'].' AND r_d_id = '.(int)$r['id']) ) {
#		continue;
#	}

	$bNoMsg = true;

	echo '<tr valign="top" class="bt"'.( isset($arrPlanetRD[$r['id']]) && $arrPlanetRD[$r['id']] != '0' ? ' bgcolor="#221111"' : '' ).'>';
	// ID
	echo '<td align="right" width="10">'.$r['id'].'</td>';
	$arrExcludes = db_select_fields('d_r_d_excludes e, d_r_d_available a', 'a.id,a.name', 'a.id = e.r_d_excludes_id AND e.r_d_id = '.(int)$r['id']);
	$szExcludes = '';
	if ( 0 < count($arrExcludes) ) {
		$e = array_pop($arrExcludes);
		$szExcludes = '<br />(<u style="color:red;">excludes</u> ' . ( count($arrExcludes) ? '<i>`'.implode('`</i>, <i>`', $arrExcludes) . '`</i> <b>and</b> ' : '' ) . '<i>`' . $e . '`</i>)';
	}
	$szEnables = '';
	if ( $u=array_flip(array_flip(db_select_fields('d_all_units', 'id,unit_plural', 'r_d_required_id = '.(int)$r['id']))) ) {
		$szEnables = ' title="Enables `'.implode('`, `', $u).'`"';
	}
	// Name
	echo '<td width="100%"'.$szEnables.'><i onclick="var d=this.parentNode.getElementsByTagName(\'div\')[0].style;d.display=\'none\'!=d.display?\'none\':\'\';" style="cursor:pointer;">'.$r['name'].( $szExcludes ? ' '.$szRDExcludes : '' ).'</i><div style="display:none;">'.$r['explanation'].$szExcludes.'</div></td>';
	$szEta = isset($arrPlanetRD[$r['id']]) ? $arrPlanetRD[$r['id']] : ( $HAVOC_RESDEV ? $HAVOC_RESDEV_ETA : applyRDChange('r_d_eta',  (int)$r['eta']) );
	// ETA
	echo '<td align="center">'.$szEta.'</td>';
	// Progress
	echo '<td align="right">'.( isset($arrPlanetRD[$r['id']]) ? round(($r['eta']-$arrPlanetRD[$r['id']])/max(1,$r['eta'])*100).' %' : '-' ).'</td>';
	// Action
	echo '<td align="center">'.( !isset($arrPlanetRD[$r['id']]) ? ( $bInProgess ? '-' : '<a onclick="return R(this);" href="?r_d_id='.$r['id'].'">start</a>' ) : '-' ).'</td>';
	$arrPreCosts = db_select_fields('d_r_d_costs', 'resource_id,amount', '0 < amount AND r_d_id = '.(int)$r['id'].' ORDER BY resource_id ASC');
	$arrCosts = array();
	foreach ( $arrPreCosts AS $iResourceId => $iAmount ) {
		$iAmount = max(0, applyRDChange('r_d_costs', (int)$iAmount));
		$arrCosts[] = '<span style="color:'.$g_arrResources[$iResourceId]['color'].';">'.nummertje($iAmount).'&nbsp;'.strtolower($g_arrResources[$iResourceId]['resource']).'</span>';
	}
	$szCosts = $arrCosts /*&& (!isset($arrPlanetRD[$r['id']]) || '0' !== $arrPlanetRD[$r['id']])*/ ? implode('<br />', $arrCosts) : '-';
	// Costs
	echo '<td align="right">'.$szCosts.'</td>';
	$arrRequiredSkills = db_select_fields('d_skills s, d_skills_per_r_d r', 's.id,concat(s.skill,\': \',r.required_value)', 'r.skill_id = s.id AND r.r_d_id = '.(int)$r['id'].' AND 0 < required_value');
	foreach ( $arrRequiredSkills AS $iSkill => &$szSkill ) {
		$x = explode(':', $szSkill);
		$iValue = (int)trim($x[1]);
		if ( (float)$arrSkills[$iSkill] < (float)$iValue ) {
			$szSkill = '<span style="color:red;">'.$szSkill.'</span>';
		}
		unset($szSkill);
	}
	$szSkills = $arrRequiredSkills ? implode('<br />', $arrRequiredSkills) : '-';
	// Skills
	echo '<td align="right" style="font-size:10px;" nowrap="1" wrap="off">'.$szSkills.'</td>';
	echo '</tr>'."\n";
}
if ( empty($bNoMsg) ) {
	echo '<tr><td colspan="6" align="center">'.$szAllRDDone.'</td></tr>';
}

?>
</table>

<br />

<?php

_footer();

?>