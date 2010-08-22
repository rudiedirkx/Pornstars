<?

#  server host , username, and password(blank)

#$db = mysql_connect("http://www.ewars.f2o.org", "ewars", "jeffrey");
require("dblogon.php");

#$result = mysql_query("CREATE DATABASE planetarion");

mysql_select_db("pci");

echo "created database: pci planetarion\n<BR>";
$result = mysql_query("DROP TABLE IF EXISTS topcontinent");
mysql_query("CREATE TABLE topcontinent (x INT,score BIGINT(255), galname VARCHAR(150))");


// Start generating tables
$result = mysql_query("DROP TABLE IF EXISTS pa_users");
$result = mysql_query("DROP TABLE IF EXISTS pa_alliance");
$result = mysql_query("DROP TABLE IF EXISTS pa_news");
$result = mysql_query("DROP TABLE IF EXISTS pa_logging");
$result = mysql_query("DROP TABLE IF EXISTS pa_tags");
$result = mysql_query("DROP TABLE IF EXISTS pa_mail");
$result = mysql_query("DROP TABLE IF EXISTS pa_galaxies");


$result = mysql_query("CREATE TABLE pa_users (
   id int(11) NOT NULL PRIMARY KEY auto_increment,
   name varchar(30) NOT NULL,
   nick varchar(30) NOT NULL,
   email varchar(30) NOT NULL,
   city varchar(30) NOT NULL,
   phone varchar(30) NOT NULL,
   password varchar(30) NOT NULL,
   crystal bigint(255) DEFAULT '0' NOT NULL,
   metal bigint(255) DEFAULT '0' NOT NULL,
   energy bigint(255) DEFAULT '0' NOT NULL,
   r_energy int(11) DEFAULT '0' NOT NULL,
   c_energy int(11) DEFAULT '0' NOT NULL,
   sats int(11) DEFAULT '0' NOT NULL,
   infinitys int(11) DEFAULT '0' NOT NULL,   
   wraiths int(11) DEFAULT '0' NOT NULL,
   warfrigs int(11) DEFAULT '0' NOT NULL,
   destroyers int(11) DEFAULT '0' NOT NULL,
   scorpions int(11) DEFAULT '0' NOT NULL,
   astropods int(11) DEFAULT '0' NOT NULL,
   cobras int(11) DEFAULT '0' NOT NULL,
   infinitys_base int(11) DEFAULT '0' NOT NULL,   
   wraiths_base int(11) DEFAULT '0' NOT NULL,
   warfrigs_base int(11) DEFAULT '0' NOT NULL,
   destroyers_base int(11) DEFAULT '0' NOT NULL,
   scorpions_base int(11) DEFAULT '0' NOT NULL,
   astropods_base int(11) DEFAULT '0' NOT NULL,
   cobras_base int(11) DEFAULT '0' NOT NULL,
   p_scorpions int(11) DEFAULT '0' NOT NULL,
   p_scorpionst int(11) DEFAULT '0' NOT NULL,
   p_cobras int(11) DEFAULT '0' NOT NULL,
   p_cobrast int(11) DEFAULT '0' NOT NULL,
   missiles int(11) DEFAULT '0' NOT NULL,
   score BIGINT(255) DEFAULT '0' NOT NULL,
   asteroids int(11) DEFAULT '0' NOT NULL,
   asteroid_crystal int(11) DEFAULT '0' NOT NULL,
   asteroid_metal int(11) DEFAULT '0' NOT NULL,
   ui_roids int(11) DEFAULT '0' NOT NULL,
   war int(11) DEFAULT '0' NOT NULL,
   def int(11) DEFAULT '0' NOT NULL,
   wareta int(11) DEFAULT '0' NOT NULL,
   defeta int(11) DEFAULT '0' NOT NULL,
   c_crystal int(10) DEFAULT '0' NOT NULL,
   c_metal int(10) DEFAULT '0' NOT NULL,
   c_airport int(11) DEFAULT '0' NOT NULL,
   c_abase int(10) DEFAULT '0' NOT NULL,
   c_wstation int(10) DEFAULT '0' NOT NULL,
   c_amp1 int(10) DEFAULT '0' NOT NULL,
   c_amp2 int(10) DEFAULT '0' NOT NULL,
   c_warfactory int(10) DEFAULT '0' NOT NULL,
   c_destfact int(11) DEFAULT '0' NOT NULL,
   c_scorpfact int(11) DEFAULT '0' NOT NULL,
   r_imcrystal int(10) DEFAULT '0' NOT NULL,
   r_immetal int(10) DEFAULT '0' NOT NULL,
   r_iafs int(10) DEFAULT '0' NOT NULL,
   r_aaircraft int(11) DEFAULT '0' NOT NULL,
   r_tbeam int(11) DEFAULT '0' NOT NULL,
   r_uscan int(11) DEFAULT '0' NOT NULL,
   r_oscan int(11) DEFAULT '0' NOT NULL,
   p_infinitys int(11) DEFAULT '0' NOT NULL,
   p_infinityst int(11) DEFAULT '0' NOT NULL,
   p_wraiths int(11) DEFAULT '0' NOT NULL,
   p_wraithst int(11) DEFAULT '0' NOT NULL,
   p_warfrigs int(11) DEFAULT '0' NOT NULL,
   p_warfrigst int(11) DEFAULT '0' NOT NULL,
   p_destroyers int(11) DEFAULT '0' NOT NULL,
   p_destroyerst int(11) DEFAULT '0' NOT NULL,
   p_missiles int(11) DEFAULT '0' NOT NULL,
   p_missilest int(11) DEFAULT '0' NOT NULL,
   timer int(15) DEFAULT '0' NOT NULL,
   size int(15),
   p_astropods int(11) DEFAULT '0' NOT NULL,
   p_astropodst int(11) DEFAULT '0' NOT NULL,
   tag varchar(10) NOT NULL,
   rank int(11) DEFAULT '0' NOT NULL,
   rcannons int(11) DEFAULT '0' NOT NULL,
   p_rcannons int(11) DEFAULT '0' NOT NULL,
   p_rcannonst int(11) DEFAULT '0' NOT NULL,
   avengers int(11) DEFAULT '0' NOT NULL,
   p_avengers int(11) DEFAULT '0' NOT NULL,
   p_avengerst int(11) DEFAULT '0' NOT NULL,
   lstalkers int(11) DEFAULT '0' NOT NULL,
   p_lstalkers int(11) DEFAULT '0' NOT NULL,
   p_lstalkerst int(11) DEFAULT '0' NOT NULL,
   r_odg int(11) DEFAULT '0' NOT NULL,
   c_odg int(11) DEFAULT '0' NOT NULL,
   sleep int(11) DEFAULT '0' NOT NULL,
   lastsleep int(11) DEFAULT '0' NOT NULL,
   closed tinyint(4) DEFAULT '0' NOT NULL,
   x int(11) DEFAULT '1' NOT NULL,
   y int(11) DEFAULT '1' NOT NULL,
   commander int(11) DEFAULT '0'  NOT NULL,
   galname varchar(30) DEFAULT 'No name' NOT NULL,
   galpic varchar(150) DEFAULT '125x125earthdoom.gif'   NOT NULL,
   motd varchar(60)  NOT NULL,
   vote varchar(30)  NOT NULL,
   farms int(11) DEFAULT '1' NOT NULL,
   civilians int(11) DEFAULT '1000' NOT NULL,
   tax int(11) DEFAULT '20' NOT NULL,
   credits int(11) DEFAULT '5000' NOT NULL,
   morale int(11) DEFAULT '100' NOT NULL,
   newbie int(11) DEFAULT '100' NOT NULL)");

