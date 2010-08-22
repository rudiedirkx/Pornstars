<?php

$indextitel = '<table border="0" cellpadding="5" cellspacing="0" width="100%"><tr valign="middle"><td align="center"><a href="./">Index</a> &nbsp; || &nbsp; <a href="login.php">Login</a> &nbsp; || &nbsp; <a href="signup.php">Signup</a> &nbsp;||&nbsp; <a href="./"><b>Play</b></a> &nbsp;||&nbsp; <a href="comp.php" target="_parent">Administration</a></td></tr></table>';

// Misc
$PWD_MUST_CHANGE			= (bool)(int)$GAMEPREFS['must_change_pwd'];			// Password must be changed in order to play when set to TRUE
$CHECK_TIME_BETWEEN_LOGINS	= (int)$GAMEPREFS['between_logins_time'];			// If 0, disabled. Else you can't login for this amount of seconds after your last login
$PLANETS_IN_ONE_GALAXY		= (int)$GAMEPREFS['planets_per_galaxy'];			// Duh
$RESCON_NEWS_ON				= (bool)(int)$GAMEPREFS['news_for_done_rd'];			// If TRUE, the engine will check R&D per planet (takes a bit longer, but sends a Msg when finished)
$GALFORUM_WAIT_FOR_TURN		= (bool)(int)$GAMEPREFS['galaxy_forum_wait_for_turn'];	// If TRUE, you cant post 2 posts after eachother without anyone posting in between

$FLEETNAMES					= explode(',', (trim($GAMEPREFS['fleetnames'])?$GAMEPREFS['fleetnames']:'Base'));			// The names for BASEFLEET and $NUM_OUTGOING_FLEETS outgoing fleets
$NUM_OUTGOING_FLEETS		= min(count($FLEETNAMES)-1, (int)$GAMEPREFS['num_outgoing_fleets']);			// Duh


foreach ( db_select_fields( 'd_news_subjects', 'id,const_name', '1 ORDER BY id ASC' ) AS $iSubjectId => $szConstant ) {
	define( 'NEWS_SUBJECT_'.strtoupper($szConstant), (int)$iSubjectId );
}


// Preferences
$TICKERTIME		= $GAMEPREFS['tickertime'];
$tickertime		= $GAMEPREFS['tickertime'];
$MyT			= $GAMEPREFS['tickcount'];
$GAMENAME		= $GAMEPREFS['gamename'];

$showcolors['metal']	= '#555555';
$showcolors['crystal']	= '#2244dd';
$showcolors['energy']	= '#228800';
$showcolors['gc']	= '#8080ff';
$showcolors['mow']	= '#ff3333';
$showcolors['moc']	= 'green';
$showcolors['mof']	= 'gold';
$showcolors['attack']	= 'red';
$showcolors['defend']	= 'lime';
$showcolors['return']	= '#777777';

$scansavecosts['metal']		= 20000;
$scansavecosts['crystal']	= 20000;
$scansavecosts['energy']	= 50000;

// Spec Ops
$HAVOC_RESDEV		= (bool)(int)$GAMEPREFS['havoc_r_d'];
$HAVOC_RESDEV_ETA	= max(0, (int)$GAMEPREFS['r_d_eta_in_havoc']);
$HAVOC_MILITARY		= (bool)(int)$GAMEPREFS['havoc_military'];
$HAVOC_PRODUCTION	= (bool)(int)$GAMEPREFS['havoc_production'];

$titlearray = array(
	'communication'	=> 'messages',
	'galaxynews'	=> 'galaxy_status',
	'resources'		=> 'mining',
	'creonogy'		=> 'energy',
	'ranking'		=> 'universe',
	'military'		=> 'missions',
//	'construction'	=> 'development',
);

// Layout options
$szRDExcludes = '<span style="color:red;font-weight:bold;">!!</span>';


return;







