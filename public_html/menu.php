<?php

require_once('inc.config.php');

$defaultTitle	= '';
$defaultTarget	= 'a9';

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
	array( 'Constructions',		'construction.php',		'After researching, you have to actually build that stuff' ),
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
	array( 'Logout',			'logout.php?doit=1',	'Logout here. Session destroyed','_parent' ),
//	null,
//	array( 'Stats',				'stats.php',			'Some statistics about the game. No personal stuff in here' ),
//	array( 'Facts',				'facts.php',			'Some boring facts about the game. Visible to anyone' ),
);

?>
<html>

<head>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
ul#mmenu,
ul#mmenu li {
	list-style			: none;
	margin				: 0;
	padding				: 0;
}
ul#mmenu {
	margin				: 5px;
}
ul#mmenu li {
	margin-top			: 1px;
}
ul#mmenu li a {
	display				: block;
	background-color	: #111;
	padding				: 3px;
}
ul#mmenu li a:hover {
	background-color	: #e80;
	color				: #111;
}
</style>
</head>

<body style="margin:0;background-color:black;">
<img src="images/pornstars.jpg" border="0" onclick="window.location.reload();"><br />
<ul id="mmenu"><?php foreach ( $menu AS $r ) {
	echo '<li>'.( is_array($r) ? '<a href="'.$r[1].'" target="'.( empty($r[3]) ? 'a9' : $r[3] ).'">'.$r[0].'</a>' : '<br />' ).'</li>';
} ?></ul>
</body>

</html>