echo "usertable created";

$result = mysql_query("CREATE TABLE pa_news (
   id int(11) DEFAULT '0' NOT NULL,
   time int(15) DEFAULT '0' NOT NULL,
   news text NOT NULL,
   seen varchar(10) NOT NULL,
   header varchar(40) NOT NULL)");

echo "newstable created";

$result = mysql_query("CREATE TABLE pa_logging (
   id int(11) NOT NULL auto_increment,
   subject tinytext NOT NULL,
   text text NOT NULL,
   author int(11) DEFAULT '0' NOT NULL,
   stamp int(11) DEFAULT '0' NOT NULL,
   toid int(11) DEFAULT '0' NOT NULL,
   type tinytext NOT NULL,
   ip varchar(30) NOT NULL,
   PRIMARY KEY (id),
   UNIQUE id (id),
   KEY id_2 (id))");

echo "logging table created";

$result = mysql_query("CREATE TABLE pa_tags (
   id int(11) NOT NULL auto_increment,
   tag varchar(10) NOT NULL,
leader varchar(30) NOT NULL,
   password tinytext NOT NULL,
   PRIMARY KEY (id),
   UNIQUE id (id),
   KEY id_2 (id))");

echo "tags table created";

$result = mysql_query("CREATE TABLE pa_mail (
   id int(11) DEFAULT '0' NOT NULL,
   time int(15) DEFAULT '0' NOT NULL,
   news text NOT NULL,
   seen varchar(10) NOT NULL,
   header varchar(40) NOT NULL)");

echo "mailtable created";

$result = mysql_query("CREATE TABLE pa_galaxies (
   id int(11) NOT NULL auto_increment,
   time int(15) DEFAULT '0' NOT NULL,
   tekst text NOT NULL,
   x int(11) DEFAULT '1' NOT NULL,
   creator varchar(40) NOT NULL,
   threadid varchar(40) NOT NULL,
PRIMARY KEY (id),
   UNIQUE id (id),
   header varchar(40) NOT NULL)");

echo "forum table created";

$result = mysql_query("CREATE TABLE pa_alliance (
   id int(11) NOT NULL auto_increment,
   time int(15) DEFAULT '0' NOT NULL,
   tekst text NOT NULL,
   x int(11) DEFAULT '1' NOT NULL,
   creator varchar(40) NOT NULL,
   threadid varchar(40) NOT NULL,
PRIMARY KEY (id),
   UNIQUE id (id),
   header varchar(40) NOT NULL)");

echo "alliance forum table created";



echo mysql_error();

?>