/**
// CONSTRUCTIONARRAY: SOORT=>ARRAY(NAAM,UITLEG,ETA,METAL,CRYSTAL,ENERGY,NODIG,EXCLUDES)
$constructionarray = array(	'crystal10'=>array('Crystal Refinery','Gain +1500 extra Crystal','4','0','0','0','',''),
				'metal10'=>array('Metal Refinery','Gain +1500 extra Metal','4','500','500','0','',''),
				'bscan'=>array('Sector Scanning','Enables Sectorscans','8','1000','1000','0','r_scan1',''),
				'pinf'=>array('First Airport','Enables the production of Inifinitys','10','1000','1000','0','c_metal10;c_crystal10',''),
				'energy'=>array('Making Energy','Enables the production of a Creon living environment','9','3000','3000','0','r_energy',''),
				'crystal20'=>array('Get More Crystal','Gain +3000 extra Crystal','11','6000','9000','0','r_crystal15',''),
				'metal20'=>array('Get More Metal','Gain +3000 extra Metal','13','9000','6000','0','r_metal15',''),
				'pwra'=>array('Robot Factory','Enables the production of Wraiths','14','8000','4000','0','c_pinf',''),
				'uscan'=>array('Unit Scanning','Enables Unitscans','14','12000','20000','0','r_scan2',''),
				'crystal30'=>array('Hardcore Crystal','Builds machines who provide you an extra +6000 Crystal','20','25000','45000','0','r_crystal25',''),
				'metal30'=>array('Hardcore Metal','Builds machines who provide you an extra +6000 Metal','24','50000','30000','0','r_metal25',''),
				'pdes'=>array('Destroyer Factory','Enables the production of Destroyers','36','80000','80000','0','r_pwarast',''),
				'psco'=>array('Scorpion Factory','Enables the production of Destroyers','44','120000','200000','0','c_pdes',''),
				'pscan'=>array('PDU Scanning','Enables PDUscans','30','200000','300000','0','r_scan3',''),
				'mscan'=>array('Fleet Scanners','Enables Fleetscans','40','400000','500000','0','r_scan4',''),
				'nscan'=>array('High-speed-plasma Spies','Enables newsscans by penetrating the high-speed-plasma of your target','44','400000','800000','0','r_scan5',''),
				'pdu1'=>array('PDU Machines','Enables the production of Planetery Defence Units (Reaper Cannons and Lucius Stalkers)','36','600000','550000','0','r_pcob',''),
				'pdu2'=>array('Super PDU','Enables the production of Avengers; a sneaky bitch ass PDU','42','700000','700000','0','c_pdu1',''),
				'wblockers'=>array('Wave Blockers','Extra holding and forcefull shields to block infiltrationscans','50','950000','1200000','0','r_wblockers',''),
				'abot'=>array('SuperCruisers','Only hangars and upgrading factories are yet necessary','56','2200000','1100000','0','r_abot2',''),
				'enef'=>array('Creons in Boxes','Boxes being built which can contain a large amount of Creons','52','2800000','4000000','800000','r_enef',''),
				'timt'=>array('Hot \'n\' Thick','Improving pantser and heatresistment on all moving units. After this, you save another 5 ticks on all fleet ETA','74','2000000','2800000','0','r_timt2',''),
				'rebo'=>array('Work, Bitch, Work!','Construction of weapons to convince your professors to work more and harder','42','4000000','2400000','0','r_rebo',''),
				'rebo2'=>array('Building Space Warehouses','Increasing storage facilities. Asteroid income should rise considerable','38','4000000','4000000','0','c_rebo',''),
				'prsp'=>array('Production Spy Suits','Creating the suits Production Spies get to wear, to infiltrate enemy production facilities','92','800000','800000','800000','r_prsp','')
);

// RESEARCHARRAY: SOORT=>ARRAY(NAAM,UITLEG,ETA,METAL,CRYSTAL,NODIG,EXCLUDES)
$researcharray = array(	'scan1'=>array('About Scanning','Enables the production of huge-scale radars and asteroidscans','6','0','1000','0','',''),
			'energy'=>array('About Energy','Researches the art of Creon growth','4','0','850','0','',''),
			'crystal15'=>array('More Crystal','Researches more efficient mining methods','8','2000','2000','0','c_crystal10',''),
			'metal15'=>array('More Metal','Researches more efficient mining methods','10','3000','3000','0','c_metal10',''),
			'scan2'=>array('Unit Patterns','Researching the early unitscans','10','6000','6000','0','c_bscan',''),
			'crystal25'=>array('Blind Diggers for Crystal','Researching machines who dig to the core','16','20000','30000','0','c_crystal20',''),
			'metal25'=>array('Blind Diggers for Metal','Researching machines who dig to the core','20','30000','20000','0','c_metal20',''),
			'pwarast'=>array('Tank Building','Enables the production of Warfrigates and Astropods','16','48000','40000','0','c_pwra',''),
			'scan3'=>array('PDU Patterns','Researching PDU-methods','22','70000','90000','20000','c_uscan',''),
			'pcob'=>array('EMP Studies','Enables the production of Cobras (Anti-Pod) and the research for WaveBlockers','26','100000','140000','0','r_pwarast',''),
			'scan4'=>array('Fleet Signatures '.$special,'Researches fleetsignatures to penetrate fleet communication shields<br>(<font color=red><i><b>excludes NewAge Spies</b></i></font>)','30','200000','200000','0','c_pscan','c_scan5'),
			'scan5'=>array('NewAge Spies '.$special,'Trains human-robots to fly trough time and space to get enemies\' news<br>(<font color=red><i><b>excludes Fleet Signatures</b></i></font>)','30','200000','200000','0','c_pscan','c_scan4'),
			'wblockers'=>array('Wave Blockers','Researches extra powerfull shields','44','400000','400000','0','r_pcob',''),
			'abot'=>array('AntennaBot '.$special,'Searching for new mining technologies for shipmaterials for a supercruiser<br>(<font color=red><i><b>excludes Energy Efficiency</b></i></font>)','42','900000','650000','0','c_pdu1;c_energy','c_enef'),
			'abot2'=>array('New Hangars','Researching for testfacilities big enough for these ships','52','1300000','700000','0','r_abot',''),
			'enef'=>array('Energy Efficiency '.$special,'Researching techniques to get more Creons in a cell, making it more efficient: new cells available when finished<br>(<font color=red><i><b>excludes AntennaBot</b></i></font>)','46','2000000','1800000','400000','c_energy;c_pdu1','c_abot'),
			'timt'=>array('Time Travel '.$special,'The Theory of traveling through time with all possible units. Cuts 8 ticks off any fleet ETA!<br>(<font color=red><i><b>excludes Resource Boost</b></i></font>)','68','3000000','2000000','200000','c_pscan;c_crystal30;c_metal30','c_rebo'),
			'timt2'=>array('Time Travel (II)','First part of TimeTravel in practice! After finishing this research you save your first 3 ticks on every fleet ETA','68','4000000','4000000','0','r_timt',''),
			'rebo'=>array('Resource Boost '.$special,'Are not efficiency and improvement the greatest goods? Asteroid income increases with 15%!<br>(<font color=red><i><b>excludes Time Travel</b></i></font>)','52','4000000','4000000','1000000','c_crystal30;c_metal30;c_pscan','c_timt'),
			'prsp'=>array('Production Spies','Researching for new camouflage techniques for spies to infiltrate in production facilities, creating new Intel: Production Scans','74','4000000','8000000','1200000','c_wblockers','')
);
/**/




