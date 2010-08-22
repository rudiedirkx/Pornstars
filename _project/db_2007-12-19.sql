-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 19 Dec 2007 om 22:44
-- Server versie: 5.0.27
-- PHP Versie: 5.2.1
-- 
-- Database: `pornstars`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `alliances`
-- 

CREATE TABLE `alliances` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `tag` varchar(32) NOT NULL default '',
  `pwd` varchar(255) NOT NULL default '',
  `name` varchar(160) NOT NULL default '',
  `leader_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `leader_id` (`leader_id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `alliances`
-- 

INSERT INTO `alliances` VALUES (1, 'RDX', '1234567890', 'Dirkxes', 1);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_all_units`
-- 

CREATE TABLE `d_all_units` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `T` enum('ship','defence','wave','power') NOT NULL default 'ship',
  `name` varchar(30) NOT NULL,
  `explanation` varchar(150) default NULL,
  `build_eta` int(11) unsigned NOT NULL,
  `metal` int(11) unsigned NOT NULL,
  `crystal` int(11) unsigned NOT NULL,
  `energy` int(11) unsigned NOT NULL,
  `move_eta` int(11) unsigned NOT NULL,
  `fuel` int(11) unsigned NOT NULL,
  `r_d_required_id` int(11) unsigned default NULL,
  `is_stealth` enum('0','1') NOT NULL default '0',
  `is_mobile` enum('0','1') NOT NULL default '0',
  `is_fleet` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `r_d_required_id` (`r_d_required_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_all_units`
-- 

INSERT INTO `d_all_units` VALUES (1, 'ship', 'Infinitys', 'Small weak ships who make up their little powers with their agility and speed', 10, 0, 300, 0, 4, 40, 4, '0', '0', '0'),
(2, 'ship', 'Wraiths', 'Slightly bigger ships with slower though stronger weapons. Also high agility and speed', 20, 1000, 0, 0, 4, 48, 8, '0', '0', '0'),
(3, 'ship', 'Warfrigates', 'Warfrigates are real warships! Big ships loaded with lowspeed cannons and highspeed machineguns', 30, 2500, 2500, 0, 5, 56, 33, '0', '0', '0'),
(4, 'ship', 'Astropods', 'The Astropod is the only ship that can steal roids from others. Astropods are slow and weak and cant shoot. They die on success', 40, 1250, 1250, 0, 6, 70, 33, '0', '0', '0'),
(5, 'ship', 'Cobras', 'Very strong and armoured ships! Blocks but not kills Astropods. A musthave in the modern army', 55, 0, 3000, 0, 6, 72, 35, '1', '0', '0'),
(6, 'ship', 'Destroyers', 'The biggest of all heavy duty warships! Quite an investment, but very efficient in combat', 60, 3000, 2000, 0, 8, 97, 12, '0', '0', '0'),
(7, 'ship', 'Scorpions', 'Great ships, <u>very stealthy</u> and quite manouvrable and fast. A Cobra''s worst enemy', 85, 2000, 5000, 0, 7, 80, 13, '0', '0', '0'),
(8, 'ship', 'Antennas', 'These babys are huge! And I mean HUGE! They''re barely worth the resources though. But they''re fast considering their size', 90, 18000, 3000, 0, 9, 700, 20, '1', '0', '0'),
(9, 'defence', 'Reaper Cannons', 'Best PDU against Astropods. This unit is a musthave in your Planetary Defense', 20, 1000, 0, 0, 0, 0, 17, '0', '0', '0'),
(10, 'defence', 'Avengers', 'Avengers are of a new generation of PDU. They select their targets and are therefore very efficient', 30, 800, 800, 0, 0, 0, 18, '0', '0', '0'),
(11, 'defence', 'Lucius Stalkers', 'Very heavy Defense Unit, which can only be killed by Wraiths. Lucius Stalkers go for the big fishes of the attacker', 60, 3000, 3000, 0, 0, 0, 17, '1', '0', '0'),
(12, 'power', 'Creon Cells', 'One Cell contains 2,871,396 Creons. They make 40 Energy/tick', 6, 6000, 0, 2000, 0, 0, 5, '0', '0', '0'),
(13, 'power', 'Creon Boxes', 'A Box contains so many Creons to count. They''re highly radioactive, so be careful', 6, 8000, 0, 1000, 0, 0, 21, '0', '0', '0'),
(14, 'wave', 'Asteroid Scans', 'You need these scans to find roids. Build as many as you can', 10, 0, 1000, 1000, 0, 0, 26, '0', '0', '0'),
(15, 'wave', 'Sector Scans', 'The basic sectorscan provides no particular info; no military intel', 20, 0, 1000, 2000, 0, 2, 3, '0', '0', '0'),
(16, 'wave', 'Unit Scans', 'The unitscan shows you all current non-stealth ships of your target', 30, 0, 2000, 4000, 0, 4, 9, '0', '0', '0'),
(17, 'wave', 'Defence Scans', 'The PDUscan shows all PDU of the targets planet', 30, 0, 4000, 8000, 0, 5, 14, '0', '0', '0'),
(18, 'wave', 'Fleet Scans', 'The fleetscan shows you ALL of your targets units in his fleet', 40, 0, 12000, 24000, 0, 7, 15, '0', '0', '0'),
(19, 'wave', 'News Scans', 'The newsscan shows you all of your targets news of the last 24hours', 40, 0, 12000, 24000, 0, 8, 16, '0', '0', '0'),
(20, 'wave', 'Production Scans', 'The productionscan shows the ships in production of the target', 50, 0, 10000, 18000, 0, 7, 25, '0', '0', '0'),
(21, 'wave', 'Wave Amplifiers', 'The more you have, the greater the chance is you penetrate your target''s shields', 25, 0, 3000, 6000, 0, 0, 3, '0', '0', '0'),
(22, 'wave', 'Wave Blockers', 'The more you have, the smaller the chance is your enemy penetrates your shields', 60, 0, 4000, 10000, 0, 0, 19, '0', '0', '0'),
(23, 'wave', 'Political Scans', 'Scans the politics of your target planet. Great way to penetrate internal communications', 55, 0, 8000, 8000, 0, 8, 47, '0', '0', '0');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_defence`
-- 

CREATE TABLE `d_defence` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `all_units_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `all_units_id` (`all_units_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_defence`
-- 

INSERT INTO `d_defence` VALUES (1, 9),
(2, 10),
(3, 11);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_fleet_actions`
-- 

CREATE TABLE `d_fleet_actions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_fleet_actions`
-- 

INSERT INTO `d_fleet_actions` VALUES (1, 'Attack', 'attack'),
(2, 'Defend', 'defend'),
(3, 'Return', 'return');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_news_subjects`
-- 

CREATE TABLE `d_news_subjects` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `image` varchar(30) NOT NULL,
  `const_name` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_news_subjects`
-- 

