<!-- ENDS PAGE CONTENT -->
<!-- STARTS FOOTER -->

<?php if ( isset($titlearray[$st]) ) { $st = $titlearray[$st]; } if ( !isset($stF) || $stF != "overview" ) { ?>
<br />
<table border="0" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse;background-color:#383838;">
<tr><td style="border-bottom:solid 1px #555;" align="center"><b>INFORMATION -- <?php echo strtoupper(str_replace('_', ' ', $st)); ?></b></td></tr>
<tr><td style="padding:4px;" align="center">
<?php

global $NUM_OUTGOING_FLEETS, $FLEETNAMES;

switch ( $stF ) {
	case 'galaxynews':
		echo "In the Galaxynews you can see all incoming fleets, both friendly and hostile!<br />Green lines stand for friendly troops, red lines for hostiles.<br />It's possible that 2 lines come from or go to one planet.<br />Hostiles and friendlies are firstly ordered by purpose (hostile or friendly) and than by target.";
	break;

	case 'communication':
		echo "The communication area is to send messages of unlimited length to other planets, both in your own galaxy and outside. General topics can better be discussed in the galaxyforum/politics, where all planets have a seat.";
	break;

	case 'galaxyforums':
		echo "The galaxyforum is a great place to campaign for GC or listen to those campaigns. Its also very useful for planning attacks and discussing the role of a ruler and its planet in the galaxy.";
	break;

	case 'journal':
		echo "Your personal planetnotes.<br />Old scans, passwords, coordinates, old mail, old news, etc. Anything you want to save or store, you can store here. Nobody can read it, but you. Only text!";
	break;

	case 'production':
		echo "In the production area you can build all units you have a factory for. The upper units are ships, the last few are PDU. You can also see the amount of ships you already have, and the amount of ships being built.<br /> Click on the shipname for a brief explanation! If you need all info on all ships, goto ShipStatistics in the Manual.";
	break;

	case 'research':
		echo "All researches you have completed or can be researched or are being researched now. You can toggle the view of the list by clicking Toggle View, this will also affect the view of the Construction area. If you want to see whats coming after a research thats being done, goto the TechTree in the Manual.<br />Click on the name of the research for a brief explanation!";
	break;

	case 'construction':
		echo "The list of constructions, with all the info you need about it. You can toggle the view of the list by clicking Toggle View, this will also affect the view of the Research area. If you want to see whats coming after a construction thats being done, goto the TechTree in the Manual.<br />Click on the name of the construction for a brief explanation!";
	break;

	case 'resources':
		echo "A smooth overview of your asteroids and what they produce for you per tick. You can also donate resources if you want, but only to planets in your galaxy or members of your Alliance. You can also initiate unactive roids here. This page is very very important in the early stage of the game, when you build, find and initiate your own roids.";
	break;

	case 'creonogy':
		echo "Energy is a very powerful and important resource. Especially in the early stage of the game. Build powerplantscells to gain more energy per tick. One Cell creates 40 Energy, a Box makes 70! The bigger your fleet gets, the more Energy you will need to send it all away...<br /><b style='color:red;'>&gt;&gt;</b> Note that your Creons will decrease over time!!! You can have 1000 cells and 1000 boxes without losing any! <b style='color:red;'>&lt;&lt;</b>";
	break;

	case 'galaxy':
		echo "This shows all galaxies you want to see. Scroll through them by clicking the arrows. If galaxy X4 is empty, so will be X5 and on. Some galaxies might have less planets in them, or have an unfilled Y-coord. This area is perfect for finding your targets. Note that galaxies are very personal, with their own picture and name. Its useful to know what the colours are for. See the explanation above. Note: X0 is the first galaxy!";
	break;

	case 'politics':
		echo "This is where you vote for your Galaxy Commander. You can talk about those matters in the Galaxy Forums or send eachother personal notes through the Mail. When you have been elected for a function, be proud of it and forfil it with precision. A GC, MoC or MoW does not earn extra resources, they are important in peace negotiations and planning wars though. Every function has its own toys.";
	break;

	case 'ranking.planet':
		echo "The rankings are updated every tick. The list is ordered by score and contains the 100 planets with the highest scores. This page is very useful for selecting your target, because you can see the score/size ratio. The size of a planet is measured by the number of roids it has, both initiated and not initiated. The rankings are also useful to see the biggest/strongest Alliances.<br />To see how your score is calculated, check the manual!";
	break;

	case 'ranking.galaxy':
		echo "The rankings are updated every tick. The list is ordered by score and contains all galaxies in the universe. The ranking of galaxies is very unpersonal, no tags, no planets; only total scores and sizes of the galaxy.";
	break;

	case 'ranking.alliance':
		echo "The rankings are updated every tick. The list is ordered by score and contains all registered alliances/tags. You could say that the most powerful alliance is ranked first. You can order on total score and on average score.";
	break;

	case 'waves':
		echo "Ordering waves is important all through the game. First you need a lot of asteroidscans, through time you will need more intelligence scans. When you have made yourself a steady income, you  might consider Amplifiers and Blockers too. The WavesOrder area is divided in three sections: asteroidscans, intelscans, enhancers.";
	break;

	case 'military':
		echo "Military is the keyword in this game. That might make this the most important area. You have an overview of your units, stationed in fleet `".$FLEETNAMES[0]."` and ".($NUM_OUTGOING_FLEETS)." mobile fleets, you can transfer units from one to another and ofcourse you can send fleets out.<br />Remember: You always need Energy to send your fleet. How much depends on the amount of units, what they consume per tick, and the ETA. If you recall, you lose your Energy.";
	break;

	case 'preferences':
		echo "<b>Change your password regularly!</b><br />Preferences are not very important to the game, but they ARE for your protection and comfort! You can set some things up here. A few options still might not work :)";
	break;

	case 'alliance':
		echo "<b>Be careful with your Alliance Password</b><br />You can start a new alliance here, or join an existing one. An Alliance is like a Galaxy, only closer and not random. Team up with a few of your mates and kick the Universe's arse!<br /><b>&#183;&#183;&#183;&#183;&#183; When a new member joins, the password automatically changes! &#183;&#183;&#183;&#183;&#183;</b>";
	break;

#	case 'old_intel':
#		echo "You can manually and automatically save scans. The costs: <font color=".$showcolors['metal'].">".$scansavecosts['metal']." metal</font>, <font color=".$showcolors['crystal'].">".$scansavecosts['crystal']." crystal</font>, <font color=".$showcolors['energy'].">".$scansavecosts['energy']." energy</font>.<br />You can turn automatically on in the Preferences section. Scans are ordered by time&date, not type or target.";
#	break;

	default:
		echo "There is no Info-section for this page. Probably speaks for itself!";
	break;
}

unset($_SESSION['ps_msg']);

?><br />-- For more info, there is always <a href="manual.php">the Manual</a> --</td>
</tr></table>
<?php } ?>

<table border="0" cellpadding="8" cellspacing="0" width="100%">
<tr valign="middle"><td align="center">
<?php

$iUsersOnline = db_count( 'planets', 'lastaction > '.(time()-180) );
$iAccounts = db_count('planets');

global $g_iQueries, $g_arrQueries, $iUtcStartTime;

$fParseTime = number_format( microtime(true)-$iUtcStartTime, ($x=4) );
echo '<div>[ <span style="color:#3388dd;">Loaded in '.$fParseTime.' sec</span> ] <!--| [ <span style="color:#3388dd;">'.((int)$g_iQueries+1).' queries used</span> ] -->| [ <span style="color:#3388dd;">'.$iUsersOnline.' / '.$iAccounts.' accounts online</span> ]</div>';

?>
</td></tr>
</table>

</div>

<!-- ENDS FOOTER -->
</body>

</html>