/**
// echo "<pre>";
// print_r($researcharray);

// First, let's get all constructions
$constructionarray = Array();
$c = mysql_query("SELECT * FROM $TABLE[r_d] WHERE SUBSTRING(soort,1,2) = 'c_' ORDER BY id ASC;");
while ($i = mysql_fetch_assoc($c))
{
	// Nieuwe array initiaten
	$tmp = Array();

	// Array vullen
	$tmp[] = $i['naam'];
	$tmp[] = $i['uitleg'];
	$tmp[] = $i['eta'];
	$tmp[] = $i['metal'];
	$tmp[] = $i['crystal'];
	$tmp[] = $i['energy'];
	if ( trim($i['nodig']) )	$nodig = explode(";", $i['nodig']);
	else						$nodig = Array();
	$tmp[] = implode(";", $nodig);
	$tmp[] = $i['excludes'];

	// Array toewijzen
	$constructionarray[substr($i['soort'],2)] = $tmp;
}

// Next, all researches
$researcharray = Array();
$c = mysql_query("SELECT * FROM $TABLE[r_d] WHERE SUBSTRING(soort,1,2) = 'r_' ORDER BY id ASC;");
while ($i = mysql_fetch_assoc($c))
{
	// Nieuwe array initiaten
	$tmp = Array();

	// Array vullen
	$tmp[] = $i['naam'];
	$tmp[] = $i['uitleg'];
	$tmp[] = $i['eta'];
	$tmp[] = $i['metal'];
	$tmp[] = $i['crystal'];
	$tmp[] = $i['energy'];
	if ( trim($i['nodig']) )	$nodig = explode(";", $i['nodig']);
	else						$nodig = Array();
	$tmp[] = implode(";", $nodig);
	$tmp[] = $i['excludes'];

	// Array toewijzen
	$researcharray[substr($i['soort'],2)] = $tmp;
}

/**/