INSERT INTO `d_news_subjects` VALUES (0, 'Unknown', 'news_unknown.gif', 'UNKNOWN'),
(1, 'Alliance', 'news_alliance.gif', 'ALLIANCE'),
(2, 'Combat Report', 'news_combatreport.gif', 'COMBAT'),
(3, 'Friendly Incoming', 'news_friendly_incoming.gif', 'FRIENDLY_INCOMING'),
(4, 'Friendly Going Out', 'news_friendly_outgoing.gif', 'FRIENDLY_OUTGOING'),
(5, 'Galaxy', 'news_galaxy.gif', 'GALAXY'),
(6, 'Hostile Incoming', 'news_hostile_incoming.gif', 'HOSTILE_INCOMING'),
(7, 'Hostile Going Out', 'news_hostile_outgoing.gif', 'HOSTILE_OUTGOING'),
(8, 'R & D', 'news_r_d.gif', 'R_D'),
(9, 'Waves', 'news_waves.gif', 'WAVES');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_power`
-- 

CREATE TABLE `d_power` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `all_units_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `all_units_id` (`all_units_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_power`
-- 

INSERT INTO `d_power` VALUES (1, 12),
(2, 13);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_r_d_available`
-- 

CREATE TABLE `d_r_d_available` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `T` enum('r','d') NOT NULL default 'r',
  `explanation` varchar(150) NOT NULL,
  `eta` tinyint(4) unsigned NOT NULL default '0',
  `metal` int(11) unsigned NOT NULL default '0',
  `crystal` int(11) unsigned NOT NULL default '0',
  `energy` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_r_d_available`
-- 

INSERT INTO `d_r_d_available` VALUES (1, 'Crystal Refinery', 'd', 'Gain +1500 extra Crystal', 4, 0, 0, 0),
(2, 'Metal Refinery', 'd', 'Gain +1500 extra Metal', 4, 500, 500, 0),
(3, 'Sector Scanning', 'd', 'Enables Sectorscans', 8, 1000, 1000, 0),
(4, 'First Airport', 'd', 'Enables the production of Inifinitys', 10, 1000, 1000, 0),
(5, 'Making Energy', 'd', 'Enables the production of a Creon living environment', 9, 3000, 3000, 0),
(6, 'Get More Crystal', 'd', 'Gain +3000 extra Crystal', 11, 6000, 9000, 0),
(7, 'Get More Metal', 'd', 'Gain +3000 extra Metal', 13, 9000, 6000, 0),
(8, 'Robot Factory', 'd', 'Enables the production of Wraiths', 14, 8000, 4000, 0),
(9, 'Unit Scanning', 'd', 'Enables Unitscans', 14, 12000, 20000, 0),
(10, 'Hardcore Crystal', 'd', 'Builds machines who provide you an extra +6000 Crystal', 20, 25000, 45000, 0),
(11, 'Hardcore Metal', 'd', 'Builds machines who provide you an extra +6000 Metal', 24, 50000, 30000, 0),
(12, 'Destroyer Factory', 'd', 'Enables the production of Destroyers', 36, 80000, 80000, 0),
(13, 'Scorpion Factory', 'd', 'Enables the production of Scorpions', 44, 120000, 200000, 0),
(14, 'Defence Scanning', 'd', 'Enables Defence Scans', 30, 200000, 300000, 0),
(15, 'Fleet Scanners', 'd', 'Enables Fleet Scans', 40, 400000, 500000, 0),
(16, 'High-speed-plasma Spies', 'd', 'Enables News Scans by penetrating the high-speed-plasma of your target', 44, 400000, 800000, 0),
(17, 'Defence Machines', 'd', 'Enables the production of basic Planetary Defence Units (Reaper Cannons and Lucius Stalkers)', 36, 600000, 550000, 0),
(18, 'Super Defence', 'd', 'Enables the production of Avengers', 42, 700000, 700000, 0),
(19, 'Wave Blockers', 'd', 'Extra holding and forcefull shields to block infiltrationscans', 50, 950000, 1200000, 0),
(20, 'SuperCruisers', 'd', 'Only hangars and upgrading factories are yet necessary', 56, 2200000, 1100000, 0),
(21, 'Creons in Boxes', 'd', 'Boxes being built which can contain a large amount of Creons', 52, 2800000, 4000000, 800000),
(22, 'Hot ''n'' Thick', 'd', 'Improving pantser and heatresistment on all moving units. After this, you save another 5 ticks on all fleet ETA', 74, 2000000, 2800000, 0),
(23, 'Work, Bitch, Work!', 'd', 'Construction of weapons to convince your professors to work more and harder', 42, 4000000, 2400000, 0),
(24, 'Building Space Warehouses', 'd', 'Increasing storage facilities. Asteroid income should rise considerably', 38, 4000000, 4000000, 0),
(25, 'Production Spy Suits', 'd', 'Creating the suits Production Spies get to wear, to infiltrate enemy production facilities', 92, 800000, 800000, 800000),
(26, 'About Scanning', 'r', 'Enables the production of huge-scale radars (+asteroidscans)', 6, 0, 1000, 0),
(27, 'About Energy', 'r', 'Researches the art of Creon growth', 4, 0, 850, 0),
(28, 'More Crystal', 'r', 'Researches more efficient mining methods', 8, 2000, 2000, 0),
(29, 'More Metal', 'r', 'Researches more efficient mining methods', 10, 3000, 3000, 0),
(30, 'Unit Patterns', 'r', 'Researching the early unitscans', 10, 6000, 6000, 0),
(31, '"Blind Diggers" for Crystal', 'r', 'Researching machines who dig to the core', 16, 20000, 30000, 0),
(32, '"Blind Diggers" for Metal', 'r', 'Researching machines who dig to the core', 20, 30000, 20000, 0),
(33, 'Tank Building', 'r', 'Enables the production of Warfrigates and Astropods', 16, 48000, 40000, 0),
(34, 'Defence Patterns', 'r', 'Researching PDU-methods', 22, 70000, 90000, 20000),
(35, 'EMP Studies', 'r', 'Enables the production of Cobras and the research for WaveBlockers', 26, 100000, 140000, 0),
(36, 'Fleet Signatures', 'r', 'Researches fleetsignatures to penetrate fleet communication shields', 30, 200000, 200000, 0),
(37, 'NewAge Spies', 'r', 'Trains human-robots to fly trough time and space to get enemies'' news', 30, 200000, 200000, 0),
(38, 'Wave Blockers', 'r', 'Researches extra powerfull shields', 44, 400000, 400000, 0),
(39, 'AntennaBot', 'r', 'Searching for new mining technologies for shipmaterials for a supercruiser', 42, 900000, 650000, 0),
(40, 'New Hangars', 'r', 'Researching for testfacilities big enough for these ships', 52, 1300000, 700000, 0),
(41, 'Energy Efficiency', 'r', 'Researching techniques to get more Creons in a cell, making it more efficient: new cells available when finished', 46, 2000000, 1800000, 400000),
(42, 'Time Travel', 'r', 'The Theory of traveling through time with all possible units. Cuts 8 ticks off any fleet ETA!', 68, 3000000, 2000000, 200000),
(43, 'Time Travel (II)', 'r', 'First part of TimeTravel in practice! After finishing this research you save your first 3 ticks on every fleet ETA', 68, 4000000, 4000000, 0),
(44, 'Resource Boost', 'r', 'Are not efficiency and improvement the greatest goods? Asteroid income increases with 15%!', 52, 4000000, 4000000, 1000000),
(45, 'Production Spies', 'r', 'Researching for new camouflage techniques for spies to infiltrate in production facilities, creating new Intel: Production Scans', 74, 4000000, 8000000, 1200000),
(46, 'Infiltraitors', 'r', 'Researches and teaches AI moleculair robots to infiltrate organisations like a Galaxy', 80, 3800000, 3800000, 0),
(47, 'Infiltraition Syringes', 'd', 'Enables Political Scans', 68, 6500000, 3800000, 5600000),
(48, 'Stress Releasers', 'd', 'Development of pills to releave workmen of stress and work harder', 30, 20000, 20000, 20000),
(49, 'Work floor management', 'r', 'Teaches evolvement management for efficient desk & factory work', 30, 25000, 25000, 15000);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_r_d_excludes`
-- 

CREATE TABLE `d_r_d_excludes` (
  `r_d_id` int(11) unsigned NOT NULL,
  `r_d_excludes_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`r_d_id`,`r_d_excludes_id`),
  KEY `r_d_id` (`r_d_id`),
  KEY `r_d_excludes_id` (`r_d_excludes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_r_d_excludes`
-- 

INSERT INTO `d_r_d_excludes` VALUES (36, 37),
(37, 36),
(39, 41),
(41, 39),
(42, 44),
(44, 42),
(45, 46),
(46, 45);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_r_d_requires`
-- 

CREATE TABLE `d_r_d_requires` (
  `r_d_id` int(11) unsigned NOT NULL,
  `r_d_requires_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`r_d_id`,`r_d_requires_id`),
  KEY `r_d_id` (`r_d_id`),
  KEY `r_d_requires_id` (`r_d_requires_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_r_d_requires`
-- 

INSERT INTO `d_r_d_requires` VALUES (3, 26),
(4, 1),
(4, 2),
(5, 27),
(6, 28),
(7, 29),
(8, 4),
(9, 30),
(10, 31),
(11, 32),
(12, 33),
(13, 12),
(14, 34),
(15, 36),
(16, 37),
(17, 35),
(18, 17),
(19, 38),
(20, 40),
(21, 41),
(22, 43),
(23, 44),
(24, 23),
(25, 45),
(28, 1),
(29, 2),
(30, 1),
(30, 2),
(30, 3),
(31, 6),
(32, 7),
(33, 8),
(34, 9),
(35, 33),
(36, 14),
(37, 14),
(38, 13),
(38, 17),
(39, 5),
(39, 17),
(40, 39),
(41, 5),
(41, 17),
(42, 10),
(42, 11),
(42, 14),
(43, 42),
(44, 10),
(44, 11),
(44, 14),
(45, 19),
(46, 19),
(47, 46),
(48, 5),
(49, 48);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_r_d_results`
-- 

CREATE TABLE `d_r_d_results` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `type` enum('travel_eta','r_d_eta','income','r_d_costs','fuel_use') NOT NULL,
  `done_r_d_id` int(11) unsigned NOT NULL,
  `change` float(11,2) NOT NULL,
  `unit` enum('real','pct') NOT NULL default 'real',
  `explanation` varchar(255) NOT NULL,
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `r_d_id` (`done_r_d_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_r_d_results`
-- 

INSERT INTO `d_r_d_results` VALUES (1, 'travel_eta', 43, -3.00, 'real', 'Cuts 3 ticks off of your travel eta', '1'),
(2, 'travel_eta', 22, -5.00, 'real', 'Cuts 5 ticks off of your travel eta', '1'),
(3, 'income', 24, 0.15, 'pct', 'Increase your asteroid income with 15%', '1'),
(4, 'r_d_eta', 48, 0.75, 'pct', 'Cuts 25% off of your R & D time', '1'),
(5, 'r_d_eta', 49, 0.75, 'pct', 'Cuts 25% off of your R & D time', '1');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_ships`
-- 

CREATE TABLE `d_ships` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `all_units_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `all_units_id` (`all_units_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_ships`
-- 

INSERT INTO `d_ships` VALUES (1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_smilies`
-- 

CREATE TABLE `d_smilies` (
  `id` tinyint(4) NOT NULL auto_increment,
  `smilie` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_smilies`
-- 

INSERT INTO `d_smilies` VALUES (1, ':D', 'icon_biggrin.gif'),
(2, ':-D', 'icon_biggrin.gif'),
(3, ':S', 'icon_confused.gif'),
(4, ':''(', 'icon_cry.gif'),
(5, ':@', 'icon_evil.gif'),
(6, '=D', 'icon_mrgreen.gif'),
(7, ':(', 'icon_sad.gif'),
(8, ':-(', 'icon_sad.gif'),
(9, '=(', 'icon_sad.gif'),
(10, ':)', 'icon_smile.gif'),
(11, ':-)', 'icon_smile.gif'),
(12, '=)', 'icon_smile.gif'),
(13, ';)', 'icon_wink.gif'),
(14, ';-)', 'icon_wink.gif'),
(15, '=p', 'lalatong.gif'),
(16, '_O-', 'schater.gif'),
(17, '_O_', 'worshippy.gif'),
(18, '+D', 'icon_mrgreen.gif');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `d_waves`
-- 

CREATE TABLE `d_waves` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `all_units_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `all_units_id` (`all_units_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `d_waves`
-- 

INSERT INTO `d_waves` VALUES (1, 14),
(2, 15),
(3, 16),
(4, 17),
(5, 18),
(6, 19),
(7, 20),
(8, 21),
(9, 22),
(10, 23);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `defence_on_planets`
-- 

CREATE TABLE `defence_on_planets` (
  `defence_id` int(11) unsigned NOT NULL,
  `planet_id` int(11) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`defence_id`),
  KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Gegevens worden uitgevoerd voor tabel `defence_on_planets`
-- 

INSERT INTO `defence_on_planets` VALUES (1, 1, 3400);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fleets`
-- 

CREATE TABLE `fleets` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `owner_planet_id` int(11) unsigned NOT NULL,
  `destination_planet_id` int(11) unsigned default NULL,
  `eta` tinyint(4) NOT NULL default '0',
  `starteta` tinyint(4) NOT NULL default '0',
  `action` enum('attack','defend','return') default NULL,
  `action_id` int(11) unsigned default NULL,
  `actiontime` tinyint(4) NOT NULL default '0',
  `startactiontime` tinyint(4) NOT NULL default '0',
  `fleetname` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `owner_planet_id` (`owner_planet_id`),
  KEY `destination_id` (`destination_planet_id`),
  KEY `action_id` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Elke player kan max 3 fleets hebben, waarvan 1 _base' AUTO_INCREMENT=18 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `fleets`
-- 

INSERT INTO `fleets` VALUES (1, 1, NULL, 0, 0, NULL, NULL, 0, 0, '0'),
(2, 1, NULL, 0, 0, NULL, NULL, 0, 0, '1'),
(3, 1, NULL, 0, 0, NULL, NULL, 0, 0, '2'),
(4, 2, NULL, 0, 0, NULL, NULL, 0, 0, '0'),
(5, 2, NULL, 0, 0, NULL, NULL, 0, 0, '1'),
(6, 2, NULL, 0, 0, NULL, NULL, 0, 0, '2'),
(7, 3, NULL, 0, 0, NULL, NULL, 0, 0, '0'),
(8, 3, NULL, 0, 0, NULL, NULL, 0, 0, '1'),
(9, 3, NULL, 0, 0, NULL, NULL, 0, 0, '2'),
(10, 4, NULL, 0, 0, NULL, NULL, 0, 0, '0'),
(11, 4, NULL, 0, 0, NULL, NULL, 0, 0, '1'),
(12, 4, NULL, 0, 0, NULL, NULL, 0, 0, '2'),
(13, 4, NULL, 0, 0, NULL, NULL, 0, 0, '3'),
(14, 5, NULL, 0, 0, NULL, NULL, 0, 0, '0'),
(15, 5, NULL, 0, 0, NULL, NULL, 0, 0, '1'),
(16, 5, NULL, 0, 0, NULL, NULL, 0, 0, '2'),
(17, 5, NULL, 0, 0, NULL, NULL, 0, 0, '3');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `galaxies`
-- 

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
  PRIMARY KEY  (`id`),
  UNIQUE KEY `x-y` (`x`,`y`),
  KEY `gc_planet_id` (`gc_planet_id`),
  KEY `moc_planet_id` (`moc_planet_id`),
  KEY `mow_planet_id` (`mow_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `galaxies`
-- 

INSERT INTO `galaxies` VALUES (1, 1, 1, 'FRoM HeLL', 'images/death.jpg', 'Blaaaaaaaaaaaaat', NULL, NULL, NULL),
(2, 1, 2, 'Far Far Away', 'images/death.jpg', 'Welcome fearless rulers', NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `logbook`
-- 

CREATE TABLE `logbook` (
  `id` bigint(20) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `action` varchar(20) NOT NULL default '',
  `time` bigint(20) NOT NULL default '0',
  `myt` bigint(20) NOT NULL default '0',
  `text` text NOT NULL,
  `ip` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=83 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `logbook`
-- 

INSERT INTO `logbook` VALUES (1, 1, 'login', 1139859266, 2886, '', '127.0.0.1'),
(2, 0, 'affairs', 1139867728, 2886, 'Seems like y=2 from x=1 has been elected GC!', '127.0.0.1'),
(3, 0, 'affairs', 1139867734, 2886, 'Seems like y=2 from x=1 has been elected GC!', '127.0.0.1'),
(4, 1, 'login', 1139868418, 2886, '', '127.0.0.1'),
(5, 1, 'login', 1139872486, 2888, '', '127.0.0.1'),
(6, 1, 'login', 1139872529, 2888, '', '127.0.0.1'),
(7, 1, 'affairs', 1139873179, 2888, 'Seems like y=2 from x=1 has been elected GC!', '127.0.0.1'),
(8, 1, 'affairs', 1139873959, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(9, 1, 'affairs', 1139874172, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(10, 1, 'affairs', 1139874190, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(11, 1, 'affairs', 1139874195, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(12, 1, 'affairs', 1139874214, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(13, 1, 'affairs', 1139874249, 2888, 'Seems like y=3 from x=1 has been elected GC!', '127.0.0.1'),
(14, 1, 'affairs', 1139874262, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(15, 1, 'affairs', 1139874573, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(16, 1, 'affairs', 1139874606, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(17, 1, 'affairs', 1139874612, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(18, 1, 'affairs', 1139874616, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(19, 1, 'affairs', 1139874685, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(20, 1, 'affairs', 1139874690, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(21, 1, 'affairs', 1139874697, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(22, 1, 'affairs', 1139874697, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(23, 1, 'affairs', 1139874698, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(24, 1, 'affairs', 1139874767, 2888, 'Seems like y=3 from x=1 has been elected GC!', '127.0.0.1'),
(25, 1, 'affairs', 1139874772, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(26, 1, 'affairs', 1139874779, 2888, 'Seems like y=3 from x=1 has been elected GC!', '127.0.0.1'),
(27, 1, 'affairs', 1139875005, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(28, 1, 'affairs', 1139875012, 2888, 'Seems like y=3 from x=1 has been elected GC!', '127.0.0.1'),
(29, 1, 'affairs', 1139875014, 2888, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(30, 1, 'affairs', 1139875020, 2888, 'Seems like y=3 from x=1 has been elected GC!', '127.0.0.1'),
(31, 1, 'login', 1140795203, 2888, '', '127.0.0.1'),
(32, 1, 'login', 1140800028, 3081, '', '127.0.0.1'),
(33, 1, 'login', 1140800098, 3081, '', '127.0.0.1'),
(34, 1, 'logout', 1140800106, 3081, '', '127.0.0.1'),
(35, 1, 'login', 1140800118, 3081, '', '127.0.0.1'),
(36, 1, 'logout', 1140800389, 3081, '', '127.0.0.1'),
(37, 1, 'login', 1140800401, 3081, '', '127.0.0.1'),
(38, 1, 'logout', 1140800443, 3081, '', '127.0.0.1'),
(39, 1, 'login', 1140800719, 3081, '', '127.0.0.1'),
(40, 1, 'login', 1140877623, 3081, '', '127.0.0.1'),
(41, 1, 'affairs', 1140882459, 3237, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(42, 1, 'affairs', 1140882474, 3238, 'Seems like y=3 from x=1 has been elected GC!', '127.0.0.1'),
(43, 1, 'affairs', 1140882476, 3238, 'Seems like y=1 from x=1 has been elected GC!', '127.0.0.1'),
(44, 1, 'login', 1140883267, 3239, '', '127.0.0.1'),
(45, 1, 'logout', 1140884204, 3301, '', '127.0.0.1'),
(46, 1, 'login', 1140961594, 3301, '', '127.0.0.1'),
(47, 1, 'logout', 1140963354, 3301, '', '127.0.0.1'),
(48, 3, 'login', 1140963372, 3301, '', '127.0.0.1'),
(49, 3, 'alliance', 1140963412, 3301, 'Just joined <b>RDX</b>], pwd = tag21a3baa', '127.0.0.1'),
(50, 3, 'logout', 1140963602, 3304, '', '127.0.0.1'),
(51, 1, 'login', 1140963612, 3304, '', '127.0.0.1'),
(52, 1, 'login', 1140987360, 3460, '', '127.0.0.1'),
(53, 1, 'logout', 1140988998, 3491, '', '127.0.0.1'),
(54, 3, 'login', 1140989021, 3491, '', '127.0.0.1'),
(55, 3, 'logout', 1140990525, 3640, '', '127.0.0.1'),
(56, 1, 'login', 1140990536, 3640, '', '127.0.0.1'),
(57, 1, 'login', 1141065481, 4190, '', '127.0.0.1'),
(58, 1, 'logout', 1141065596, 4190, '', '127.0.0.1'),
(59, 1, 'login', 1141068388, 4190, '', '127.0.0.1'),
(60, 1, 'login', 1141134272, 4190, '', '127.0.0.1'),
(61, 1, 'login', 1141135795, 4190, '', '127.0.0.1'),
(62, 1, 'login', 1144182628, 4190, '', '127.0.0.1'),
(63, 1, 'login', 1148554601, 4191, '', '127.0.0.1'),
(64, 1, 'login', 1148563736, 4199, '', '10.0.0.150'),
(65, 1, 'military', 1148564849, 4394, 'We sent <b>2000 ships</b> to attack <b>guldan of blabal</b> (1:2); ETA = 31 ticks!', '10.0.0.150'),
(66, 1, 'military', 1148565580, 4409, 'We sent <b>4,120 ships</b> to defend <b>guldan of blabal</b> (1:2); ETA = 16 ticks!', '10.0.0.150'),
(67, 1, 'login', 1148579533, 4434, '', '10.0.0.150'),
(68, 1, 'login', 1148581095, 4544, '', '10.0.0.150'),
(69, 1, 'logout', 1148581173, 4544, '', '10.0.0.150'),
(70, 1, 'login', 1148581185, 4544, '', '10.0.0.150'),
(71, 1, 'military', 1148581281, 4544, 'Our fleet has been destroyed and the crew been killed!', '10.0.0.150'),
(72, 1, 'login', 1148662645, 6558, '', '10.0.0.150'),
(73, 1, 'login', 1148729012, 6561, '', '10.0.0.150'),
(74, 1, 'login', 1148763475, 8779, '', '10.0.0.150'),
(75, 1, 'login', 1148812921, 8779, '', '10.0.0.150'),
(76, 1, 'logout', 1148813501, 8779, '', '10.0.0.150'),
(77, 0, 'forgot_pwd', 1156969467, 8779, 'Your password has been sent to "<b>info@jouwmoeder.nl"', '127.0.0.4'),
(78, 0, 'forgot_pwd', 1156969581, 8779, 'Your password has been sent to "<b>info@jouwmoeder.nl"', '127.0.0.4'),
(79, 1, 'login', 1156969623, 8779, '', '127.0.0.4'),
(80, 1, 'login', 1156970520, 8779, '', '127.0.0.4'),
(81, 1, 'login', 1183498460, 8779, '', '127.0.0.1'),
(82, 1, 'logout', 1183498741, 8797, '', '127.0.0.1');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `mail`
-- 

CREATE TABLE `mail` (
  `id` int(11) NOT NULL auto_increment,
  `to_planet_id` int(11) unsigned NOT NULL,
  `from_planet_id` int(11) unsigned default NULL,
  `utc_sent` int(10) unsigned NOT NULL,
  `myt_sent` int(11) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `seen` enum('0','1') NOT NULL default '0',
  `deleted` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `to_planet_id` (`to_planet_id`),
  KEY `from_planet_id` (`from_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `mail`
-- 

INSERT INTO `mail` VALUES (1, 1, 1, 1197898779, 9815, 'Sexy ho', '1', '1'),
(2, 1, 1, 1197898803, 9815, 'YO HO DE BITCH DOG', '1', '1');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `news`
-- 

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `news`
-- 

INSERT INTO `news` VALUES (1, 1, 1197559923, 8797, 0, 'Your technicians have finished researching <b>More Crystal</b>', '1', '1'),
(2, 1, 1197559929, 8797, 0, 'Your technicians have finished researching <b>First Airport</b>', '1', '1'),
(3, 1, 1197560221, 8797, 8, 'Your technicians have finished researching <b>About Energy</b>', '1', '1'),
(4, 1, 1197561184, 8797, 8, 'Your technicians have finished researching <b>Metal Refinery</b>', '1', '1'),
(5, 1, 1197648023, 8797, 8, 'Your technicians have finished researching <b>More Metal</b>', '1', '1'),
(6, 1, 1197744755, 8797, 8, 'Your technicians have finished researching <b>Robot Factory</b>', '1', '1'),
(7, 1, 1197744772, 8797, 8, 'Your technicians have finished researching <b>Tank Building</b>', '1', '1'),
(8, 1, 1197744783, 8797, 8, 'Your technicians have finished researching <b>EMP Studies</b>', '1', '1'),
(9, 1, 1197744818, 8797, 8, 'Your technicians have finished researching <b>Defence Machines</b>', '1', '1'),
(10, 1, 1197809695, 8820, 8, 'Your technicians have finished researching <b>Unit Patterns</b>', '1', '1'),
(11, 1, 1197809799, 8858, 8, 'Your technicians have finished researching <b>Destroyer Factory</b>', '1', '1'),
(12, 1, 1197811055, 8871, 8, 'Your technicians have finished researching <b>Crystal Refinery</b>', '1', '1'),
(13, 1, 1197811059, 8873, 8, 'Your technicians have finished researching <b>About Scanning</b>', '1', '1'),
(14, 1, 1197811108, 8881, 8, 'Your technicians have finished researching <b>Sector Scanning</b>', '1', '1'),
(15, 1, 1197811129, 8886, 8, 'Your technicians have finished researching <b>Metal Refinery</b>', '1', '1'),
(16, 1, 1197811358, 8891, 8, 'Your technicians have finished researching <b>About Energy</b>', '1', '1'),
(17, 1, 1197836840, 8900, 8, 'Your technicians have finished researching <b>More Crystal</b>', '1', '1'),
(18, 1, 1197836841, 8902, 8, 'Your technicians have finished researching <b>First Airport</b>', '1', '1'),
(19, 1, 1197847220, 8947, 8, 'Your technicians have finished researching <b>Making Energy</b>', '1', '1'),
(20, 1, 1197847220, 8948, 8, 'Your technicians have finished researching <b>More Metal</b>', '1', '1'),
(21, 1, 1197847345, 8959, 8, 'Your technicians have finished researching <b>Unit Patterns</b>', '1', '1'),
(22, 1, 1197847350, 8963, 8, 'Your technicians have finished researching <b>Robot Factory</b>', '1', '1'),
(23, 1, 1197847413, 8980, 8, 'Your technicians have finished researching <b>Tank Building</b>', '1', '1'),
(24, 1, 1197866564, 8994, 8, 'Your technicians have finished researching <b>Get More Crystal</b>', '1', '1'),
(25, 1, 1197866601, 9009, 8, 'Your technicians have finished researching <b>EMP Studies</b>', '1', '1'),
(26, 1, 1197866869, 9119, 8, 'Your technicians have finished researching <b>Unit Scanning</b>', '1', '1'),
(27, 1, 1197866869, 9119, 8, 'Your technicians have finished researching <b>"Blind Diggers" for Crystal</b>', '1', '1'),
(28, 1, 1197867181, 9247, 8, 'Your technicians have finished researching <b>Defence Patterns</b>', '1', '1'),
(29, 1, 1197867214, 9260, 8, 'Your technicians have finished researching <b>Defence Machines</b>', '1', '1'),
(30, 1, 1197867513, 9381, 8, 'Your technicians have finished researching <b>Destroyer Factory</b>', '1', '1'),
(31, 1, 1197867687, 9448, 8, 'Your technicians have finished researching <b>Defence Scanning</b>', '1', '1'),
(32, 1, 1197867949, 9552, 8, 'Your technicians have finished researching <b>Hardcore Crystal</b>', '1', '1'),
(33, 1, 1197916523, 10670, 8, 'Your technicians have finished researching <b>Get More Metal</b>', '1', '0'),
(34, 1, 1198018060, 10686, 8, 'Your technicians have finished researching <b>Fleet Signatures</b>', '1', '0'),
(35, 1, 1198018132, 10715, 8, 'Your technicians have finished researching <b>Stress Releasers</b>', '1', '0'),
(36, 1, 1198019727, 11073, 8, 'Your technicians have finished researching <b>Work floor management</b>', '1', '0'),
(37, 1, 1198020777, 11533, 8, 'Your technicians have finished researching <b>"Blind Diggers" for Metal</b>.', '1', '0');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `old_intel`
-- 

CREATE TABLE `old_intel` (
  `id` int(11) NOT NULL auto_increment,
  `planet_id` int(11) unsigned NOT NULL,
  `target_planet_id` int(11) unsigned NOT NULL,
  `utc_time` int(10) unsigned NOT NULL,
  `myt` int(11) unsigned NOT NULL,
  `soort` varchar(22) NOT NULL default 'sectors',
  `result` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `old_intel`
-- 

INSERT INTO `old_intel` VALUES (1, 1, 2, 1140798998, 0, 'sector', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>BASIC Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200>Score:</td><td width=2><br></td><td>75,980,392</td></tr>\r\n<tr><td align=right>Metal:</td><td><br></td><td>14,273,516</td></tr>\r\n<tr><td align=right>Crystal:</td><td><br></td><td>16,973,084</td></tr>\r\n<tr><td align=right>Energy:</td><td><br></td><td>36,301,865</td></tr>\r\n<tr><td align=right>Metal Asteroids:</td><td><br></td><td>138</td></tr>\r\n<tr><td align=right>Crystal Asteroids:</td><td><br></td><td>227</td></tr>\r\n<tr><td align=right>Undeveloped Asteroids:</td><td><br></td><td>59</td></tr>\r\n<tr><td align=right>PDU:</td><td><br></td><td>8,306</td></tr>\r\n<tr><td align=right>Ships:</td><td><br></td><td>253,932</td></tr>\r\n</table>\r\n'),
(2, 1, 2, 1140799051, 0, 'unit', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>UNIT Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200 style=\\''border-bottom:solid 1px #444444;\\''><b>Total:</td><td width=2 style=\\''border-bottom:solid 1px #444444;\\''><br></td><td style=\\''border-bottom:solid 1px #444444;\\''><b>253,932</td></tr>\r\n<tr><td align=right>Infinitys:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Wraiths:</td><td><br></td><td>250,819</td></tr>\r\n<tr><td align=right>Warfrigates:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Astropods:</td><td><br></td><td>3,113</td></tr>\r\n<tr><td align=right>Cobras:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Destroyers:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Antennas:</td><td><br></td><td>0</td></tr>\r\n</table>\r\n'),
(3, 1, 2, 1140799083, 0, 'pdu', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>PDU Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200 style=\\''border-bottom:solid 1px #444444;\\''><b>Total:</td><td style=\\''border-bottom:solid 1px #444444;\\'' width=2><br></td><td style=\\''border-bottom:solid 1px #444444;\\''><b>8,306</td></tr>\r\n<tr><td align=right>Reaper Cannons:</td><td><br></td><td>8,306</td></tr>\r\n<tr><td align=right>Avengers:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Lucius Stalkers:</td><td><br></td><td>0</td></tr>\r\n</table>\r\n'),
(4, 1, 2, 1140799129, 0, 'unit', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>UNIT Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200 style=\\''border-bottom:solid 1px #444444;\\''><b>Total:</td><td width=2 style=\\''border-bottom:solid 1px #444444;\\''><br></td><td style=\\''border-bottom:solid 1px #444444;\\''><b>253,932</td></tr>\r\n<tr><td align=right>Infinitys:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Wraiths:</td><td><br></td><td>250,819</td></tr>\r\n<tr><td align=right>Warfrigates:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Astropods:</td><td><br></td><td>3,113</td></tr>\r\n<tr><td align=right>Cobras:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Destroyers:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Antennas:</td><td><br></td><td>0</td></tr>\r\n</table>\r\n'),
(5, 1, 2, 1140799133, 0, 'unit', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>UNIT Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200 style=\\''border-bottom:solid 1px #444444;\\''><b>Total:</td><td width=2 style=\\''border-bottom:solid 1px #444444;\\''><br></td><td style=\\''border-bottom:solid 1px #444444;\\''><b>253,932</td></tr>\r\n<tr><td align=right>Infinitys:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Wraiths:</td><td><br></td><td>250,819</td></tr>\r\n<tr><td align=right>Warfrigates:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Astropods:</td><td><br></td><td>3,113</td></tr>\r\n<tr><td align=right>Cobras:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Destroyers:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Antennas:</td><td><br></td><td>0</td></tr>\r\n</table>\r\n'),
(6, 1, 3, 1140799183, 0, 'sector', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>BASIC Infiltration Report on master of Valdres (1:3)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200>Score:</td><td width=2><br></td><td>674,641</td></tr>\r\n<tr><td align=right>Metal:</td><td><br></td><td>54,208,139</td></tr>\r\n<tr><td align=right>Crystal:</td><td><br></td><td>33,340,753</td></tr>\r\n<tr><td align=right>Energy:</td><td><br></td><td>32,649,240</td></tr>\r\n<tr><td align=right>Metal Asteroids:</td><td><br></td><td>29</td></tr>\r\n<tr><td align=right>Crystal Asteroids:</td><td><br></td><td>4</td></tr>\r\n<tr><td align=right>Undeveloped Asteroids:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>PDU:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Ships:</td><td><br></td><td>805</td></tr>\r\n</table>\r\n'),
(7, 1, 2, 1140878487, 0, 'sector', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>BASIC Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200>Score:</td><td width=2><br></td><td>75,988,096</td></tr>\r\n<tr><td align=right>Metal:</td><td><br></td><td>14,686,225</td></tr>\r\n<tr><td align=right>Crystal:</td><td><br></td><td>17,469,778</td></tr>\r\n<tr><td align=right>Energy:</td><td><br></td><td>36,933,155</td></tr>\r\n<tr><td align=right>Metal Asteroids:</td><td><br></td><td>138</td></tr>\r\n<tr><td align=right>Crystal Asteroids:</td><td><br></td><td>227</td></tr>\r\n<tr><td align=right>Undeveloped Asteroids:</td><td><br></td><td>59</td></tr>\r\n<tr><td align=right>PDU:</td><td><br></td><td>8,306</td></tr>\r\n<tr><td align=right>Ships:</td><td><br></td><td>253,932</td></tr>\r\n</table>\r\n'),
(8, 1, 2, 1140878543, 0, 'fleet', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>FLEET Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0>\r\n<tr><td align=right width=200> </td><td><br></td><td width=100><center>Fleet I</td><td width=100><center>Fleet II</td></tr>\r\n<tr><td align=right><b>Total:</td><td><br></td><td><b><center>0</td><td><b><center>0</td></tr>\r\n<tr><td align=right>Infinitys:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n<tr><td align=right>Wraiths:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n<tr><td align=right>Warfrigates:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n<tr><td align=right>Astropods:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n<tr><td align=right>Cobras:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n<tr><td align=right>Destroyers:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n<tr><td align=right>Scorpions:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n<tr><td align=right>Antennas:</td><td><br></td><td><center>0</td><td><center>0</td></tr>\r\n</table>\r\n'),
(9, 1, 2, 1140878577, 0, 'unit', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>UNIT Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200 style=\\''border-bottom:solid 1px #444444;\\''><b>Total:</td><td width=2 style=\\''border-bottom:solid 1px #444444;\\''><br></td><td style=\\''border-bottom:solid 1px #444444;\\''><b>253,932</td></tr>\r\n<tr><td align=right>Infinitys:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Wraiths:</td><td><br></td><td>250,819</td></tr>\r\n<tr><td align=right>Warfrigates:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Astropods:</td><td><br></td><td>3,113</td></tr>\r\n<tr><td align=right>Cobras:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Destroyers:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Antennas:</td><td><br></td><td>0</td></tr>\r\n</table>\r\n'),
(10, 1, 2, 1140878616, 0, 'unit', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>UNIT Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200 style=\\''border-bottom:solid 1px #444444;\\''><b>Total:</td><td width=2 style=\\''border-bottom:solid 1px #444444;\\''><br></td><td style=\\''border-bottom:solid 1px #444444;\\''><b>253,932</td></tr>\r\n<tr><td align=right>Infinitys:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Wraiths:</td><td><br></td><td>250,819</td></tr>\r\n<tr><td align=right>Warfrigates:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Astropods:</td><td><br></td><td>3,113</td></tr>\r\n<tr><td align=right>Cobras:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Destroyers:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Antennas:</td><td><br></td><td>0</td></tr>\r\n</table>\r\n'),
(11, 1, 2, 1140962915, 0, 'sector', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>BASIC Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200>Score:</td><td width=2><br></td><td>76,137,739</td></tr>\r\n<tr><td align=right>Metal:</td><td><br></td><td>22,978,008</td></tr>\r\n<tr><td align=right>Crystal:</td><td><br></td><td>27,448,896</td></tr>\r\n<tr><td align=right>Energy:</td><td><br></td><td>48,590,905</td></tr>\r\n<tr><td align=right>Metal Asteroids:</td><td><br></td><td>138</td></tr>\r\n<tr><td align=right>Crystal Asteroids:</td><td><br></td><td>227</td></tr>\r\n<tr><td align=right>Undeveloped Asteroids:</td><td><br></td><td>12,345</td></tr>\r\n<tr><td align=right>PDU:</td><td><br></td><td>8,306</td></tr>\r\n<tr><td align=right>Ships:</td><td><br></td><td>253,932</td></tr>\r\n</table>\r\n'),
(12, 1, 2, 1140962982, 0, 'sector', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>BASIC Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200>Score:</td><td width=2><br></td><td>76,137,739</td></tr>\r\n<tr><td align=right>Metal:</td><td><br></td><td>22,978,008</td></tr>\r\n<tr><td align=right>Crystal:</td><td><br></td><td>27,448,896</td></tr>\r\n<tr><td align=right>Energy:</td><td><br></td><td>48,590,905</td></tr>\r\n<tr><td align=right>Metal Asteroids:</td><td><br></td><td>138</td></tr>\r\n<tr><td align=right>Crystal Asteroids:</td><td><br></td><td>227</td></tr>\r\n<tr><td align=right>Undeveloped Asteroids:</td><td><br></td><td>12,345</td></tr>\r\n<tr><td align=right>PDU:</td><td><br></td><td>8,306</td></tr>\r\n<tr><td align=right>Ships:</td><td><br></td><td>253,932</td></tr>\r\n</table>\r\n'),
(13, 1, 2, 1140963093, 0, 'sector', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>BASIC Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200>Score:</td><td width=2><br></td><td>76,137,739</td></tr>\r\n<tr><td align=right>Metal:</td><td><br></td><td>22,978,008</td></tr>\r\n<tr><td align=right>Crystal:</td><td><br></td><td>27,448,896</td></tr>\r\n<tr><td align=right>Energy:</td><td><br></td><td>48,590,905</td></tr>\r\n<tr><td align=right>Metal Asteroids:</td><td><br></td><td>138</td></tr>\r\n<tr><td align=right>Crystal Asteroids:</td><td><br></td><td>227</td></tr>\r\n<tr><td align=right>Undeveloped Asteroids:</td><td><br></td><td>12,345</td></tr>\r\n<tr><td align=right>PDU:</td><td><br></td><td>8,306</td></tr>\r\n<tr><td align=right>Ships:</td><td><br></td><td>253,932</td></tr>\r\n</table>\r\n'),
(14, 1, 2, 1148579563, 0, 'unit', '<table border=0 cellpadding=2 cellspacing=0 width=100%><tr><td style=\\''border:solid 1px #444444;\\''><center><b>UNIT Infiltration Report on guldan of blabal (1:2)</b></td></tr></table>\r\n<table border=0 cellpadding=2 cellspacing=0 width=100%>\r\n<tr><td align=right width=200 style=\\''border-bottom:solid 1px #444444;\\''><b>Total:</td><td width=2 style=\\''border-bottom:solid 1px #444444;\\''><br></td><td style=\\''border-bottom:solid 1px #444444;\\''><b>253,932</td></tr>\r\n<tr><td align=right>Infinitys:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Wraiths:</td><td><br></td><td>250,819</td></tr>\r\n<tr><td align=right>Warfrigates:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Astropods:</td><td><br></td><td>3,113</td></tr>\r\n<tr><td align=right>Cobras:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Destroyers:</td><td><br></td><td>0</td></tr>\r\n<tr><td align=right>Antennas:</td><td><br></td><td>0</td></tr>\r\n</table>\r\n');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `online`
-- 

CREATE TABLE `online` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `ip` varchar(99) NOT NULL default '',
  `time` bigint(20) NOT NULL default '0',
  `uniek` varchar(99) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `online`
-- 

INSERT INTO `online` VALUES (1, 1, '127.0.0.1', 1139859671, '9268da5ca52c59450153bc89ced18c26'),
(2, 1, '127.0.0.1', 1139872462, '6cdad4542dc967ee482fdde3cdb67473'),
(3, 1, '127.0.0.1', 1139872515, 'fafdf4284cd035f826e79d3b4964fced'),
(4, 1, '127.0.0.1', 1139875562, '6cd59ac744a79d40239b4e89bfa120a2'),
(5, 1, '127.0.0.1', 1140799973, '48aa5e3eeb40f5b58eb80d64ba678950'),
(6, 1, '127.0.0.1', 1140800049, 'ce8bb1892ba73c754ba73acc6a2f9b53'),
(10, 1, '127.0.0.1', 1140800842, '5d1460f21faf091babacbb970e3678b1'),
(11, 1, '127.0.0.1', 1140882729, 'cc8acc73857a219b1c0da4ad5b30845d'),
(15, 1, '127.0.0.1', 1141135255, '9f410c6e3a2c0656351f1ec808604d4c'),
(18, 1, '127.0.0.1', 1140996108, 'f32b17b671bde87310caa4fb02394027'),
(20, 1, '127.0.0.1', 1141069094, 'b8fb14b9d411925f971d7aef9f4b3de0'),
(21, 1, '127.0.0.1', 1141135772, '929ff0cd0c6fbc5b0a90a3d94d830c47'),
(22, 1, '127.0.0.1', 1141137484, '00110648f4160955e40bf9908fc04ff7'),
(23, 1, '127.0.0.1', 1144186351, '55f1a645b68e61ca63ab5bdf3baead28'),
(24, 1, '127.0.0.1', 1148554638, 'e38aef42cf18dd3c64cc2448b2c9c126'),
(25, 1, '10.0.0.150', 1148565674, 'f2eac075dc320bd85bf19ebd4b4832dd'),
(26, 1, '10.0.0.150', 1148580175, '45001ef47ae001b84392a5dcc269ca2c'),
(28, 1, '10.0.0.150', 1148590629, '345e13527c73ef0f7e72918a54805e64'),
(29, 1, '10.0.0.150', 1148663597, 'ff6a466f128e5c0d17d0b53bef8b6fed'),
(30, 1, '10.0.0.150', 1148733964, '3aa955d862bd4f17d1e5290c081ac833'),
(31, 1, '10.0.0.150', 1148770346, 'aafa4af79fed722af630a6e0795cd666'),
(33, 1, '127.0.0.4', 1156969726, 'bd0949b2ef6be4c1848a732e01c2a2ae'),
(34, 1, '127.0.0.4', 1156971565, '9dab4c6661ad8ba56f480bef6079edb9');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `planets`
-- 

CREATE TABLE `planets` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(30) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `activationcode` varchar(255) NOT NULL default '',
  `new_email` varchar(255) NOT NULL default '',
  `new_email_code` varchar(255) NOT NULL default '',
  `old_email_aproved` enum('0','1') NOT NULL default '0',
  `oldpwd` enum('1','0') NOT NULL default '1',
  `ready2play` enum('0','1') NOT NULL default '0',
  `rulername` varchar(40) NOT NULL default '',
  `planetname` varchar(40) NOT NULL default '',
  `galaxy_id` int(11) unsigned NOT NULL,
  `z` tinyint(4) unsigned NOT NULL,
  `crystal` bigint(20) NOT NULL default '0',
  `metal` bigint(20) NOT NULL default '1000',
  `energy` bigint(20) NOT NULL default '0',
  `crystal_asteroids` int(11) unsigned NOT NULL,
  `metal_asteroids` int(11) unsigned NOT NULL,
  `uninitiated_asteroids` int(11) unsigned NOT NULL,
  `all_res_done` enum('0','1') NOT NULL default '0',
  `all_con_done` enum('0','1') NOT NULL default '0',
  `lastlogin` int(10) NOT NULL default '0',
  `lastaction` int(10) NOT NULL default '0',
  `alliance_id` int(11) unsigned default NULL,
  `score` bigint(20) unsigned NOT NULL,
  `sleep` int(11) NOT NULL default '0',
  `nextsleep` int(10) NOT NULL default '0',
  `vacation` int(10) NOT NULL default '0',
  `closed` enum('0','1') NOT NULL default '0',
  `galaxyfunction` enum('GC','MoC','MoW') default NULL,
  `vote` int(11) NOT NULL default '0',
  `newbie` int(11) NOT NULL default '200',
  `journal` text NOT NULL,
  `show_all_r_d` enum('0','1') NOT NULL default '1',
  `autoscansave` enum('0','1') NOT NULL default '0',
  `unihash` varchar(40) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `rulername` (`rulername`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `planetname` (`planetname`),
  UNIQUE KEY `z-in-galaxy` (`galaxy_id`,`z`),
  KEY `tag` (`alliance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `planets`
-- 

INSERT INTO `planets` VALUES (1, 'info@jouwmoeder.nl', '1a1347681a2adead044b1e0e2a3ff99a', '', '', '', '0', '0', '1', 'Lazy Rudie', 'Sleepy Lazy Planet', 1, 2, 3259879, 32421970, 155459049, 233, 12504, 0, '0', '0', 1198082845, 1198100580, NULL, 6946832, 0, 1140851119, 0, '0', 'GC', 1, 0, 'Kenkert', '0', '0', '63c4b9452d95cda772b654501e47bf2b'),
(2, 'barthoogenboezem@hotmail.com', 'f4e6c7068cd7fe2be8f055ae0756df85', '', '', '', '0', '1', '1', 'guldan', 'blabal', 1, 1, 275614926, 229187928, 292536505, 227, 138, 12345, '0', '0', 1138504060, 1197544441, NULL, 1649429, 0, 0, 0, '0', 'MoW', 1, 0, 'Welcome to the game\r\nYou can use this area for personal notes.\r\nIt is guaranteed that only you will be able to view these notes, so you can store passwords, old intelligence, old mail, etc.\r\n\r\nThe Team wishes you the best of luck and a fun game!', '1', '0', NULL),
(3, 'master_greyfox@hotmail.com', 'dcde9ae7d3d721989218ecc903966e1d', '', '', '', '0', '1', '1', 'master', 'Valdres', 1, 3, 101309452, 167695304, 100109840, 4, 29, 12345, '0', '0', 0, 1197544441, NULL, 743179, 0, 0, 0, '0', 'MoC', 3, 0, 'Welcome to the game\r\nYou can use this area for personal notes.\r\nIt is guaranteed that only you will be able to view these notes, so you can store passwords, old intelligence, old mail, etc.\r\n\r\nThe Team wishes you the best of luck and a fun game!', '1', '0', NULL),
(4, 'jaap@kk.nl', '3c32b0fc35ddb226604a7e886057a19f', '82aed6892414dfef49c56c2923d816a3', '', '', '0', '1', '0', 'japie', 'dejaaps', 2, 1, 0, 1000, 0, 0, 0, 0, '0', '0', 0, 0, NULL, 2, 0, 0, 0, '0', NULL, 0, 200, '', '1', '0', NULL),
(5, 'rudie@jouwmoeder.nl', 'ce4088a4fe6d325cd82929a00fd12a60', '', '', '', '0', '1', '0', 'Rudie', 'Veldhoven', 2, 2, 0, 1000, 0, 0, 0, 0, '0', '0', 1197933643, 1197933766, NULL, 2, 0, 0, 0, '0', NULL, 0, 200, '', '1', '0', '151b87b63b5c4939c627261ba4abf791');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `politics`
-- 

CREATE TABLE `politics` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `parent_thread_id` int(11) unsigned default NULL,
  `owner_x` int(11) unsigned NOT NULL,
  `owner_y` int(11) unsigned NOT NULL,
  `utc_time` int(10) unsigned NOT NULL,
  `title` varchar(40) default NULL,
  `message` text NOT NULL,
  `creator_planet_id` int(11) unsigned NOT NULL,
  `deleted` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent_thread_id` (`parent_thread_id`),
  KEY `creator_planet_id` (`creator_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `politics`
-- 

INSERT INTO `politics` VALUES (1, NULL, 1, 0, 1139870740, 'yo', 'gekke', 1, '0'),
(2, NULL, 1, 0, 1139870869, 'fck', 'bitch', 1, '0'),
(3, NULL, 1, 0, 1139870878, 'fucking twee yo:D', 'sletjess', 1, '0'),
(4, 3, 1, 0, 1139871111, '', 'teringhomo', 1, '0'),
(5, 3, 1, 0, 1139871120, '', 'fuckerbitsj', 1, '0'),
(6, NULL, 1, 0, 1140988214, '', 'test :)', 1, '1'),
(7, NULL, 1, 0, 1140988343, '', 'wdf', 1, '1'),
(8, NULL, 1, 0, 1140988359, '', 'asdf', 1, '1'),
(9, NULL, 1, 0, 1140988449, 'titel :)', 'sdf GVD :D\r\n', 1, '1'),
(10, 9, 1, 0, 1140988573, '', 'jah jah jah jah jah jij wint :D', 1, '1'),
(11, 9, 1, 0, 1140988582, '', 'sad', 1, '1'),
(12, 9, 1, 0, 1140988589, '', 'asdasdasdasdsasdsaSD', 1, '1'),
(13, 9, 1, 0, 1140988958, '', '', 1, '1'),
(14, 3, 1, 0, 1140990173, '', 'sletje :D', 3, '0'),
(15, 3, 1, 0, 1144186324, '', 'teef\r\n', 1, '0');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `power_on_planets`
-- 

CREATE TABLE `power_on_planets` (
  `power_id` int(11) unsigned NOT NULL,
  `planet_id` int(11) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`power_id`,`planet_id`),
  KEY `power_id` (`power_id`),
  KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Gegevens worden uitgevoerd voor tabel `power_on_planets`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `prefs`
-- 

CREATE TABLE `prefs` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `gamename` varchar(24) NOT NULL default '',
  `tickcount` int(11) unsigned NOT NULL default '0',
  `last_tick` int(11) unsigned NOT NULL default '0',
  `military_attack` enum('1','0') NOT NULL default '1',
  `military_defend` enum('1','0') NOT NULL default '1',
  `military_scorelimit` tinyint(4) unsigned NOT NULL default '0',
  `tickertime` int(11) unsigned NOT NULL default '20',
  `general_login` enum('1','0') NOT NULL default '1',
  `general_signup` enum('1','0') NOT NULL default '1',
  `general_adminmsg` text NOT NULL,
  `pwd_voor_ticker` enum('1','0') NOT NULL default '1',
  `ticker_on` enum('1','0') NOT NULL default '1',
  `general_gamestoptick` int(11) unsigned NOT NULL default '0',
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
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `prefs`
-- 

INSERT INTO `prefs` VALUES (1, 'PORNSTARS v2', 11591, 1198092997, '1', '1', 2, 2, '1', '1', 'Ticker: <a href="tickah.php" target=t1>hierzo</a>.\r\nAls je wilt ticken, graag. Je kan in het bovenste menu de ticker iets discreter aanzetten. Gebruikt weinig of geen extra geheugen. Als je alleen speelt, zet m aan. Anders: zet m ook maar aan.', '0', '1', 0, '0', '1', '0', '1', '1', '1', 10, 3, '1', '0', 3, 'Home,Alpha,Beta,Gamma,Delta,Epsilon,Zeta,Iota,Kappa');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `production`
-- 

CREATE TABLE `production` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `planet_id` int(11) unsigned NOT NULL,
  `unit_id` int(11) unsigned NOT NULL,
  `eta` tinyint(4) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`planet_id`),
  KEY `unit_id` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Gegevens worden uitgevoerd voor tabel `production`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `r_d_per_planet`
-- 

CREATE TABLE `r_d_per_planet` (
  `r_d_id` int(11) unsigned NOT NULL,
  `planet_id` int(11) unsigned NOT NULL,
  `eta` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY  (`r_d_id`,`planet_id`),
  KEY `r_d_id` (`r_d_id`),
  KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Gegevens worden uitgevoerd voor tabel `r_d_per_planet`
-- 

INSERT INTO `r_d_per_planet` VALUES (1, 1, 0),
(2, 1, 0),
(3, 1, 0),
(4, 1, 0),
(5, 1, 0),
(6, 1, 0),
(7, 1, 0),
(8, 1, 0),
(9, 1, 0),
(10, 1, 0),
(12, 1, 0),
(14, 1, 0),
(17, 1, 0),
(26, 1, 0),
(27, 1, 0),
(28, 1, 0),
(29, 1, 0),
(30, 1, 0),
(31, 1, 0),
(32, 1, 0),
(33, 1, 0),
(34, 1, 0),
(35, 1, 0),
(36, 1, 0),
(48, 1, 0),
(49, 1, 0);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ships_in_fleets`
-- 

CREATE TABLE `ships_in_fleets` (
  `fleet_id` int(11) unsigned NOT NULL,
  `ship_id` int(11) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fleet_id`,`ship_id`),
  KEY `fleet_id` (`fleet_id`),
  KEY `ship_id` (`ship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Gegevens worden uitgevoerd voor tabel `ships_in_fleets`
-- 

INSERT INTO `ships_in_fleets` VALUES (1, 1, 40000),
(1, 3, 90000);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `waves_on_planets`
-- 

CREATE TABLE `waves_on_planets` (
  `wave_id` int(11) unsigned NOT NULL,
  `planet_id` int(11) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`planet_id`,`wave_id`),
  KEY `planet_id` (`planet_id`),
  KEY `wave_id` (`wave_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Gegevens worden uitgevoerd voor tabel `waves_on_planets`
-- 


-- 
-- Beperkingen voor gedumpte tabellen
-- 

-- 
-- Beperkingen voor tabel `alliances`
-- 
ALTER TABLE `alliances`
  ADD CONSTRAINT `alliances_fk` FOREIGN KEY (`leader_id`) REFERENCES `planets` (`id`);

-- 
-- Beperkingen voor tabel `d_all_units`
-- 
ALTER TABLE `d_all_units`
  ADD CONSTRAINT `d_units_fk` FOREIGN KEY (`r_d_required_id`) REFERENCES `d_r_d_available` (`id`);

-- 
-- Beperkingen voor tabel `d_defence`
-- 
ALTER TABLE `d_defence`
  ADD CONSTRAINT `d_defence_fk` FOREIGN KEY (`all_units_id`) REFERENCES `d_all_units` (`id`);

-- 
-- Beperkingen voor tabel `d_power`
-- 
ALTER TABLE `d_power`
  ADD CONSTRAINT `d_power_fk` FOREIGN KEY (`all_units_id`) REFERENCES `d_all_units` (`id`);

-- 
-- Beperkingen voor tabel `d_r_d_excludes`
-- 
ALTER TABLE `d_r_d_excludes`
  ADD CONSTRAINT `r_d_excludes_fk` FOREIGN KEY (`r_d_id`) REFERENCES `d_r_d_available` (`id`),
  ADD CONSTRAINT `r_d_excludes_fk1` FOREIGN KEY (`r_d_excludes_id`) REFERENCES `d_r_d_available` (`id`);

-- 
-- Beperkingen voor tabel `d_r_d_requires`
-- 
ALTER TABLE `d_r_d_requires`
  ADD CONSTRAINT `r_d_requires_fk` FOREIGN KEY (`r_d_id`) REFERENCES `d_r_d_available` (`id`);

-- 
-- Beperkingen voor tabel `d_r_d_results`
-- 
ALTER TABLE `d_r_d_results`
  ADD CONSTRAINT `r_d_results_fk` FOREIGN KEY (`done_r_d_id`) REFERENCES `d_r_d_available` (`id`);

-- 
-- Beperkingen voor tabel `d_ships`
-- 
ALTER TABLE `d_ships`
  ADD CONSTRAINT `d_ships_fk` FOREIGN KEY (`all_units_id`) REFERENCES `d_all_units` (`id`);

-- 
-- Beperkingen voor tabel `d_waves`
-- 
ALTER TABLE `d_waves`
  ADD CONSTRAINT `d_waves_fk` FOREIGN KEY (`all_units_id`) REFERENCES `d_all_units` (`id`);

-- 
-- Beperkingen voor tabel `defence_on_planets`
-- 
ALTER TABLE `defence_on_planets`
  ADD CONSTRAINT `defence_on_planets_fk` FOREIGN KEY (`defence_id`) REFERENCES `d_defence` (`id`),
  ADD CONSTRAINT `defence_on_planet_fk1` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`);

-- 
-- Beperkingen voor tabel `fleets`
-- 
ALTER TABLE `fleets`
  ADD CONSTRAINT `fleets_fk` FOREIGN KEY (`owner_planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `fleets_fk1` FOREIGN KEY (`destination_planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `fleets_fk2` FOREIGN KEY (`action_id`) REFERENCES `d_fleet_actions` (`id`);

-- 
-- Beperkingen voor tabel `galaxies`
-- 
ALTER TABLE `galaxies`
  ADD CONSTRAINT `galaxies_fk` FOREIGN KEY (`gc_planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `galaxies_fk1` FOREIGN KEY (`moc_planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `galaxies_fk2` FOREIGN KEY (`mow_planet_id`) REFERENCES `planets` (`id`);

-- 
-- Beperkingen voor tabel `mail`
-- 
ALTER TABLE `mail`
  ADD CONSTRAINT `mail_fk` FOREIGN KEY (`to_planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `mail_fk1` FOREIGN KEY (`from_planet_id`) REFERENCES `planets` (`id`);

-- 
-- Beperkingen voor tabel `news`
-- 
ALTER TABLE `news`
  ADD CONSTRAINT `news_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `news_fk1` FOREIGN KEY (`news_subject_id`) REFERENCES `d_news_subjects` (`id`);

-- 
-- Beperkingen voor tabel `planets`
-- 
ALTER TABLE `planets`
  ADD CONSTRAINT `planets_fk` FOREIGN KEY (`alliance_id`) REFERENCES `alliances` (`id`),
  ADD CONSTRAINT `planets_fk1` FOREIGN KEY (`galaxy_id`) REFERENCES `galaxies` (`id`);

-- 
-- Beperkingen voor tabel `politics`
-- 
ALTER TABLE `politics`
  ADD CONSTRAINT `politics_fk` FOREIGN KEY (`parent_thread_id`) REFERENCES `politics` (`id`),
  ADD CONSTRAINT `politics_fk1` FOREIGN KEY (`creator_planet_id`) REFERENCES `planets` (`id`);

-- 
-- Beperkingen voor tabel `power_on_planets`
-- 
ALTER TABLE `power_on_planets`
  ADD CONSTRAINT `power_on_planet_fk` FOREIGN KEY (`power_id`) REFERENCES `d_power` (`id`),
  ADD CONSTRAINT `power_on_planet_fk1` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`);

-- 
-- Beperkingen voor tabel `production`
-- 
ALTER TABLE `production`
  ADD CONSTRAINT `production_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `production_fk1` FOREIGN KEY (`unit_id`) REFERENCES `d_all_units` (`id`);

-- 
-- Beperkingen voor tabel `r_d_per_planet`
-- 
ALTER TABLE `r_d_per_planet`
  ADD CONSTRAINT `r_d_per_planet_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `r_d_per_user_fk` FOREIGN KEY (`r_d_id`) REFERENCES `d_r_d_available` (`id`);

-- 
-- Beperkingen voor tabel `ships_in_fleets`
-- 
ALTER TABLE `ships_in_fleets`
  ADD CONSTRAINT `ships_in_fleets_fk` FOREIGN KEY (`fleet_id`) REFERENCES `fleets` (`id`),
  ADD CONSTRAINT `ships_in_fleets_fk1` FOREIGN KEY (`ship_id`) REFERENCES `d_ships` (`id`);

-- 
-- Beperkingen voor tabel `waves_on_planets`
-- 
ALTER TABLE `waves_on_planets`
  ADD CONSTRAINT `waves_on_planet_fk` FOREIGN KEY (`planet_id`) REFERENCES `planets` (`id`),
  ADD CONSTRAINT `waves_on_planet_fk1` FOREIGN KEY (`wave_id`) REFERENCES `d_waves` (`id`);