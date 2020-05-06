<?php

use rdx\ps\Resource;

// Misc
$PWD_MUST_CHANGE			= (bool) $g_prefs->must_change_pwd;			// Password must be changed in order to play when set to TRUE
$CHECK_TIME_BETWEEN_LOGINS	= (int) $g_prefs->between_logins_time;			// If 0, disabled. Else you can't login for this amount of seconds after your last login
$PLANETS_IN_ONE_GALAXY		= (int) $g_prefs->planets_per_galaxy;			// Duh
$RESCON_NEWS_ON				= (bool)(int) $g_prefs->news_for_done_rd;			// If TRUE, the engine will check R&D per planet (takes a bit longer, but sends a Msg when finished)
$GALFORUM_WAIT_FOR_TURN		= (bool)(int) $g_prefs->galaxy_forum_wait_for_turn;	// If TRUE, you cant post 2 posts after eachother without anyone posting in between

$FLEETNAMES					= explode(',', $g_prefs->fleetnames ?: 'Base');
$NUM_OUTGOING_FLEETS		= min(count($FLEETNAMES)-1, (int)$g_prefs->num_outgoing_fleets);


// Preferences
$TICKERTIME		= $g_prefs->tickertime;
$tickertime		= $g_prefs->tickertime;
$MyT			= $g_prefs->tickcount;
$GAMENAME		= $g_prefs->gamename;

$showcolors['metal']	= '#555555';
$showcolors['crystal']	= '#2244dd';
$showcolors['energy']	= '#228800';
$showcolors['gc']		= '#8080ff';
$showcolors['mow']		= '#ff3333';
$showcolors['moc']		= 'green';
$showcolors['mof']		= 'gold';
$showcolors['attack']	= 'red';
$showcolors['defend']	= 'lime';
$showcolors['return']	= '#777777';

$scansavecosts['metal']		= 20000;
$scansavecosts['crystal']	= 20000;
$scansavecosts['energy']	= 50000;

// Spec Ops
$HAVOC_RESDEV		= (bool)(int)$g_prefs->havoc_r_d;
$HAVOC_RESDEV_ETA	= max(0, (int)$g_prefs->r_d_eta_in_havoc);
$HAVOC_MILITARY		= (bool)(int)$g_prefs->havoc_military;
$HAVOC_PRODUCTION	= (bool)(int)$g_prefs->havoc_production;

$titlearray = array(
	'communication'	=> 'messages',
	'galaxynews'	=> 'galaxy_status',
	'resources'		=> 'mining',
	'creonogy'		=> 'energy',
	'ranking'		=> 'universe',
	'military'		=> 'missions',
);

$szRDExcludes = '<span style="color:red;font-weight:bold;">!!</span>';





$g_resources = Resource::all();
