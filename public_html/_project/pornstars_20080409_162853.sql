/** alliances **/
CREATE TABLE `alliances` (
`id` int(11) unsigned NOT NULL auto_increment,
`tag` varchar(32) NOT NULL default '',
`pwd` varchar(255) NOT NULL default '',
`name` varchar(160) NOT NULL default '',
`leader_planet_id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`id`),
UNIQUE KEY `leader_id` (`leader_planet_id`),
KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_all_units **/
CREATE TABLE `d_all_units` (
`id` int(11) unsigned NOT NULL auto_increment,
`T` enum('ship','defence','roidscan','power','scan','amp','block') NOT NULL default 'ship',
`name` varchar(30) NOT NULL,
`explanation` varchar(150) default NULL,
`build_eta` int(11) unsigned NOT NULL,
`metal` int(11) unsigned NOT NULL,
`crystal` int(11) unsigned NOT NULL,
`energy` int(11) unsigned NOT NULL,
`move_eta` int(11) unsigned NOT NULL,
`fuel` int(11) unsigned NOT NULL,
`r_d_required_id` int(11) unsigned NOT NULL,
`is_stealth` enum('0','1') NOT NULL default '0',
`is_mobile` enum('0','1') NOT NULL default '0',
`is_offensive` enum('0','1') default '0',
`o` smallint(5) unsigned NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `r_d_required_id` (`r_d_required_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_combat_stats **/
CREATE TABLE `d_combat_stats` (
`shooting_unit_id` int(11) NOT NULL,
`receiving_unit_id` int(11) NOT NULL,
`ratio` float(5,4) unsigned NOT NULL,
PRIMARY KEY  (`shooting_unit_id`,`receiving_unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_defence **/
CREATE TABLE `d_defence` (
`id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_news_subjects **/
CREATE TABLE `d_news_subjects` (
`id` int(11) unsigned NOT NULL default '0',
`name` varchar(30) NOT NULL,
`image` varchar(30) NOT NULL,
`const_name` varchar(20) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_power **/
CREATE TABLE `d_power` (
`id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_r_d_available **/
CREATE TABLE `d_r_d_available` (
`id` int(11) unsigned NOT NULL auto_increment,
`name` varchar(30) NOT NULL,
`T` enum('r','d') NOT NULL default 'r',
`explanation` varchar(150) NOT NULL,
`eta` tinyint(4) unsigned NOT NULL default '0',
`metal` int(11) unsigned NOT NULL default '0',
`crystal` int(11) unsigned NOT NULL default '0',
`energy` int(11) unsigned NOT NULL default '0',
`race_id` int(11) unsigned default NULL,
PRIMARY KEY  (`id`),
KEY `race_id` (`race_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_r_d_excludes **/
CREATE TABLE `d_r_d_excludes` (
`r_d_id` int(11) unsigned NOT NULL,
`r_d_excludes_id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`r_d_id`,`r_d_excludes_id`),
KEY `r_d_id` (`r_d_id`),
KEY `r_d_excludes_id` (`r_d_excludes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_r_d_requires **/
CREATE TABLE `d_r_d_requires` (
`r_d_id` int(11) unsigned NOT NULL,
`r_d_requires_id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`r_d_id`,`r_d_requires_id`),
KEY `r_d_id` (`r_d_id`),
KEY `r_d_requires_id` (`r_d_requires_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_r_d_results **/
CREATE TABLE `d_r_d_results` (
`id` int(11) unsigned NOT NULL auto_increment,
`type` enum('travel_eta','r_d_eta','metal_income','crystal_income','energy_income','r_d_costs','fuel_use') NOT NULL,
`done_r_d_id` int(11) unsigned NOT NULL,
`change` float(11,2) NOT NULL,
`unit` enum('real','pct') NOT NULL default 'real',
`explanation` varchar(255) NOT NULL,
`enabled` enum('0','1') NOT NULL default '1',
`o` tinyint(4) NOT NULL,
PRIMARY KEY  (`id`),
KEY `r_d_id` (`done_r_d_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_races **/
CREATE TABLE `d_races` (
`id` int(11) unsigned NOT NULL auto_increment,
`race` varchar(255) NOT NULL,
`race_plural` varchar(255) NOT NULL,
PRIMARY KEY  (`id`),
UNIQUE KEY `name` (`race`),
UNIQUE KEY `name_plural` (`race_plural`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_ships **/
CREATE TABLE `d_ships` (
`id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** d_waves **/
CREATE TABLE `d_waves` (
`id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** defence_on_planets **/
CREATE TABLE `defence_on_planets` (
`defence_id` int(11) unsigned NOT NULL,
`planet_id` int(11) unsigned NOT NULL,
`amount` int(11) unsigned NOT NULL,
PRIMARY KEY  (`defence_id`),
KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** fleets **/
CREATE TABLE `fleets` (
`id` int(11) unsigned NOT NULL auto_increment,
`owner_planet_id` int(11) unsigned NOT NULL,
`destination_planet_id` int(11) unsigned default NULL,
`eta` tinyint(4) unsigned NOT NULL default '0',
`starteta` tinyint(4) unsigned NOT NULL default '0',
`action` enum('attack','defend','return') default NULL,
`actiontime` tinyint(4) unsigned NOT NULL default '0',
`startactiontime` tinyint(4) unsigned NOT NULL default '0',
`fleetname` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL default '0',
`activated` enum('0','1') NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `owner_planet_id` (`owner_planet_id`),
KEY `destination_id` (`destination_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Elke player kan max 3 fleets hebben, waarvan 1 _base';

/** galaxies **/
CREATE TABLE `galaxies` (
`id` int(11) unsigned NOT NULL auto_increment,
`x` tinyint(4) unsigned NOT NULL default '0',
`y` tinyint(4) unsigned NOT NULL default '0',
`name` varchar(40) NOT NULL default 'Far Far Away',
`picture` varchar(255) NOT NULL default 'images/death.jpg',
`gc_message` varchar(255) NOT NULL default 'Welcome fearless rulers',
`gc_planet_id` int(11) unsigned default NULL,
`moc_planet_id` int(11) unsigned default NULL,
`mow_planet_id` int(11) unsigned default NULL,
`mof_planet_id` int(11) unsigned default NULL,
`fund_metal` bigint(20) unsigned NOT NULL default '0',
`fund_crystal` bigint(20) unsigned NOT NULL default '0',
`fund_energy` bigint(20) unsigned NOT NULL default '0',
PRIMARY KEY  (`id`),
UNIQUE KEY `x-y` (`x`,`y`),
KEY `gc_planet_id` (`gc_planet_id`),
KEY `moc_planet_id` (`moc_planet_id`),
KEY `mow_planet_id` (`mow_planet_id`),
KEY `mof_planet_id` (`mof_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** logbook **/
CREATE TABLE `logbook` (
`id` bigint(20) NOT NULL auto_increment,
`uid` int(11) NOT NULL default '0',
`action` varchar(20) NOT NULL default '',
`time` bigint(20) NOT NULL default '0',
`myt` bigint(20) NOT NULL default '0',
`text` text NOT NULL,
`ip` varchar(20) NOT NULL default '',
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** mail **/
CREATE TABLE `mail` (
`id` int(11) NOT NULL auto_increment,
`to_planet_id` int(11) unsigned default NULL,
`from_planet_id` int(11) unsigned default NULL,
`utc_sent` int(10) unsigned NOT NULL,
`myt_sent` int(11) unsigned NOT NULL default '0',
`message` text NOT NULL,
`seen` enum('0','1') NOT NULL default '0',
`deleted` enum('0','1') NOT NULL default '0',
`is_help_msg` enum('0','1') NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `to_planet_id` (`to_planet_id`),
KEY `from_planet_id` (`from_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** news **/
CREATE TABLE `news` (
`id` int(11) NOT NULL auto_increment,
`planet_id` int(11) unsigned NOT NULL,
`utc_time` int(10) unsigned NOT NULL default '0',
`myt` int(11) unsigned NOT NULL default '0',
`news_subject_id` int(11) unsigned NOT NULL,
`message` text NOT NULL,
`seen` enum('0','1') NOT NULL default '0',
`deleted` enum('0','1') NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `planet_id` (`planet_id`),
KEY `news_subject_id` (`news_subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** old_intel **/
CREATE TABLE `old_intel` (
`id` int(11) NOT NULL auto_increment,
`planet_id` int(11) unsigned NOT NULL,
`target_planet_id` int(11) unsigned NOT NULL,
`utc_time` int(10) unsigned NOT NULL,
`myt` int(11) unsigned NOT NULL,
`soort` varchar(22) NOT NULL default 'sectors',
`result` text NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** online **/
CREATE TABLE `online` (
`id` int(11) NOT NULL auto_increment,
`uid` int(11) NOT NULL default '0',
`ip` varchar(99) NOT NULL default '',
`time` bigint(20) NOT NULL default '0',
`uniek` varchar(99) NOT NULL default '',
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** planets **/
CREATE TABLE `planets` (
`id` int(11) unsigned NOT NULL auto_increment,
`email` varchar(30) NOT NULL default '',
`password` varchar(255) NOT NULL default '',
`activationcode` varchar(255) NOT NULL default '',
`oldpwd` enum('0','1') NOT NULL default '1',
`race_id` int(11) unsigned NOT NULL,
`rulername` varchar(40) NOT NULL default '',
`planetname` varchar(40) NOT NULL default '',
`galaxy_id` int(11) unsigned NOT NULL,
`z` tinyint(4) unsigned NOT NULL,
`crystal` bigint(20) NOT NULL default '0',
`metal` bigint(20) NOT NULL default '1000',
`energy` bigint(20) NOT NULL default '0',
`metal_asteroids` int(10) unsigned NOT NULL default '0',
`crystal_asteroids` int(10) unsigned NOT NULL default '0',
`energy_asteroids` int(10) unsigned NOT NULL default '0',
`uninitiated_asteroids` int(10) unsigned NOT NULL default '0',
`lastlogin` int(10) unsigned NOT NULL default '0',
`lastaction` int(10) unsigned NOT NULL default '0',
`alliance_id` int(11) unsigned default NULL,
`score` bigint(20) unsigned NOT NULL,
`sleep` int(10) unsigned NOT NULL default '0',
`nextsleep` int(10) unsigned NOT NULL default '0',
`closed` enum('0','1') NOT NULL default '0',
`voted_for_planet_id` int(11) unsigned default NULL,
`newbie_ticks` smallint(5) unsigned NOT NULL default '100',
`journal` text NOT NULL,
`show_all_r_d` enum('0','1') NOT NULL default '1',
`autoscansave` enum('0','1') NOT NULL default '0',
`unihash` varchar(40) default NULL,
PRIMARY KEY  (`id`),
UNIQUE KEY `rulername` (`rulername`),
UNIQUE KEY `email` (`email`),
UNIQUE KEY `planetname` (`planetname`),
UNIQUE KEY `z-in-galaxy` (`galaxy_id`,`z`),
KEY `tag` (`alliance_id`),
KEY `race_id` (`race_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** politics **/
CREATE TABLE `politics` (
`id` int(11) unsigned NOT NULL auto_increment,
`parent_thread_id` int(11) unsigned default NULL,
`galaxy_id` int(11) unsigned NOT NULL,
`utc_time` int(10) unsigned NOT NULL,
`title` varchar(40) default NULL,
`message` text NOT NULL,
`creator_planet_id` int(11) unsigned NOT NULL,
`is_deleted` enum('0','1') NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `parent_thread_id` (`parent_thread_id`),
KEY `creator_planet_id` (`creator_planet_id`),
KEY `galaxy_id` (`galaxy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** power_on_planets **/
CREATE TABLE `power_on_planets` (
`power_id` int(11) unsigned NOT NULL,
`planet_id` int(11) unsigned NOT NULL,
`amount` int(11) unsigned NOT NULL,
PRIMARY KEY  (`power_id`,`planet_id`),
KEY `power_id` (`power_id`),
KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** prefs **/
CREATE TABLE `prefs` (
`id` int(11) unsigned NOT NULL auto_increment,
`gamename` varchar(24) NOT NULL default '',
`tickertime` int(11) unsigned NOT NULL default '20',
`ticker_on` enum('1','0') NOT NULL default '1',
`ticker_password` varchar(40) NOT NULL default '',
`tickcount` int(11) unsigned NOT NULL default '0',
`last_tick` int(11) unsigned NOT NULL default '0',
`general_gamestoptick` int(11) unsigned NOT NULL default '0',
`military_attack` enum('1','0') NOT NULL default '1',
`military_attack_ticks` tinyint(3) unsigned NOT NULL default '5',
`military_defend` enum('1','0') NOT NULL default '1',
`military_defend_ticks` tinyint(3) unsigned NOT NULL default '10',
`military_scorelimit` tinyint(4) unsigned NOT NULL default '0',
`general_login` enum('1','0') NOT NULL default '1',
`general_signup` enum('1','0') NOT NULL default '1',
`general_adminmsg` text NOT NULL,
`autologout` enum('0','1') NOT NULL default '0',
`debug_mode` enum('1','0') NOT NULL default '0',
`logincode` enum('0','1') NOT NULL default '0',
`ticker_autorefresh` enum('0','1') NOT NULL default '1',
`tickertime_override` enum('0','1') NOT NULL default '0',
`must_change_pwd` enum('0','1') NOT NULL default '0',
`between_logins_time` tinyint(4) unsigned NOT NULL default '60',
`planets_per_galaxy` tinyint(4) unsigned NOT NULL default '5',
`news_for_done_rd` enum('0','1') NOT NULL default '1',
`galaxy_forum_wait_for_turn` enum('0','1') NOT NULL default '1',
`num_outgoing_fleets` tinyint(3) unsigned NOT NULL default '2',
`fleetnames` varchar(255) NOT NULL,
`havoc_r_d` enum('0','1') NOT NULL default '0',
`r_d_eta_in_havoc` tinyint(4) NOT NULL default '0',
`havoc_military` enum('0','1') NOT NULL default '0',
`havoc_production` enum('0','1') NOT NULL default '0',
`admin_planet_ids` varchar(60) NOT NULL default '0',
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** production_per_planet **/
CREATE TABLE `production_per_planet` (
`id` int(11) unsigned NOT NULL auto_increment,
`planet_id` int(11) unsigned NOT NULL,
`unit_id` int(11) unsigned NOT NULL,
`eta` tinyint(4) unsigned NOT NULL,
`amount` int(11) unsigned NOT NULL,
PRIMARY KEY  (`id`),
KEY `user_id` (`planet_id`),
KEY `unit_id` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** r_d_per_planet **/
CREATE TABLE `r_d_per_planet` (
`r_d_id` int(11) unsigned NOT NULL,
`planet_id` int(11) unsigned NOT NULL,
`eta` tinyint(4) unsigned NOT NULL,
PRIMARY KEY  (`r_d_id`,`planet_id`),
KEY `r_d_id` (`r_d_id`),
KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** ships_in_fleets **/
CREATE TABLE `ships_in_fleets` (
`fleet_id` int(11) unsigned NOT NULL,
`ship_id` int(11) unsigned NOT NULL,
`amount` int(11) unsigned NOT NULL default '0',
PRIMARY KEY  (`fleet_id`,`ship_id`),
KEY `fleet_id` (`fleet_id`),
KEY `ship_id` (`ship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/** waves_on_planets **/
CREATE TABLE `waves_on_planets` (
`wave_id` int(11) unsigned NOT NULL,
`planet_id` int(11) unsigned NOT NULL,
`amount` int(11) unsigned NOT NULL,
PRIMARY KEY  (`planet_id`,`wave_id`),
KEY `planet_id` (`planet_id`),
KEY `wave_id` (`wave_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/** alliances **/
INSERT INTO `alliances` VALUES ('1','RDX','1234567890','Dirkxes','1');

/** d_all_units **/
INSERT INTO `d_all_units` VALUES ('1','ship','Infinitys','Small weak ships who make up their little powers with their agility and speed','10','0','300','0','8','40','4','0','1','1','1'),
('2','ship','Wraiths','Slightly bigger ships with slower though stronger weapons. Also high agility and speed','20','1000','0','0','8','48','8','0','1','1','2'),
('3','ship','Warfrigates','Warfrigates are real warships! Big ships loaded with lowspeed cannons and highspeed machineguns','30','2500','2500','0','10','56','33','0','1','1','3'),
('4','ship','Astropods','The Astropod is the only ship that can steal roids from others. Astropods are slow and weak and cant shoot. They die on success','40','1250','1250','0','12','70','33','0','1','0','4'),
('5','ship','Cobras','Very strong and armoured ships! Blocks but not kills Astropods. A musthave in the modern army','55','0','3000','0','12','72','35','1','1','1','5'),
('6','ship','Destroyers','The biggest of all heavy duty warships! Quite an investment, but very efficient in combat','60','3000','2000','0','16','97','12','0','1','1','6'),
('7','ship','Scorpions','Great ships, <u>very stealthy</u> and quite manouvrable and fast. A Cobra\'s worst enemy','85','2000','5000','0','14','80','13','0','1','1','7'),
('8','ship','Antennas','These babys are huge! And I mean HUGE! They\'re barely worth the resources though. But they\'re fast considering their size','90','18000','3000','0','18','700','20','1','1','1','8'),
('9','defence','Reaper Cannons','Best PDU against Astropods. This unit is a musthave in your Planetary Defense','20','1000','0','0','0','0','17','0','0','1','9'),
('10','defence','Avengers','Avengers are of a new generation of PDU. They select their targets and are therefore very efficient','30','800','800','0','0','0','18','0','0','1','10'),
('11','defence','Lucius Stalkers','Very heavy Defense Unit, which can only be killed by Wraiths. Lucius Stalkers go for the big fishes of the attacker','60','3000','3000','0','0','0','17','0','0','1','11'),
('12','power','Creon Cells','One Cell contains 2,871,396 Creons. They make 40 Energy/tick','6','6000','0','0','0','40','5','0','0','1','12'),
('13','power','Creon Boxes','A Box contains so many Creons to count. They\'re highly radioactive, so be careful','6','8000','0','1000','0','70','21','0','0','1','13'),
('14','roidscan','Asteroid Scan','You need these scans to find roids. Build as many as you can','10','0','1000','1000','0','0','26','0','0','1','1'),
('15','scan','Sector Scan','The basic sectorscan provides no particular info; no military intel','20','0','1000','2000','0','2','3','0','1','1','2'),
('16','scan','Unit Scan','The unitscan shows you all current non-stealth ships of your target','30','0','2000','4000','0','4','9','0','1','1','3'),
('17','scan','Defence Scan','The PDUscan shows all PDU of the targets planet','30','0','4000','8000','0','5','14','0','1','1','4'),
('18','scan','Fleet Scan','The fleetscan shows you ALL of your targets units in his fleet','40','0','12000','24000','0','7','15','0','1','1','5'),
('19','scan','News Scan','The newsscan shows you all of your targets news of the last 24hours','40','0','12000','24000','0','8','16','0','1','1','6'),
('20','scan','Production Scan','The productionscan shows the ships in production of the target','50','0','10000','18000','0','7','25','0','1','1','7'),
('21','amp','Wave Amplifiers','The more you have, the greater the chance is you penetrate your target\'s shields','25','0','6000','3000','0','0','3','0','0','1','9'),
('22','block','Wave Blockers','The more you have, the smaller the chance is your enemy penetrates your shields','60','0','4000','10000','0','0','19','0','0','1','10'),
('23','scan','Political Scan','Scans the politics of your target planet. Great way to penetrate internal communications','55','0','8000','8000','0','8','47','0','1','1','8'),
('24','roidscan','Asteroid Scan','You need these....','10','0','0','2500','0','0','26','0','0','1','2'),
('25','roidscan','Asteroid Scan','You need these...','10','0','2500','0','0','0','26','0','0','1','3'),
('26','block','Wave Blockers','...','62','0','0','15000','0','0','19','','','1','11');

/** d_combat_stats **/
INSERT INTO `d_combat_stats` VALUES ('1','1','0.0417'),
('1','2','0.0083'),
('1','3','0.0125'),
('1','4','0.0125'),
('1','5','0.0167'),
('1','6','0.0021'),
('1','7','0.0029'),
('1','8','0.0008'),
('2','1','0.1250'),
('2','2','0.1042'),
('2','3','0.0208'),
('2','4','0.0125'),
('2','5','0.0042'),
('2','6','0.1250'),
('2','7','0.0208'),
('2','8','0.0183'),
('2','11','0.0583'),
('3','1','2.0833'),
('3','2','0.3333'),
('3','3','0.0417'),
('3','4','0.0458'),
('3','5','0.0125'),
('3','6','0.0208'),
('3','7','0.0208'),
('3','8','0.0417'),
('3','9','0.2917'),
('5','1','2.0000'),
('5','2','0.1667'),
('5','3','0.0875'),
('5','5','0.0417'),
('5','6','0.1000'),
('5','7','0.0750'),
('5','8','0.0067'),
('6','1','0.6667'),
('6','2','0.1750'),
('6','3','0.2667'),
('6','4','0.1042'),
('6','5','0.2083'),
('6','6','0.0417'),
('6','7','0.0333'),
('6','8','0.0167'),
('6','10','0.4583'),
('7','1','2.5000'),
('7','2','0.2917'),
('7','3','0.1042'),
('7','4','0.1125'),
('7','5','0.8333'),
('7','6','0.1500'),
('7','7','0.0417'),
('7','8','0.1000'),
('8','1','3.3333'),
('8','2','0.5000'),
('8','4','0.4583'),
('8','5','0.7500'),
('8','6','0.9167'),
('8','7','0.2917'),
('8','8','0.0417'),
('8','11','0.4000'),
('9','1','0.0700'),
('9','4','0.0600'),
('9','8','0.0350'),
('10','2','0.1000'),
('10','3','0.0700'),
('10','5','0.0400'),
('11','6','0.0800'),
('11','7','0.1000');

/** d_defence **/
INSERT INTO `d_defence` VALUES ('9'),
('10'),
('11');

/** d_news_subjects **/
INSERT INTO `d_news_subjects` VALUES ('0','Unknown','news_unknown.gif','UNKNOWN'),
('1','Alliance','news_alliance.gif','ALLIANCE'),
('2','Combat Report','news_combatreport.gif','COMBAT'),
('3','Friendly Incoming','news_friendly_incoming.gif','FRIENDLY_INCOMING'),
('4','Friendly Going Out','news_friendly_outgoing.gif','FRIENDLY_OUTGOING'),
('5','Galaxy','news_galaxy.gif','GALAXY'),
('6','Hostile Incoming','news_hostile_incoming.gif','HOSTILE_INCOMING'),
('7','Hostile Going Out','news_hostile_outgoing.gif','HOSTILE_OUTGOING'),
('8','R & D','news_r_d.gif','R_D'),
('9','Waves','news_waves.gif','WAVES');

/** d_power **/
INSERT INTO `d_power` VALUES ('12'),
('13');

/** d_r_d_available **/
INSERT INTO `d_r_d_available` VALUES ('1','Crystal Refinery','d','Gain +1500 extra Crystal','4','0','0','0',NULL),
('2','Metal Refinery','d','Gain +1500 extra Metal','4','500','500','0',NULL),
('3','Sector Scanning','d','Enables Sectorscans','8','1000','1000','0',NULL),
('4','First Airport','d','Enables the production of Inifinitys','10','1000','1000','0',NULL),
('5','Making Energy','d','Enables the production of a Creon living environment','9','3000','3000','0',NULL),
('6','Get More Crystal','d','Gain +3000 extra Crystal','11','6000','9000','0',NULL),
('7','Get More Metal','d','Gain +3000 extra Metal','13','9000','6000','0',NULL),
('8','Robot Factory','d','Enables the production of Wraiths','14','8000','4000','0',NULL),
('9','Unit Scanning','d','Enables Unitscans','14','12000','20000','0',NULL),
('10','Hardcore Crystal','d','Builds machines who provide you an extra +6000 Crystal','20','25000','45000','0',NULL),
('11','Hardcore Metal','d','Builds machines who provide you an extra +6000 Metal','24','50000','30000','0',NULL),
('12','Destroyer Factory','d','Enables the production of Destroyers','36','80000','80000','0',NULL),
('13','Scorpion Factory','d','Enables the production of Scorpions','44','120000','200000','0',NULL),
('14','Defence Scanning','d','Enables Defence Scans','30','200000','300000','0',NULL),
('15','Fleet Scanners','d','Enables Fleet Scans','40','400000','500000','0',NULL),
('16','High-speed-plasma Spies','d','Enables News Scans by penetrating the high-speed-plasma of your target','44','400000','800000','0',NULL),
('17','Defence Machines','d','Enables the production of basic Planetary Defence Units (Reaper Cannons and Lucius Stalkers)','36','600000','550000','0',NULL),
('18','Super Defence','d','Enables the production of Avengers','42','700000','700000','0',NULL),
('19','Wave Blockers','d','Extra holding and forcefull shields to block infiltrationscans','50','950000','1200000','0',NULL),
('20','SuperCruisers','d','Only hangars and upgrading factories are yet necessary','56','2200000','1100000','0','1'),
('21','Creons in Boxes','d','Boxes being built which can contain a large amount of Creons','52','2800000','4000000','800000',NULL),
('22','Hot \'n\' Thick','d','Improving pantser and heatresistment on all moving units. After this, you save another 5 ticks on all fleet ETA','74','2000000','2800000','0',NULL),
('23','Work, Bitch, Work!','d','Construction of weapons to convince your professors to work more and harder','42','4000000','2400000','0',NULL),
('24','Building Space Warehouses','d','Increasing storage facilities. Asteroid income should rise considerably','38','4000000','4000000','0',NULL),
('25','Production Spy Suits','d','Creating the suits Production Spies get to wear, to infiltrate enemy production facilities','92','800000','800000','800000',NULL),
('26','About Scanning','r','Enables the production of huge-scale radars (+asteroidscans)','6','0','1000','0',NULL),
('27','About Energy','r','Researches the art of Creon growth','4','0','850','0',NULL),
('28','More Crystal','r','Researches more efficient mining methods','8','2000','2000','0',NULL),
('29','More Metal','r','Researches more efficient mining methods','10','3000','3000','0',NULL),
('30','Unit Patterns','r','Researching the early unitscans','10','6000','6000','0',NULL),
('31','\"Blind Diggers\" for Crystal','r','Researching machines who dig to the core','16','20000','30000','0',NULL),
('32','\"Blind Diggers\" for Metal','r','Researching machines who dig to the core','20','30000','20000','0',NULL),
('33','Tank Building','r','Enables the production of Warfrigates and Astropods','16','48000','40000','0',NULL),
('34','Defence Patterns','r','Researching PDU-methods','22','70000','90000','20000',NULL),
('35','EMP Studies','r','Enables the production of Cobras and the research for WaveBlockers','26','100000','140000','0',NULL),
('36','Fleet Signatures','r','Researches fleetsignatures to penetrate fleet communication shields','30','200000','200000','0',NULL),
('37','NewAge Spies','r','Trains human-robots to fly trough time and space to get enemies\' news','30','200000','200000','0',NULL),
('38','Wave Blockers','r','Researches extra powerfull shields','44','400000','400000','0',NULL),
('39','AntennaBot','r','Searching for new mining technologies for shipmaterials for a supercruiser','42','900000','650000','0',NULL),
('40','New Hangars','r','Researching for testfacilities big enough for these ships','52','1300000','700000','0','1'),
('41','Energy Efficiency','r','Researching techniques to get more Creons in a cell, making it more efficient: new cells available when finished','46','2000000','1800000','400000','1'),
('42','Time Travel','r','The Theory of traveling through time with all possible units. Cuts 8 ticks off any fleet ETA!','68','3000000','2000000','200000',NULL),
('43','Time Travel (II)','r','First part of TimeTravel in practice! After finishing this research you save your first 3 ticks on every fleet ETA','68','4000000','4000000','0',NULL),
('44','Resource Boost','r','Are not efficiency and improvement the greatest goods? Asteroid income increases with 15%!','52','4000000','4000000','1000000',NULL),
('45','Production Spies','r','Researching for new camouflage techniques for spies to infiltrate in production facilities, creating new Intel: Production Scans','74','4000000','8000000','1200000',NULL),
('46','Infiltraitors','r','Researches and teaches AI moleculair robots to infiltrate organisations like a Galaxy','80','3800000','3800000','0',NULL),
('47','Infiltraition Syringes','d','Enables Political Scans','68','6500000','3800000','5600000',NULL),
('48','Stress Releasers','d','Development of pills to releave workmen of stress and work harder','30','20000','20000','20000',NULL),
('49','Work floor management','r','Teaches evolvement management for efficient desk & factory work','30','25000','25000','15000',NULL),
('50','R&D RC','r','It\'s worth it!','40','40000','35000','25000',NULL);

/** d_r_d_excludes **/
INSERT INTO `d_r_d_excludes` VALUES ('36','37'),
('37','36'),
('39','41'),
('41','39'),
('42','44'),
('44','42'),
('45','46'),
('46','45');

/** d_r_d_requires **/
INSERT INTO `d_r_d_requires` VALUES ('3','26'),
('4','1'),
('4','2'),
('5','27'),
('6','28'),
('7','29'),
('8','4'),
('9','30'),
('10','31'),
('11','32'),
('12','33'),
('13','12'),
('14','34'),
('15','36'),
('16','37'),
('17','35'),
('18','17'),
('19','38'),
('20','40'),
('21','41'),
('22','43'),
('23','44'),
('24','23'),
('25','45'),
('28','1'),
('29','2'),
('30','1'),
('30','2'),
('30','3'),
('31','6'),
('32','7'),
('33','8'),
('34','9'),
('35','33'),
('36','14'),
('37','14'),
('38','13'),
('38','17'),
('39','5'),
('39','17'),
('40','39'),
('41','5'),
('41','17'),
('42','10'),
('42','11'),
('42','14'),
('43','42'),
('44','10'),
('44','11'),
('44','14'),
('45','19'),
('46','19'),
('47','46'),
('48','5'),
('49','48'),
('50','31'),
('50','32');

/** d_r_d_results **/
INSERT INTO `d_r_d_results` VALUES ('1','travel_eta','43','-3.00','real','Cuts 3 ticks off of your travel eta','1','1'),
('2','travel_eta','22','-5.00','real','Cuts 5 ticks off of your travel eta','1','2'),
('3','metal_income','24','1.15','pct','Increase your asteroid income with 15%','1','3'),
('4','r_d_eta','48','0.75','pct','Cuts 25% off of your R & D time','1','5'),
('5','r_d_eta','49','0.75','pct','Cuts 25% off of your R & D time','1','6'),
('6','r_d_costs','50','0.50','pct','R&D costs reduced to half','1','7'),
('7','crystal_income','1','1500.00','real','Receive 1500 planetary crystal','1','8'),
('8','metal_income','2','1500.00','real','Receive 1500 planetary metal','1','9'),
('9','crystal_income','6','3000.00','real','Receive 3000 planetary crystal','1','10'),
('10','metal_income','7','3000.00','real','Receive 3000 planetary metal','1','11'),
('11','crystal_income','10','6000.00','real','Receive 6000 planetary crystal','1','12'),
('12','metal_income','11','6000.00','real','Receive 6000 planetary metal','1','13'),
('13','crystal_income','24','1.15','pct','Increase your asteroid income with 15%','1','5');

/** d_races **/
INSERT INTO `d_races` VALUES ('1','Human','Humans'),
('2','Centaur','Centaurs'),
('3','Frog','Frogs');

/** d_ships **/
INSERT INTO `d_ships` VALUES ('1'),
('2'),
('3'),
('4'),
('5'),
('6'),
('7'),
('8');

/** d_waves **/
INSERT INTO `d_waves` VALUES ('14'),
('15'),
('16'),
('17'),
('18'),
('19'),
('20'),
('21'),
('22'),
('23'),
('24'),
('25'),
('26');

/** defence_on_planets **/
INSERT INTO `defence_on_planets` VALUES ('9','1','800'),
('10','1','700');

/** fleets **/
INSERT INTO `fleets` VALUES ('1','1',NULL,'0','0',NULL,'0','0','0','0'),
('2','1',NULL,'0','0',NULL,'0','0','1','1'),
('3','1','2','8','8','attack','6','6','2','0'),
('4','2',NULL,'0','0',NULL,'0','0','0','0'),
('5','2',NULL,'0','0',NULL,'0','0','1','0'),
('6','2',NULL,'0','0',NULL,'0','0','2','0'),
('7','3',NULL,'0','0',NULL,'0','0','0','0'),
('8','3',NULL,'0','0',NULL,'0','0','1','0'),
('9','3',NULL,'0','0',NULL,'0','0','2','0'),
('10','4',NULL,'0','0',NULL,'0','0','0','0'),
('11','4',NULL,'0','0',NULL,'0','0','1','0'),
('12','4',NULL,'0','0',NULL,'0','0','2','0'),
('13','4',NULL,'0','0',NULL,'0','0','3','0'),
('14','5',NULL,'0','0',NULL,'0','0','0','0'),
('15','5',NULL,'0','0',NULL,'0','0','1','0'),
('16','5',NULL,'0','0',NULL,'0','0','2','0'),
('17','5',NULL,'0','0',NULL,'0','0','3','0'),
('18','6',NULL,'0','0',NULL,'0','0','0','0'),
('19','6',NULL,'0','0',NULL,'0','0','1','0'),
('20','6',NULL,'0','0',NULL,'0','0','2','0'),
('21','6',NULL,'0','0',NULL,'0','0','3','0'),
('22','1',NULL,'0','0',NULL,'0','0','3','1'),
('23','2',NULL,'0','0',NULL,'0','0','3','0'),
('24','3',NULL,'0','0',NULL,'0','0','3','0');

/** galaxies **/
INSERT INTO `galaxies` VALUES ('1','1','1','FroM HeLL','images/death.jpg','Blaaaaaaaaaaaaat! \'yoo
Teefjens','1','2',NULL,'3','48545','13875795','95'),
('2','1','2','fierlus','images/death.jpg','MoC, make connections with other galaxies in this quadrant
MoF, make sure everybody!! donates to the Fund!! We gotta help new planets in our galaxy to help eachother!','4','5',NULL,'6','0','0','0');

/** logbook **/
INSERT INTO `logbook` VALUES ('1','1','login','1139859266','2886','','127.0.0.1'),
('2','0','affairs','1139867728','2886','Seems like y=2 from x=1 has been elected GC!','127.0.0.1'),
('3','0','affairs','1139867734','2886','Seems like y=2 from x=1 has been elected GC!','127.0.0.1'),
('4','1','login','1139868418','2886','','127.0.0.1'),
('5','1','login','1139872486','2888','','127.0.0.1'),
('6','1','login','1139872529','2888','','127.0.0.1'),
('7','1','affairs','1139873179','2888','Seems like y=2 from x=1 has been elected GC!','127.0.0.1'),
('8','1','affairs','1139873959','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('9','1','affairs','1139874172','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('10','1','affairs','1139874190','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('11','1','affairs','1139874195','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('12','1','affairs','1139874214','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('13','1','affairs','1139874249','2888','Seems like y=3 from x=1 has been elected GC!','127.0.0.1'),
('14','1','affairs','1139874262','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('15','1','affairs','1139874573','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('16','1','affairs','1139874606','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('17','1','affairs','1139874612','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('18','1','affairs','1139874616','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('19','1','affairs','1139874685','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('20','1','affairs','1139874690','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('21','1','affairs','1139874697','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('22','1','affairs','1139874697','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('23','1','affairs','1139874698','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('24','1','affairs','1139874767','2888','Seems like y=3 from x=1 has been elected GC!','127.0.0.1'),
('25','1','affairs','1139874772','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('26','1','affairs','1139874779','2888','Seems like y=3 from x=1 has been elected GC!','127.0.0.1'),
('27','1','affairs','1139875005','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('28','1','affairs','1139875012','2888','Seems like y=3 from x=1 has been elected GC!','127.0.0.1'),
('29','1','affairs','1139875014','2888','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('30','1','affairs','1139875020','2888','Seems like y=3 from x=1 has been elected GC!','127.0.0.1'),
('31','1','login','1140795203','2888','','127.0.0.1'),
('32','1','login','1140800028','3081','','127.0.0.1'),
('33','1','login','1140800098','3081','','127.0.0.1'),
('34','1','logout','1140800106','3081','','127.0.0.1'),
('35','1','login','1140800118','3081','','127.0.0.1'),
('36','1','logout','1140800389','3081','','127.0.0.1'),
('37','1','login','1140800401','3081','','127.0.0.1'),
('38','1','logout','1140800443','3081','','127.0.0.1'),
('39','1','login','1140800719','3081','','127.0.0.1'),
('40','1','login','1140877623','3081','','127.0.0.1'),
('41','1','affairs','1140882459','3237','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('42','1','affairs','1140882474','3238','Seems like y=3 from x=1 has been elected GC!','127.0.0.1'),
('43','1','affairs','1140882476','3238','Seems like y=1 from x=1 has been elected GC!','127.0.0.1'),
('44','1','login','1140883267','3239','','127.0.0.1'),
('45','1','logout','1140884204','3301','','127.0.0.1'),
('46','1','login','1140961594','3301','','127.0.0.1'),
('47','1','logout','1140963354','3301','','127.0.0.1'),
('48','3','login','1140963372','3301','','127.0.0.1'),
('49','3','alliance','1140963412','3301','Just joined <b>RDX</b>], pwd = tag21a3baa','127.0.0.1'),
('50','3','logout','1140963602','3304','','127.0.0.1'),
('51','1','login','1140963612','3304','','127.0.0.1'),
('52','1','login','1140987360','3460','','127.0.0.1'),
('53','1','logout','1140988998','3491','','127.0.0.1'),
('54','3','login','1140989021','3491','','127.0.0.1'),
('55','3','logout','1140990525','3640','','127.0.0.1'),
('56','1','login','1140990536','3640','','127.0.0.1'),
('57','1','login','1141065481','4190','','127.0.0.1'),
('58','1','logout','1141065596','4190','','127.0.0.1'),
('59','1','login','1141068388','4190','','127.0.0.1'),
('60','1','login','1141134272','4190','','127.0.0.1'),
('61','1','login','1141135795','4190','','127.0.0.1'),
('62','1','login','1144182628','4190','','127.0.0.1'),
('63','1','login','1148554601','4191','','127.0.0.1'),
('64','1','login','1148563736','4199','','10.0.0.150'),
('65','1','military','1148564849','4394','We sent <b>2000 ships</b> to attack <b>guldan of blabal</b> (1:2); ETA = 31 ticks!','10.0.0.150'),
('66','1','military','1148565580','4409','We sent <b>4,120 ships</b> to defend <b>guldan of blabal</b> (1:2); ETA = 16 ticks!','10.0.0.150'),
('67','1','login','1148579533','4434','','10.0.0.150'),
('68','1','login','1148581095','4544','','10.0.0.150'),
('69','1','logout','1148581173','4544','','10.0.0.150'),
('70','1','login','1148581185','4544','','10.0.0.150'),
('71','1','military','1148581281','4544','Our fleet has been destroyed and the crew been killed!','10.0.0.150'),
('72','1','login','1148662645','6558','','10.0.0.150'),
('73','1','login','1148729012','6561','','10.0.0.150'),
('74','1','login','1148763475','8779','','10.0.0.150'),
('75','1','login','1148812921','8779','','10.0.0.150'),
('76','1','logout','1148813501','8779','','10.0.0.150'),
('77','0','forgot_pwd','1156969467','8779','Your password has been sent to \"<b>info@jouwmoeder.nl\"','127.0.0.4'),
('78','0','forgot_pwd','1156969581','8779','Your password has been sent to \"<b>info@jouwmoeder.nl\"','127.0.0.4'),
('79','1','login','1156969623','8779','','127.0.0.4'),
('80','1','login','1156970520','8779','','127.0.0.4'),
('81','1','login','1183498460','8779','','127.0.0.1'),
('82','1','logout','1183498741','8797','','127.0.0.1');

/** mail **/
INSERT INTO `mail` VALUES ('1','1','1','1197898779','9815','Sexy ho','1','1','0'),
('2','1','1','1197898803','9815','YO HO DE BITCH DOG','1','1','0'),
('3','5','1','1198199700','12132','kk','0','0','0'),
('4','5','1','1198199725','12143','yoo','0','0','0'),
('5','1','1','1198200134','12322','kk','1','1','0'),
('6',NULL,'4','1198333230','14471','activationcode lost and have no e-mail
----------------------------------------------
hey dude
im sorry man but i lost my activationcode and my email was disabled and i cant change it anymore :S:S
So can you either change it for me or activate my account?
My e-mail = DaJaap@kk.nl and my planet id = 4.
Cheers bye','1','0','1'),
('7',NULL,'4','1198333244','14471','activationcode lost and have no e-mail
----------------------------------------------
hey dude
im sorry man but i lost my activationcode and my email was disabled and i cant change it anymore :S:S
So can you either change it for me or activate my account?
My e-mail = DaJaap@kk.nl and my planet id = 4.
Cheers bye','1','0','1'),
('8','1','1','1198333863','14471','bek houwe','1','1','0'),
('9',NULL,'1','1198334251','14471','KUTHOND
----------------------------------------------
wat heb je voor ruig spel gemaakt homoooos? :D','1','0','1'),
('10','1','1','1198334267','14471','houd je bek slet :S','1','1','0'),
('11',NULL,'2','1198366311','19323','fack it
----------------------------------------------
yo yo yo','1','0','1'),
('12',NULL,'2','1198367317','19323','teefje
----------------------------------------------
lache spelletje gek :)','1','0','1'),
('13',NULL,'3','1198367497','19323','kk
----------------------------------------------
ole oele


fack','1','0','1'),
('14','4','4','1198464210','21673','ok :)','1','1','0'),
('15','2','1','1198637645','22248','fackit','1','0','0'),
('16','1','1','1198637650','22248','fackit','1','0','0'),
('17','1','1','1198638463','22248','asdaf','1','0','0'),
('18','1','1','1198638479','22248','vuile gek!','1','0','0');

/** news **/
INSERT INTO `news` VALUES ('1','1','1197559923','8797','0','Your technicians have finished researching <b>More Crystal</b>','1','1'),
('2','1','1197559929','8797','0','Your technicians have finished researching <b>First Airport</b>','1','1'),
('3','1','1197560221','8797','8','Your technicians have finished researching <b>About Energy</b>','1','1'),
('4','1','1197561184','8797','8','Your technicians have finished researching <b>Metal Refinery</b>','1','1'),
('5','1','1197648023','8797','8','Your technicians have finished researching <b>More Metal</b>','1','1'),
('6','1','1197744755','8797','8','Your technicians have finished researching <b>Robot Factory</b>','1','1'),
('7','1','1197744772','8797','8','Your technicians have finished researching <b>Tank Building</b>','1','1'),
('8','1','1197744783','8797','8','Your technicians have finished researching <b>EMP Studies</b>','1','1'),
('9','1','1197744818','8797','8','Your technicians have finished researching <b>Defence Machines</b>','1','1'),
('10','1','1197809695','8820','8','Your technicians have finished researching <b>Unit Patterns</b>','1','1'),
('11','1','1197809799','8858','8','Your technicians have finished researching <b>Destroyer Factory</b>','1','1'),
('12','1','1197811055','8871','8','Your technicians have finished researching <b>Crystal Refinery</b>','1','1'),
('13','1','1197811059','8873','8','Your technicians have finished researching <b>About Scanning</b>','1','1'),
('14','1','1197811108','8881','8','Your technicians have finished researching <b>Sector Scanning</b>','1','1'),
('15','1','1197811129','8886','8','Your technicians have finished researching <b>Metal Refinery</b>','1','1'),
('16','1','1197811358','8891','8','Your technicians have finished researching <b>About Energy</b>','1','1'),
('17','1','1197836840','8900','8','Your technicians have finished researching <b>More Crystal</b>','1','1'),
('18','1','1197836841','8902','8','Your technicians have finished researching <b>First Airport</b>','1','1'),
('19','1','1197847220','8947','8','Your technicians have finished researching <b>Making Energy</b>','1','1'),
('20','1','1197847220','8948','8','Your technicians have finished researching <b>More Metal</b>','1','1'),
('21','1','1197847345','8959','8','Your technicians have finished researching <b>Unit Patterns</b>','1','1'),
('22','1','1197847350','8963','8','Your technicians have finished researching <b>Robot Factory</b>','1','1'),
('23','1','1197847413','8980','8','Your technicians have finished researching <b>Tank Building</b>','1','1'),
('24','1','1197866564','8994','8','Your technicians have finished researching <b>Get More Crystal</b>','1','1'),
('25','1','1197866601','9009','8','Your technicians have finished researching <b>EMP Studies</b>','1','1'),
('26','1','1197866869','9119','8','Your technicians have finished researching <b>Unit Scanning</b>','1','1'),
('27','1','1197866869','9119','8','Your technicians have finished researching <b>\"Blind Diggers\" for Crystal</b>','1','1'),
('28','1','1197867181','9247','8','Your technicians have finished researching <b>Defence Patterns</b>','1','1'),
('29','1','1197867214','9260','8','Your technicians have finished researching <b>Defence Machines</b>','1','1'),
('30','1','1197867513','9381','8','Your technicians have finished researching <b>Destroyer Factory</b>','1','1'),
('31','1','1197867687','9448','8','Your technicians have finished researching <b>Defence Scanning</b>','1','1'),
('32','1','1197867949','9552','8','Your technicians have finished researching <b>Hardcore Crystal</b>','1','1'),
('33','1','1197916523','10670','8','Your technicians have finished researching <b>Get More Metal</b>','1','1'),
('34','1','1198018060','10686','8','Your technicians have finished researching <b>Fleet Signatures</b>','1','1'),
('35','1','1198018132','10715','8','Your technicians have finished researching <b>Stress Releasers</b>','1','1'),
('36','1','1198019727','11073','8','Your technicians have finished researching <b>Work floor management</b>','1','1'),
('37','1','1198020777','11533','8','Your technicians have finished researching <b>\"Blind Diggers\" for Metal</b>.','1','1'),
('38','1','1198148878','11613','8','Your technicians have finished researching <b>R&D RC</b>.','1','1'),
('39','1','1198194725','11746','8','Your technicians have finished researching <b>AntennaBot</b>.','1','1'),
('40','1','1198194807','11761','8','Your technicians have finished researching <b>NewAge Spies</b>.','1','1'),
('41','1','1198195530','11791','8','Your technicians have finished researching <b>Scorpion Factory</b>.','1','1'),
('42','1','1198195530','11791','8','Your technicians have finished researching <b>New Hangars</b>.','1','1'),
('43','1','1198198606','11872','8','Your technicians have finished researching <b>Wave Blockers</b>.','1','1'),
('44','1','1198198713','11881','8','Your technicians have finished researching <b>SuperCruisers</b>.','1','1'),
('45','1','1198199088','11895','8','Your technicians have finished researching <b>Hardcore Metal</b>.','1','1'),
('46','1','1198199203','11924','8','Your technicians have finished researching <b>Wave Blockers</b>.','1','1'),
('47','1','1198199241','11934','8','Your technicians have finished researching <b>Time Travel</b>.','1','1'),
('48','1','1198199336','11975','8','Your technicians have finished researching <b>Super Defence</b>.','1','1'),
('49','1','1198199367','11988','8','Your technicians have finished researching <b>Production Spies</b>.','1','1'),
('50','1','1198199392','11999','8','Your technicians have finished researching <b>Fleet Scanners</b>.','1','1'),
('51','1','1198199464','12030','8','Your technicians have finished researching <b>Time Travel (II)</b>.','1','1'),
('52','1','1198199523','12055','8','Your technicians have finished researching <b>Production Spy Suits</b>.','1','1'),
('53','1','1198199623','12098','8','Your technicians have finished researching <b>Hot \'n\' Thick</b>.','1','1'),
('54','1','1198353172','15767','8','Your technicians have finished researching <b>EMP Studies</b>.','1','1'),
('55','1','1198353214','15775','8','Your technicians have finished researching <b>Destroyer Factory</b>.','1','1'),
('56','1','1198353240','15780','8','Your technicians have finished researching <b>More Crystal</b>.','1','1'),
('57','1','1198353391','15809','8','Your technicians have finished researching <b>Defence Machines</b>.','1','1'),
('58','3','1198545850','21935','5','Lazy Rudie of Sleepy Lazy Planet (1) donated to you: 1800000 metal, 1800000 crystal, 1800000 energy','1','1'),
('59','3','1198545879','21935','5','Lazy Rudie of Sleepy Lazy Planet (1) donated to you: 39 metal, 39 crystal, 39 energy','1','1'),
('60','3','1198545935','21935','5','Lazy Rudie of Sleepy Lazy Planet (1) donated to you: 199 metal, 199 crystal, 199 energy','1','1'),
('61','1','1198546239','21935','5','master of Valdres (1) donated to you:<br />180,000 metal, 180,000 crystal, 180,000 energy','1','1'),
('62','1','1198546839','21935','5','You donated: 45,000 metal, 54,000 crystal, 63,000 energy to master of Valdres (3)','1','1'),
('63','3','1198546839','21935','5','Lazy Rudie of Sleepy Lazy Planet (1) donated to you:<br />45,000 metal, 54,000 crystal, 63,000 energy','1','1'),
('64','3','1198640926','22254','8','Your technicians have finished researching <b>Energy Efficiency</b>.','1','1'),
('65','2','1198640936','22255','8','Your technicians have finished researching <b>Get More Metal</b>.','1','1'),
('66','2','1198640989','22260','8','Your technicians have finished researching <b>Defence Patterns</b>.','1','1'),
('67','3','1198641412','22300','8','Your technicians have finished researching <b>Creons in Boxes</b>.','1','1'),
('68','1','1198641548','22312','5','You donated: <span style=\"color:#555555;\">11,185,387 metal</span>, <span style=\"color:#228800;\">28,855,434 energy</span> to master of Valdres (3)','1','1'),
('69','3','1198641548','22312','5','Lazy Rudie of Sleepy Lazy Planet (1) donated to you:<br /><span style=\"color:#555555;\">11,185,387 metal</span>, <span style=\"color:#228800;\">28,855,434 energy</span>','1','1'),
('70','2','1198641589','22315','8','Your technicians have finished researching <b>\"Blind Diggers\" for Crystal</b>.','1','1'),
('71','2','1198641599','22316','8','Your technicians have finished researching <b>\"Blind Diggers\" for Metal</b>.','1','1'),
('72','2','1198641610','22317','8','Your technicians have finished researching <b>Hardcore Crystal</b>.','1','1'),
('73','2','1198641622','22318','8','Your technicians have finished researching <b>Hardcore Metal</b>.','1','1'),
('74','2','1198641649','22321','5','You donated: <span style=\"color:#2244dd;\">331,834,795 crystal</span>, <span style=\"color:#228800;\">129,738,672 energy</span> to master of Valdres (3)','1','1'),
('75','3','1198641649','22321','5','guldan of blabal (1) donated to you:<br /><span style=\"color:#2244dd;\">331,834,795 crystal</span>, <span style=\"color:#228800;\">129,738,672 energy</span>','1','1'),
('76','2','1198641860','22340','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('77','2','1198641863','22341','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('78','2','1198641905','22345','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('79','2','1198641906','22345','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('80','2','1198641907','22345','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('81','2','1198641908','22345','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('82','2','1198641909','22345','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you and succeeded!!','1','1'),
('83','2','1198641941','22348','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('84','2','1198641942','22348','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('85','2','1198641943','22348','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('86','2','1198641958','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('87','2','1198641959','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('88','2','1198641961','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('89','2','1198641961','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('90','2','1198641962','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('91','2','1198641963','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('92','2','1198641964','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('93','2','1198641965','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('94','2','1198641967','22350','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('95','2','1198641968','22351','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('96','2','1198641969','22351','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('97','2','1198641970','22351','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('98','2','1198641971','22351','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('99','2','1198641971','22351','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('100','2','1198641972','22351','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('101','2','1198641973','22351','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you and succeeded!!','1','1'),
('102','1','1198641994','22353','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('103','1','1198641996','22353','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('104','1','1198641997','22353','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('105','1','1198641998','22353','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('106','2','1198642099','22363','5','You donated: <span style=\"color:#555555;\">8,941,111 metal</span> to master of Valdres (3)','1','0'),
('107','3','1198642099','22363','5','guldan of blabal (1) donated to you:<br /><span style=\"color:#555555;\">8,941,111 metal</span>','1','1'),
('108','1','1198642133','22366','5','You donated: <span style=\"color:#555555;\">9,291,979 metal</span> to master of Valdres (3)','1','1'),
('109','3','1198642133','22366','5','Lazy Rudie of Sleepy Lazy Planet (1) donated to you:<br /><span style=\"color:#555555;\">9,291,979 metal</span>','1','1'),
('110','2','1198642187','22371','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you and succeeded!!','1','0'),
('111','2','1198642193','22372','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('112','1','1198642233','22375','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('113','1','1198642235','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('114','1','1198642236','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('115','1','1198642237','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('116','1','1198642238','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('117','1','1198642239','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('118','1','1198642240','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('119','1','1198642241','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('120','1','1198642242','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('121','1','1198642243','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('122','1','1198642244','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('123','1','1198642244','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('124','1','1198642245','22376','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('125','1','1198642248','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('126','1','1198642249','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('127','1','1198642251','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('128','1','1198642252','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('129','1','1198642253','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('130','1','1198642254','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('131','1','1198642255','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('132','1','1198642256','22377','9','<b>master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','1'),
('133','4','1198642760','22424','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you and succeeded!!','1','0'),
('134','4','1198642770','22425','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('135','4','1198642913','22437','8','Your technicians have finished researching <b>Stress Releasers</b>.','1','0'),
('136','4','1198642924','22438','8','Your technicians have finished researching <b>R&D RC</b>.','1','0'),
('137','4','1198642934','22439','8','Your technicians have finished researching <b>Work floor management</b>.','1','0'),
('138','1','1198643103','22455','5','You donated: <span style=\"color:#555555;\">15,486,633 metal</span> to Master of Valdres (3)','1','1'),
('139','3','1198643103','22455','5','Lazy Rudie of Sleepy Planet (1) donated to you:<br /><span style=\"color:#555555;\">15,486,633 metal</span>','1','1'),
('140','2','1198643128','22457','5','You donated: <span style=\"color:#555555;\">22,401,000 metal</span> to Master of Valdres (3)','1','0'),
('141','3','1198643128','22457','5','Guldan of Blabal (1) donated to you:<br /><span style=\"color:#555555;\">22,401,000 metal</span>','1','1'),
('142','1','1198645540','22621','5','You donated: <span style=\"color:#555555;\">4,500,000 metal</span> to Master of Valdres (3)','1','0'),
('143','3','1198645540','22621','5','Lazy Rudie of Sleepy Planet (1) donated to you:<br /><span style=\"color:#555555;\">4,500,000 metal</span>','1','0'),
('144','1','1198645733','22637','5','You donated: <span style=\"color:#555555;\">4,320,000 metal</span> to Master of Valdres (3)','1','0'),
('145','3','1198645733','22637','5','Lazy Rudie of Sleepy Planet (1) donated to you:<br /><span style=\"color:#555555;\">4,320,000 metal</span>','1','0'),
('146','1','1198645934','22637','5','You donated: <span style=\"color:#228800;\">13,500,000 energy</span> to Master of Valdres (3)','1','0'),
('147','3','1198645934','22637','5','Lazy Rudie of Sleepy Planet (1) donated to you:<br /><span style=\"color:#228800;\">13,500,000 energy</span>','1','0'),
('148','1','1198645944','22637','5','You donated: <span style=\"color:#228800;\">499,500 energy</span> to Master of Valdres (3)','1','0'),
('149','3','1198645944','22637','5','Lazy Rudie of Sleepy Planet (1) donated to you:<br /><span style=\"color:#228800;\">499,500 energy</span>','1','0'),
('150','1','1198645962','22637','5','You donated: <span style=\"color:#555555;\">189 metal</span>, <span style=\"color:#2244dd;\">288 crystal</span> to Master of Valdres (3)','1','0'),
('151','3','1198645962','22637','5','Lazy Rudie of Sleepy Planet (1) donated to you:<br /><span style=\"color:#555555;\">189 metal</span>, <span style=\"color:#2244dd;\">288 crystal</span>','1','0'),
('152','2','1198646306','22638','5','You donated: <span style=\"color:#555555;\">42,300,000 metal</span> to Master of Valdres (3)','1','0'),
('153','3','1198646306','22638','5','Guldan of Blabal (1) donated to you:<br /><span style=\"color:#555555;\">42,300,000 metal</span>','1','0'),
('154','1','1198646417','22643','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('155','1','1198646418','22643','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('156','1','1198646419','22643','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('157','1','1198646420','22643','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('158','1','1198646421','22643','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('159','1','1198646422','22643','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('160','1','1198646423','22643','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Sector Scan</b> you, but he failed!','1','0'),
('161','1','1198646439','22645','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Unit Scan</b> you, but he failed!','1','0'),
('162','1','1198646440','22645','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Unit Scan</b> you, but he failed!','1','0'),
('163','1','1198646441','22645','9','<b>Master of Valdres</b> (1:1:3) tried to <b>Unit Scan</b> you, but he failed!','1','0'),
('164','3','1198943087','23466','6','<b>Lazy Rudie of Sleepy Planet</b> (1:1:2) sent his/her to attack us. ETA: 12 ticks!','0','0'),
('165','1','1198943087','23466','7','We sent our fleet to attack <b>Master of Valdres</b> (::3); ETA: 12 ticks!','1','0'),
('166','2','1207729394','23477','6','<b>Lazy Rudie of Sleepy Planet</b> (1:1:2) sent his/her to attack us. ETA: 16 ticks!','0','0'),
('167','1','1207729394','23477','7','We sent our fleet to attack <b>Guldan of Blabal</b> (1:1:1); ETA: 16 ticks!','1','0'),
('168','2','1207729394','23477','6','<b>Lazy Rudie of Sleepy Planet</b> (1:1:2) sent his/her to attack us. ETA: 12 ticks!','0','0'),
('169','1','1207729394','23477','7','We sent our fleet to attack <b>Guldan of Blabal</b> (1:1:1); ETA: 12 ticks!','1','0'),
('170','4','1207729608','23478','6','<b>Lazy Rudie of Sleepy Planet</b> (1:1:2) sent his/her to attack us. ETA: 14 ticks!','0','0'),
('171','1','1207729608','23478','7','We sent our fleet to attack <b>JOE THE FARMER of THE BEAN FARM</b> (1:2:1); ETA: 14 ticks!','1','0');

/** online **/
INSERT INTO `online` VALUES ('1','1','127.0.0.1','1139859671','9268da5ca52c59450153bc89ced18c26'),
('2','1','127.0.0.1','1139872462','6cdad4542dc967ee482fdde3cdb67473'),
('3','1','127.0.0.1','1139872515','fafdf4284cd035f826e79d3b4964fced'),
('4','1','127.0.0.1','1139875562','6cd59ac744a79d40239b4e89bfa120a2'),
('5','1','127.0.0.1','1140799973','48aa5e3eeb40f5b58eb80d64ba678950'),
('6','1','127.0.0.1','1140800049','ce8bb1892ba73c754ba73acc6a2f9b53'),
('10','1','127.0.0.1','1140800842','5d1460f21faf091babacbb970e3678b1'),
('11','1','127.0.0.1','1140882729','cc8acc73857a219b1c0da4ad5b30845d'),
('15','1','127.0.0.1','1141135255','9f410c6e3a2c0656351f1ec808604d4c'),
('18','1','127.0.0.1','1140996108','f32b17b671bde87310caa4fb02394027'),
('20','1','127.0.0.1','1141069094','b8fb14b9d411925f971d7aef9f4b3de0'),
('21','1','127.0.0.1','1141135772','929ff0cd0c6fbc5b0a90a3d94d830c47'),
('22','1','127.0.0.1','1141137484','00110648f4160955e40bf9908fc04ff7'),
('23','1','127.0.0.1','1144186351','55f1a645b68e61ca63ab5bdf3baead28'),
('24','1','127.0.0.1','1148554638','e38aef42cf18dd3c64cc2448b2c9c126'),
('25','1','10.0.0.150','1148565674','f2eac075dc320bd85bf19ebd4b4832dd'),
('26','1','10.0.0.150','1148580175','45001ef47ae001b84392a5dcc269ca2c'),
('28','1','10.0.0.150','1148590629','345e13527c73ef0f7e72918a54805e64'),
('29','1','10.0.0.150','1148663597','ff6a466f128e5c0d17d0b53bef8b6fed'),
('30','1','10.0.0.150','1148733964','3aa955d862bd4f17d1e5290c081ac833'),
('31','1','10.0.0.150','1148770346','aafa4af79fed722af630a6e0795cd666'),
('33','1','127.0.0.4','1156969726','bd0949b2ef6be4c1848a732e01c2a2ae'),
('34','1','127.0.0.4','1156971565','9dab4c6661ad8ba56f480bef6079edb9');

/** planets **/
INSERT INTO `planets` VALUES ('1','info@jouwmoeder.nl','1a1347681a2adead044b1e0e2a3ff99a','','0','1','Lazy Rudie','Sleepy Planet','1','2','1395967','79792957','24284','1354','813','0','0','1207749489','1207750968',NULL,'10305133','0','1199859127','0','2','0','Kenkert','1','0','oaODGNWDvaTNytye'),
('2','barthoogenboezem@hotmail.com','f4e6c7068cd7fe2be8f055ae0756df85','','1','1','Guldan','Blabal','1','1','45095004','220764000','47986600','2012','227','0','10471','1199818597','1199818460',NULL,'2152251','0','0','0','1','0','','0','0',''),
('3','master_greyfox@hotmail.com','dcde9ae7d3d721989218ecc903966e1d','','1','1','Master','Valdres','1','3','132671562','197028136','71847614','1752','1151','0','9475','1198648180','1198648020',NULL,'8620125','0','0','0','1','0','','0','0','LUodmPu9GbLkf8Wr'),
('4','jaap@kk.nl','59c5044904713175cf2fcd4f265808fe','','1','1','JOE THE FARMER','THE BEAN FARM','2','1','20797154','63554260','26108640','370','78','0','0','1198648169','1198647995',NULL,'356956','0','0','0','4','0','','0','0','yH4LsHfdEcNxQOnP'),
('5','rudie@jouwmoeder.nl','815c8d971bdbd81088052746186a137f','','1','1','Rudie','Veldhoven','2','2','105004','106000','0','0','0','0','0','1197933643','1197933766',NULL,'422','0','0','0','5','0','','1','0','151b87b63b5c4939c627261ba4abf791'),
('6','cervetti@jouwmoeder.nl','a58f74665c5ad5ba0ad685776a6dbec5','','1','1','Cervetti','TomTomTommy','2','3','105004','106000','0','0','0','0','0','1198270909','1198274655',NULL,'422','0','0','0','4','0','','1','0','a24fbe2961733005467e8314e9ff5863');

/** politics **/
INSERT INTO `politics` VALUES ('1',NULL,'1','1139870740','yo','gekke','1','0'),
('2',NULL,'1','1139870869','fck','bitch','1','0'),
('3',NULL,'1','1139870878','fucking twee yo:D','sletjess','1','0'),
('4','3','1','1139871111','','teringhomo','1','0'),
('5','3','1','1139871120','','fuckerbitsj','1','0'),
('6',NULL,'1','1140988214','','test :)','1','0'),
('7',NULL,'1','1140988343','','wdf','1','0'),
('8',NULL,'1','1140988359','','asdf','1','0'),
('9',NULL,'1','1140988449','titel :)','sdf GVD :D
','1','0'),
('10','9','1','1140988573','','jah jah jah jah jah jij wint :D','1','0'),
('11','9','1','1140988582','','sad','1','0'),
('12','9','1','1140988589','','asdasdasdasdsasdsaSD','1','0'),
('13','9','1','1140988958','','','1','0'),
('14','3','1','1140990173','','sletje :D','3','0'),
('15','3','1','1144186324','','teef
','2','0'),
('16','3','1','1198430336',NULL,'houd je bek ouwe gek!
kusje
</table>','1','0'),
('17',NULL,'1','1198430666','stemminggg','we moeten stemmen ouwe hondjes!!','1','0');

/** power_on_planets **/
INSERT INTO `power_on_planets` VALUES ('12','1','400'),
('13','1','900');

/** prefs **/
INSERT INTO `prefs` VALUES ('1','PORNSTARS v2','10','1','','23479','1207729720','0','1','6','1','12','10','1','1','Ticker: <a href=\"tickah.php\" target=t1>hierzo</a>.
Als je wilt ticken, graag. Je kan in het bovenste menu de ticker iets discreter aanzetten. Gebruikt weinig of geen extra geheugen. Als je alleen speelt, zet m aan. Anders: zet m ook maar aan.','0','0','0','0','1','0','10','3','1','1','3','Home,Alpha,Beta,Gamma,Delta,Epsilon,Zeta,Eta,Theta,Iota,Kappa,Lambda,Mu,Nu','0','1','0','0','1,4');

/** production_per_planet **/
INSERT INTO `production_per_planet` VALUES ('3','1','14','4','2'),
('4','1','24','4','2'),
('5','1','25','4','2'),
('6','1','15','14','2'),
('7','1','16','24','2'),
('8','1','17','24','2'),
('9','1','18','34','2'),
('10','1','20','44','2'),
('11','1','23','49','2'),
('12','1','21','19','2'),
('13','1','22','54','2'),
('14','1','26','56','2'),
('15','1','14','5','1'),
('16','1','24','5','1'),
('17','1','25','5','1'),
('18','1','14','7','1'),
('19','1','24','7','1'),
('20','1','25','7','1'),
('21','1','1','7','1'),
('22','1','2','17','1'),
('23','1','3','27','1'),
('24','1','4','37','1'),
('25','1','5','52','1'),
('26','1','6','57','1'),
('27','1','7','82','1'),
('28','1','9','17','1'),
('29','1','10','27','1'),
('30','1','11','57','1'),
('31','1','1','8','1'),
('32','1','2','18','1'),
('33','1','3','28','1'),
('34','1','4','38','1'),
('35','1','5','53','1'),
('36','1','6','58','1'),
('37','1','7','83','1'),
('38','1','9','18','1'),
('39','1','10','28','1'),
('40','1','11','58','1');

/** r_d_per_planet **/
INSERT INTO `r_d_per_planet` VALUES ('1','1','0'),
('1','2','0'),
('1','3','0'),
('1','4','0'),
('2','1','0'),
('2','2','0'),
('2','3','0'),
('2','4','0'),
('3','1','0'),
('3','2','0'),
('3','3','0'),
('3','4','0'),
('4','1','0'),
('4','2','0'),
('4','3','0'),
('4','4','0'),
('5','1','0'),
('5','2','0'),
('5','3','0'),
('5','4','0'),
('6','1','0'),
('6','2','0'),
('6','3','0'),
('6','4','0'),
('7','1','0'),
('7','2','0'),
('7','3','0'),
('7','4','0'),
('8','1','0'),
('8','2','0'),
('8','3','0'),
('8','4','0'),
('9','1','0'),
('9','2','0'),
('9','3','0'),
('10','1','0'),
('10','2','0'),
('10','3','0'),
('10','4','0'),
('11','1','0'),
('11','2','0'),
('11','3','0'),
('11','4','0'),
('12','1','0'),
('12','2','0'),
('12','3','0'),
('13','1','0'),
('13','2','0'),
('13','3','0'),
('14','1','0'),
('14','3','0'),
('15','1','0'),
('16','3','0'),
('17','1','0'),
('17','2','0'),
('17','3','0'),
('18','1','0'),
('18','2','0'),
('18','3','0'),
('19','1','0'),
('19','2','0'),
('19','3','0'),
('21','1','0'),
('21','3','0'),
('23','1','0'),
('23','3','0'),
('24','1','0'),
('24','3','0'),
('25','1','0'),
('26','1','0'),
('26','2','0'),
('26','3','0'),
('26','4','0'),
('27','1','0'),
('27','2','0'),
('27','3','0'),
('27','4','0'),
('28','1','0'),
('28','2','0'),
('28','3','0'),
('28','4','0'),
('29','1','0'),
('29','2','0'),
('29','3','0'),
('29','4','0'),
('30','1','0'),
('30','2','0'),
('30','3','0'),
('31','1','0'),
('31','2','0'),
('31','3','0'),
('31','4','0'),
('32','1','0'),
('32','2','0'),
('32','3','0'),
('32','4','0'),
('33','1','0'),
('33','2','0'),
('33','3','0'),
('33','4','0'),
('34','1','0'),
('34','2','0'),
('34','3','0'),
('35','1','0'),
('35','2','0'),
('35','3','0'),
('35','4','0'),
('36','1','0'),
('37','3','0'),
('38','1','0'),
('38','2','0'),
('38','3','0'),
('41','1','0'),
('41','3','0'),
('44','1','0'),
('44','3','0'),
('46','1','0'),
('46','3','0'),
('47','1','0'),
('47','3','0'),
('48','1','0'),
('48','2','0'),
('48','3','0'),
('48','4','0'),
('49','1','0'),
('49','2','0'),
('49','3','0'),
('49','4','0'),
('50','1','0'),
('50','3','0'),
('50','4','0');

/** ships_in_fleets **/
INSERT INTO `ships_in_fleets` VALUES ('1','1','300'),
('1','2','150'),
('1','3','0'),
('1','4','0'),
('1','5','0'),
('1','6','0'),
('1','7','0'),
('2','1','0'),
('2','2','0'),
('2','3','400'),
('2','4','0'),
('2','5','0'),
('2','6','0'),
('2','7','160'),
('2','8','0'),
('3','1','200'),
('3','2','200'),
('3','3','0'),
('3','4','0'),
('3','5','0'),
('3','6','0'),
('3','7','0'),
('22','1','0'),
('22','2','0'),
('22','3','0'),
('22','4','0'),
('22','5','0'),
('22','6','0'),
('22','7','0');
/** alliances **/
ALTER TABLE `alliances`
ADD CONSTRAINT `alliances_fk` FOREIGN KEY (`leader_planet_id`) REFERENCES `planets` (`id`);

/** d_all_units **/
ALTER TABLE `d_all_units`
ADD CONSTRAINT `d_units_fk` FOREIGN KEY (`r_d_required_id`) REFERENCES `d_r_d_available` (`id`);

/** d_defence **/
ALTER TABLE `d_defence`
ADD CONSTRAINT `d_defence_fk` FOREIGN KEY (`id`) REFERENCES `d_all_units` (`id`);

/** d_power **/
ALTER TABLE `d_power`
ADD CONSTRAINT `d_power_fk` FOREIGN KEY (`id`) REFERENCES `d_all_units` (`id`);

/** d_r_d_available **/
ALTER TABLE `d_r_d_available`
ADD CONSTRAINT `d_r_d_available_fk` FOREIGN KEY (`race_id`) REFERENCES `d_races` (`id`);

/** d_r_d_excludes **/
ALTER TABLE `d_r_d_excludes`
ADD CONSTRAINT `r_d_excludes_fk` FOREIGN KEY (`r_d_id`) REFERENCES `d_r_d_available` (`id`),
ADD CONSTRAINT `r_d_excludes_fk1` FOREIGN KEY (`r_d_excludes_id`) REFERENCES `d_r_d_available` (`id`);

/** d_r_d_requires **/
ALTER TABLE `d_r_d_requires`
ADD CONSTRAINT `d_r_d_requires_fk` FOREIGN KEY (`r_d_requires_id`) REFERENCES `d_r_d_available` (`id`),
ADD CONSTRAINT `r_d_requires_fk` FOREIGN KEY (`r_d_id`) REFERENCES `d_r_d_available` (`id`);

/** d_r_d_results **/
ALTER TABLE `d_r_d_results`
ADD CONSTRAINT `r_d_results_fk` FOREIGN KEY (`done_r_d_id`) REFERENCES `d_r_d_available` (`id`);

/** d_ships **/
ALTER TABLE `d_ships`
ADD CONSTRAINT `d_ships_fk` FOREIGN KEY (`id`) REFERENCES `d_all_units` (`id`);

/** d_waves **/
ALTER TABLE `d_waves`
ADD CONSTRAINT `d_waves_fk` FOREIGN KEY (`id`) REFERENCES `d_all_units` (`id`);

/** defence_on_planets **/
ALTER TABLE `defence_on_planets`
ADD CONSTRAINT `defence_on_planets_fk` FOREIGN KEY (`defence_id`) REFERENCES `d_defence` (`id`),
ADD CONSTRAINT `defence_on_planet_fk1` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`);

/** fleets **/
ALTER TABLE `fleets`
ADD CONSTRAINT `fleets_fk` FOREIGN KEY (`owner_planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `fleets_fk1` FOREIGN KEY (`destination_planet_id`) REFERENCES `planets` (`id`);

/** galaxies **/
ALTER TABLE `galaxies`
ADD CONSTRAINT `galaxies_fk` FOREIGN KEY (`gc_planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `galaxies_fk1` FOREIGN KEY (`moc_planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `galaxies_fk2` FOREIGN KEY (`mow_planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `galaxies_fk3` FOREIGN KEY (`mof_planet_id`) REFERENCES `planets` (`id`);

/** mail **/
ALTER TABLE `mail`
ADD CONSTRAINT `mail_fk` FOREIGN KEY (`to_planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `mail_fk1` FOREIGN KEY (`from_planet_id`) REFERENCES `planets` (`id`);

/** news **/
ALTER TABLE `news`
ADD CONSTRAINT `news_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `news_fk1` FOREIGN KEY (`news_subject_id`) REFERENCES `d_news_subjects` (`id`);

/** planets **/
ALTER TABLE `planets`
ADD CONSTRAINT `planets_fk` FOREIGN KEY (`race_id`) REFERENCES `d_races` (`id`),
ADD CONSTRAINT `planets_ibfk_1` FOREIGN KEY (`galaxy_id`) REFERENCES `galaxies` (`id`),
ADD CONSTRAINT `planets_ibfk_2` FOREIGN KEY (`alliance_id`) REFERENCES `alliances` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

/** politics **/
ALTER TABLE `politics`
ADD CONSTRAINT `politics_fk` FOREIGN KEY (`parent_thread_id`) REFERENCES `politics` (`id`),
ADD CONSTRAINT `politics_fk1` FOREIGN KEY (`creator_planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `politics_fk2` FOREIGN KEY (`galaxy_id`) REFERENCES `galaxies` (`id`);

/** power_on_planets **/
ALTER TABLE `power_on_planets`
ADD CONSTRAINT `power_on_planets_fk` FOREIGN KEY (`power_id`) REFERENCES `d_power` (`id`),
ADD CONSTRAINT `power_on_planet_fk1` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`);

/** production_per_planet **/
ALTER TABLE `production_per_planet`
ADD CONSTRAINT `production_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `production_fk1` FOREIGN KEY (`unit_id`) REFERENCES `d_all_units` (`id`);

/** r_d_per_planet **/
ALTER TABLE `r_d_per_planet`
ADD CONSTRAINT `r_d_per_planet_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`),
ADD CONSTRAINT `r_d_per_user_fk` FOREIGN KEY (`r_d_id`) REFERENCES `d_r_d_available` (`id`);

/** ships_in_fleets **/
ALTER TABLE `ships_in_fleets`
ADD CONSTRAINT `ships_in_fleets_fk` FOREIGN KEY (`fleet_id`) REFERENCES `fleets` (`id`),
ADD CONSTRAINT `ships_in_fleets_fk1` FOREIGN KEY (`ship_id`) REFERENCES `d_ships` (`id`);

/** waves_on_planets **/
ALTER TABLE `waves_on_planets`
ADD CONSTRAINT `waves_on_planets_fk` FOREIGN KEY (`wave_id`) REFERENCES `d_waves` (`id`),
ADD CONSTRAINT `waves_on_planet_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`);