// PRODUCTIONARRAY: SOORT(		  NAAM,				 UITLEG,																															 ETA,	 METAL,		 CRYSTAL,	 RESCON_NODIG)
$productionarray = array(
	'infinitys'			=>array( 'Infinitys',		'Small weak ships who make up their little powers with their agility and speed.',													'10',	'0',		'300',		'c_pinf'),
	'wraiths'			=>array( 'Wraiths',			'Slightly bigger ships with slower though stronger weapons. Also high agility and speed.',											'20',	'1000',		'0',		'c_pwra'),
	'warfrigs'			=>array( 'Warfrigates',		'Warfrigates are real warships! Big ships loaded with lowspeed cannons and highspeed machineguns.',									'30',	'2500',		'2500',		'r_pwarast'),
	'astropods'			=>array( 'Astropods',		'The Astropod is the only ship that can steal roids from others. Astropods are slow and weak and cant shoot. They die on success.',	'40',	'1250',		'1250',		'r_pwarast'),
	'cobras'			=>array( 'Cobras',			'Very strong and armoured ships! Blocks but not kills Astropods. A musthave in the modern army!',									'55',	'0',		'3000',		'r_pcob'),
	'destroyers'		=>array( 'Destroyers',		'The biggest of all heavy duty warships! Quite an investment, but very efficient in combat!',										'60',	'3000',		'2000',		'c_pdes'),
	'scorpions'			=>array( 'Scorpions',		'Great ships, <u>very stealthy</u> and quite manouvrable and fast. A Cobra\'s worst enemy!',										'85',	'2000',		'5000',		'c_psco'),
	'antennas'			=>array( 'Antennas',		'These babys are huge! And I mean HUGE! They\'re worth the resources though. They\'re even fast considering their size.',			'90',	'18000',	'3000',		'c_abot'),
	'a',
	'rcannons'			=>array( 'Reaper Cannons',	'Best PDU against Astropods. This unit is a musthave in your Planetary Defense!',													'20',	'1000',		'0',		'c_pdu1'),
	'avengers'			=>array( 'Avengers',		'Avengers are of a new generation of PDU. They select their targets and are therefore very efficient!',								'30',	'800',		'800',		'c_pdu2'),
	'lstalkers'			=>array( 'Lucius Stalkers',	'Very heavy Defense Unit, which can only be killed by Wraiths. Lucius Stalkers go for the big fishes of the attacker!',				'60',	'3000',		'3000',		'c_pdu1')
);

// SHIPPROPERTIES = NAAM(		  NAAM,			 ETA,	 FUEL,	 							NODIG )
$shipproperties = array(
	'infinitys'			=>array( 'Infinitys',	'4',	'40',								'c_pinf'),
	'wraiths'			=>array( 'Wraiths',		'4',	'48',								'c_pwra'),
	'warfrigs'			=>array( 'Warfrigates',	'5',	'56',								'r_pwarast'),
	'astropods'			=>array( 'Astropods',	'6',	'70',								'r_pwarast'),
	'cobras'			=>array( 'Cobras',		'6',	'72',								'r_pcob'),
	'destroyers'		=>array( 'Destroyers',	'8',	'97',								'c_pdes'),
	'scorpions'			=>array( 'Scorpions',	'7',	'80',								'c_psco'),
	'antennas'			=>array( 'Antennas',	'9',	'700',								'c_abot')
);

// WAVEPARRAY: SOORT(			  NAAM,					 UITLEG,																			 ETA,	 CRYSTAL,	 ENERGY,	 RESCON_NODIG)
$waveParray = array(
	'roidscans'			=>array( 'Asteroidscan',		'You need these scans to find roids. Build as many as you can!',					'10',	'1000',		'1000',		'r_scan1'),
	'a',
	'sectorscans'		=>array( 'Sector-/Basicscan',	'The basic sectorscan provides no particular info; no military intel',				'20',	'1000',		'2000',		'c_bscan'),
	'unitscans'			=>array( 'Unitscan',			'The unitscan shows you all current non-stealth ships of your target',				'30',	'2000',		'4000',		'c_uscan'),
	'pduscans'			=>array( 'PDUscan',				'The PDUscan shows all PDU of the targets planet',									'30',	'4000',		'8000',		'c_pscan'),
	'fleetscans'		=>array( 'Fleetscan',			'The fleetscan shows you ALL of your targets units in his fleet',					'40',	'12000',	'24000',	'c_mscan'),
	'newsscans'			=>array( 'Newsscan',			'The newsscan shows you all of your targets news of the last 24hours',				'40',	'12000',	'24000',	'c_nscan'),
	'productionscans'	=>array( 'Productionscan',		'The productionscan shows the ships in production of the target',					'50',	'10000',	'18000',	'c_prsp'),
	'b',
	'waveamps'			=>array( 'Wave Amplifiers',		'The more you have, the greater the chance is you penetrate your target\'s shields',	'25',	'3000',		'6000',		'c_bscan'),
	'waveblockers'		=>array( 'Wave Blockers',		'The more you have, the smaller the chance is your enemy penetrates your shields',		'60',	'4000',		'10000',	'c_wblockers')
);
$scan_facts = array(
	'sector'			=> '2',
	'unit'				=> '4',
	'pdu'				=> '5',
	'fleet'				=> '7',
	'news'				=> '8',
	'production'		=> '7',
);

// ENERGY ARRAY: SOORT(			  NAAM,				 UITLEG,																		 		ETA,	 METAL,	 	ENERGY,	 	RESCON_NODIG)
$creons = Array(
	'creoncells'		=> array( 'Creoncells',		'One Cell contains 2,871,396 Creons. They make 40 Energy/tick.',						'6',	'6000',		'2000',		'c_energy'),
	'creonboxes'		=> array( 'Creonboxes',		'A Box contains so many Creons to count. They\'re highly radioactive, so watch it!',	'8',	'8000',		'1000',		'c_enef')
);

?>