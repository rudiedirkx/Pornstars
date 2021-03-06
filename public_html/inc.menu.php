<?php

$menu = array(
	array( 'Overview',			'overview.php',			'Overview :: check it!' ),
	array( 'Manual',			'manual.php',			'Overview of the manual' ),
	array( 'Galaxy Status',		'galaxynews.php',		'Incoming and Outgoing of your galaxy' ),
	array( 'Send Mail',			'communication.php',	'Send a PM to anyone. See manual for more info about PM-ing.' ),
	array( 'Galaxy Forums',		'galaxyforums.php',		'Discuss galaxy matters here' ),
	array( 'Journal',			'journal.php',			'Personal note storage facility' ),
	null,
	array( 'Production',		'production.php',		'Get some units done' ),
	array( 'Research',			'research.php',			'Research new and advanced technologies' ),
	array( 'Development',		'construction.php',		'After researching, you have to actually build that stuff' ),
	array( 'Resources',			'resources.php',		'Initiate \'roids, donate to friendlies and check your roidstatus' ),
	array( 'Skills',			'skills.php',			'Train your skills' ),
#	null,
#	array( 'Your Alliance',		'alliance.php',			'Info about your alliance, if you\'re in one' ),
#	array( 'Alliance Ranking',	'ranking.alliance.php',	'Alliance ranking. Highest cumulative score on top' ),
	null,
	array( 'Galaxy',			'galaxy.php',			'Your galaxy (pic, name, score, size and planets)' ),
	array( 'Politics',			'politics.php',			'Vote for GC here. If you are GC, you can edit some Galaxy parameters' ),
	array( 'Planet Ranking',	'ranking.planet.php',	'Planet ranking. The top 20 planets' ),
	array( 'Waves',				'waves.php',			'Scan for Asteroids or other planets here' ),
	array( 'Military',			'military.php',			'Organise your fleets and send them to defend or attack' ),
	null,
	array( 'Preferences',		'preferences.php',		'Necessities' ),
	array( 'Log out',			'logout.php?doit=1',	'Logout here. Session destroyed','_parent' ),
	null,
	array( 'Config',			'_project/',			'All config for all game content' ),
	array( 'Admin',				'comp.php',				'Game admin area' ),
//	array( 'Stats',				'stats.php',			'Some statistics about the game. No personal stuff in here' ),
//	array( 'Facts',				'facts.php',			'Some boring facts about the game. Visible to anyone' ),
);

?>
<ul class="main-menu">
<? foreach ( $menu AS $item ): ?>
	<? if ( $item ):
		$active = basename($_SERVER['SCRIPT_NAME']) == $item[1] ? 'active' : '';
		?>
		<li><?= '<a class="' . $active . '" href="' . html($item[1]) . '">' . html($item[0]) . '</a>' ?></li>
	<? else: ?>
		</ul>
		<ul class="main-menu">
	<? endif ?>
<? endforeach ?>
</ul>
