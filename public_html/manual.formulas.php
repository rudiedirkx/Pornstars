<?php

require 'inc.bootstrap.php';

?>

<b>Basic Values</b><br>
<br>
First a few values, just always handy to know:<br>
- Creoncells and -boxes are always the same price and produce always the same amount of energy;<br>
- The production of one Asteroid decreases as the amount of roids of one type increases (OR: the more Asteroids you have, the less they make PER roid);<br>
- A planet being roided loses max 13.5% of it's Asteroids per tick. The more attackers, the less every one of them will get. A planet loses in 5 ticks maximally 51.57% of its roids (max attackers, max ticks);<br>
<br>
<br>
Formulas<br>
<br>
<table border=1 cellpadding=5 cellspacing=0>
<tr><td width=1%><b>NAME</td><td><b>FORMULA</td><td><b>EXPLANATION</td></tr>
<tr valign=top><td><b>Your&nbsp;Score</td>
<td valign=top><pre style='font-size:9pt;'>
score = sum ([resources]) / 500
      + sum (costs [all_units]) / 100
      + sum ([initiated_roids]) * 150;

resources = metal
          + crystal
          + energy;

all_units = owned waves
          + owned defence
          + owned ships
          + owned energizers;

initiated_roids = metal_roids
                + crystal_roids
                + energy_roids;
</td>
<td>Only units you still have count in score. Waves don't, only Amps and Blockers.</td></tr>

<tr valign=top><td><b>Income</b><br>(metal&crystal) (from Asteroids)</td>
<td valign=top><pre style='font-size:9pt;'>
if (num_res_roids &lt; 42)
   res_per_type = round_to_top ((351-num_res_roids)*num_res_roids)
if (num_res_roids &gt; 41)
   res_per_type = round_to_top (2000*square_root(num_res_roids))
</td>
<td>Without income from Planet Mining</td></tr>

<tr valign=top><td><b>Intelligence Scan</td>
<td valign=top><pre style='font-size:9pt;'>
chance = 30 * (1 + waveamps/size - waveblockers_target/size_target)
random_val = random (0, 100)
if (random_val < chance)
{
   reached = TRUE
   if (random_val+5*scan_factor > chance)
      noticed = TRUE
}
else
{
   blocked = TRUE
   noticed = TRUE
}
</td>
<td>The more Asteroids you have, the smaller the chance of getting through.<br><pre style='font-size:9pt;'>
 scan_factor
sectorscan 2
  unitscan 4
   pduscan 5
 fleetscan 7
  newsscan 8
</td></tr>

<tr valign=top><td><b>Asteroid Scan</td>
<td valign=top><pre style='font-size:9pt;'>
rand = random (0, size)
calc = 50 * (1+sqrt(waveamps)) / size
if (rand < calc)
   return "You found one Asteroid"

NOTES:
 This will loop for with as many scans you scan!
 Check "Manual - Asteroidscanning" for a list of numbers on Asteroidscanning (roids, amps, etc)
</td>
<td>As you can calculate, you have a 100% chance of getting a roid per scan the first 8 roids. After that, you'll need luck, more roidscans and waveamps!</td></tr>

<tr valign=top><td><b>Planetary Traffic</td>
<td valign=top><pre style='font-size:9pt;'>
if (attackers_around_planet >= 3)
   return "You cannot attack anymore"
</td>
<td>No more than 3 attackers. No limit for number of defenders</td></tr>

<tr valign=top><td><b>Asteroid Initiating Costs</td>
<td valign=top><pre style='font-size:9pt;'>
init_costs = (initiated_asteroids)*110
</td>
<td>For ONE Asteroid. For the next one: all over</td></tr>
</table>
<br>


