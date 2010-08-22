<?php

require_once('inc.config.php');
logincheck();

$q = PSQ("SELECT * FROM $TABLE[old_intel] WHERE uid='$UID';");

_header();

?><center>
<table border=0 cellpadding=2 cellspacing=0 width=100%>
<tr><td class=header><center><b>Old Intelligence</b></td></tr>
</table>
<br>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?

$b=0;
while ($a = mysql_fetch_assoc($q))
{
	$b++;
	echo "<tr OnClick=\"a=document.getElementById('$b').style; if (a.display=='') a.display='none'; else a.display='';\"><td><table style='cursor:pointer;' border=0 cellpadding=3 cellspacing=0 width=100%><tr><td width=1>&nbsp;$b&nbsp;</td><td width=25%>".$a['soort']."</td><td width=40%><b>".$userarray[$a['target']]." of ".$planetarray[$a['target']]."</td><td>".date("d-m-Y H:i:s",$a['tijd'])."</td></tr></table></td></tr>\n";
	echo "<tr bgcolor=#111111 style='display:".((isset($_GET['uitgeklapt']) && $_GET['uitgeklapt']==$a['id'])?"":"none").";' id='".$b."'><td style='border:solid 0px red;'><center>".stripslashes($a['result'])."<br></td></tr>\n";
}
if ($b==0)
	echo "<tr><td><center><i>No Old Intelligence...</td></tr>\n";

?>
</table>
<br>
<?

_footer();

?>