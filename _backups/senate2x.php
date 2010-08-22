<?php 
require "dblogon.php";
require "options.php";
require "header.php";



$gal=mysql_query("select count(nick) as peeps from pa_users where x='$x' and y='$y' ");
$galp=mysql_fetch_object($gal);
$vot=round(($galp->peeps/3+0.5),0);

if (($choosemowgq != "") && ($choosemowgq != "None")) { mysql_query("update pa_users set commander='4' where x='$x' and y='$y' and commander='2'"); 
mysql_query("update pa_users set commander='2' where x='$x' and y='$y' and nick='$choosemowgq'"); } 

if (($choosemocgq != "") && ($choosemocgq != "None")) { mysql_query("update pa_users set commander='0' where x='$x' and y='$y' and commander='3'"); 
mysql_query("update pa_users set commander='3' where x='$x' and y='$y' and nick='$choosemocgq'"); } 

if ($chgalpicta == "galpic") { mysql_query("update pa_galaxy set plaatje='$cgalpicta' where x='$x' and y='$y'"); }
if ($chgalnaam == "galnaam") { mysql_query("update pa_galaxy set naam='$cgalnaampie' where x='$x' and y='$y'"); }
if ($galcommandermsg == "msg") { mysql_query("update pa_galaxy set cmdmsg='$commsg' where x='$x' and y='$y'"); }

if (($voted != "") && ($voted != "none" )) { 
$voterold=mysql_query("select vote,count(*) as aantal from pa_users where x='$x' and y='$y' group by vote order by aantal desc LIMIT 1");
$voteqold=mysql_fetch_object($voterold);
mysql_query("update pa_users set vote='$voted' where nick='$Username'");
$voteq=mysql_query("select vote,count(*) as aantal from pa_users where x='$x' and y='$y' and vote<>'' group by vote order by aantal desc LIMIT 1");
$voter=mysql_fetch_object($voteq);

if ($voter->aantal >= $vot) { 
if ($voter->vote != $voteqold->vote) { mysql_query("update pa_users set commander='0' where x='$x' and y='$y'"); }
mysql_query("update pa_users set commander='1' where nick='$voter->vote'"); }
else { mysql_query("update pa_users set commander='0' where x='$x' and y='$y'"); }
 } 

$query=mysql_query("select * from pa_users where x='$x' and y='$y' order by y");
echo("<center><table border='1' width='62%'>");
echo("<tr><td width='4%'>X</td><td width='4%'>Y</td><td width='4%'>Z</td><td width='30%'>Nick</td><td width='30%'>Voted on</td><td width='10%'>Votes</td></tr>");
while ($record=mysql_fetch_object($query)) { 

$vote=mysql_query("select vote,count(vote) as aantal from pa_users where vote='$record->nick' group by vote");
$votes=mysql_fetch_object($vote);
if ($record->commander != 0) { 
if ($record->commander == 1){ $kleur="<font color=blue>"; $commander=$record->nick; }
if ($record->commander == 2){ $kleur="<font color=red>"; $mow=$record->nick; }
if ($record->commander == 3){ $kleur="<font color=green>"; $moc=$record->nick; }
}

echo("<tr><td width='4%'>$record->x</td><td width='4%'>$record->y</td><td width='4%'>$record->z</td><td width='30%'>$kleur$record->nick</td><td width='30%'>$record->vote&nbsp;</td><td width='10%'>$votes->aantal&nbsp;</td></tr>"); 
$kleur="<font color=white>";
}

echo("</table>"); 

$query2=mysql_query("select * from pa_users where x='$x' and y='$y' order by y");
echo("<form method='post' action=vote.php>");echo("<br><select size='1' name='voted'>");
echo("<option value='none'>Choose a commander</option>");
while ($rec=mysql_fetch_object($query2)) {
echo("<option value='$rec->nick'>$rec->nick</option>");		} echo(" </select> "); ?>
<input type="Submit" name="submit" value="Vote"></from>


<?php 

$gal=mysql_query("select count(nick) as peeps from pa_users where x='$x' and y='$y' ");
$galp=mysql_fetch_object($gal);
$vot=round(($galp->peeps/2+0.5),0);

echo("<br><br>There are $galp->peeps users in this galaxy");
echo("<br>You need $vot votes to get commander<br>");

if ($commander != "") { echo("<br>Your commander is <font color=blue>$commander<font color=white>"); 
if ($mow != "") { echo("<br>Your minister of war is <font color=red>$mow<font color=white>"); }
if ($moc != "") { echo("<br>Your minister of cominucation is <font color=green>$moc<font color=white>"); } }

else { echo("<br>There is no commander in this galaxy"); }

if ($commander == $Username) { 
echo("<br><br><hr><br><br>You are the commander<br>");
$galpas=mysql_query("select * from pa_galaxy where x='$x' and y='$y'");
$galpass=mysql_fetch_object($galpas); 
echo("<br><br>The galaxy password is: $galpass->galpassword <br>");
echo("<br><br>Choose a Minister Of War<br>");
$query2=mysql_query("select * from pa_users where x='$x' and y='$y' and commander<>'1' order by z");
echo("<form method='post' action=senate.php>");echo("<br><select size='1' name='choosemowgq'>");
echo("<option value='None'>Choose a MoW</option>");
while ($rec=mysql_fetch_object($query2)) {
echo("<option value='$rec->nick'>$rec->nick</option>");		} echo(" </select> "); 
echo("<input type='Submit' name='submit' value='Choose'></from><br>");


$info=mysql_query("select * from pa_galaxy where x='$x' and y='$y'");
$infor=mysql_fetch_object($info);

} else { 

 }
require "footer.php";
?>
