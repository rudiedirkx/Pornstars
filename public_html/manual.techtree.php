<?php

require_once('inc.config.php');

function makeRDcell( $rd, $extra = '', $w = 0 ) {
	$html = '';
	$html .= '<td '.( 0 < $w ? 'width="'.$w.'%" ' : '' ).''.$extra.' class="t'.$rd['T'].'">';
	$html .= '<div onclick="goto('.$rd['id'].');" class="t">'.$rd['id'].'. '.$rd['name'].'</div>';
	$arrExcludesRD = db_select_fields('d_r_d_excludes e, d_r_d_available a', 'a.id,concat(\'<a class="a\',a.T,\'" href="#" onclick="return goto(\',a.id,\');">\',a.name,\'</a>\')', 'a.id = e.r_d_excludes_id AND e.r_d_id = '.$rd['id']);
	$arrCosts = db_select_fields('d_r_d_costs c, d_resources r', 'r.id,concat(\'<span title="\',r.resource,\'" style="color:\',r.color,\';">\',c.amount,\'</span>\')', 'c.resource_id = r.id AND 0 < c.amount AND c.r_d_id = '.$rd['id']);
	$html .= '<div style="display:none;">'.$rd['explanation'].'<br />Costs: '.implode(', ', $arrCosts).'<br />Excludes: '.implode(', ', $arrExcludesRD).'</div>';
	$html .= '</td>'."\n";
	return $html;
}

function makeRDline( $rds, $extra = '' ) {
	$w = round(100/count($rds));
	$html = '';
	$html .= '<table border="0" cellspacing="5" width="100%"><tr>'."\n";
	foreach ( $rds AS $rd ) {
		$html .= makeRDcell($rd, $extra, $w);
	}
	$html .= '</tr></table>'."\n";
	return $html;
}

?>
<html>

<head>
<title>Tech Tree</title>
<style type="text/css">
* { margin:0; padding:0; }
body { padding:5px; }
td { border:solid 1px #888; padding:15px 6px; font-size:12px; cursor:default; }
td div.t { font-weight:bold; text-align:center; font-size:15px; cursor:pointer; }
.tr { background-color:#ffb0b0; }
.td { background-color:lightgreen; }
td div a, tr div span { background-color:black; color:white; font-weight:bold; padding:1px; }
td div a.ar { color:#ffb0b0; }
td div a.ad { color:lightgreen; }
</style>
<script type="text/javascript">
<!--//
var goto = function(id) {
	document.location = "?id="+id;
	return false;
}
//-->
</script>
</head>

<body>
<?php

if ( isset($_GET['id']) && ($arrRD = db_select('d_r_d_available', 'id = '.(int)$_GET['id'])) ) {
	$arrRD = $arrRD[0];

	$arrRequiresRD = db_select('d_r_d_requires r, d_r_d_available a', 'a.id = r.r_d_requires_id AND r.r_d_id = '.$arrRD['id']);
//$arrRequiresRD = array($arrRD, $arrRD);
	if ( $arrRequiresRD ) {
		echo makeRDline($arrRequiresRD);
	}

	echo makeRDline(array($arrRD), 'style="border:solid 3px black;"');

	echo '<table border="0" cellspacing="5" width="100%"><tr>'."\n";
	$arrEnablesRD = db_select('d_r_d_requires r, d_r_d_available a', 'a.id = r.r_d_id AND r.r_d_requires_id = '.$arrRD['id']);
//unset($arrEnablesRD[1]);
	$reqs = array();
	$w = round(100/count($arrEnablesRD));
	foreach ( $arrEnablesRD AS $k => $rd ) {
		$req = db_select('d_r_d_requires r, d_r_d_available a', 'a.id = r.r_d_requires_id AND r.r_d_id = '.$rd['id'].' AND a.id <> '.$arrRD['id']);
		echo makeRDcell($rd, 'colspan="'.count($req).'"', $w);
		$reqs[$k] = $req;
	}
	echo '</tr><tr>';
	foreach ( $reqs AS $reqss ) {
		if ( empty($reqss) ) {
			echo '<td></td>';
		}
		else {
			foreach ( $reqss AS $rd ) {
				echo makeRDcell($rd, 'style="opacity:0.6;"');
			}
		}
	}
	echo '</tr></table>'."\n";
}
else {
	$arrStarters = db_fetch('select * from d_r_d_available WHERE id NOT IN (select r_d_id from d_r_d_requires);');
	echo makeRDline($arrStarters);
}

?>
<script type="text/javascript">
<!--//
var a=document.getElementsByTagName('td'), b=a.length;
while ( b-- ) {
	if ( a[b].className ) {
		a[b].onmouseover = function() { this.getElementsByTagName('div')[1].style.display=''; }
		a[b].onmouseout = function() { this.getElementsByTagName('div')[1].style.display='none'; }
	}
}
//-->
</script>

</body></html>
<?php

exit;

?>
<html>

<head>
<title>Manual > TechTree</title>
<style type="text/css">
body, table {
	font-family		: verdana;
	font-size		: 11px;
}
.techtree td {
	width			: 90px;
	height			: 35px;
	text-align		: left;
	cursor			: default;
	color			: black;
}
.techtree td.header {
	background-color: midnightblue;
	color			: white;
	text-align		: center;
	font-weight		: bold;
	vertical-align	: middle;
}
.techtree td.research {
	cursor			: pointer;
	background-color: #ffb0b0;
}
.techtree td.development {
	cursor			: pointer;
	background-color: lightgreen;
}
</style>
</head>

<body bgcolor="black">
<?php

$arrTable = unserialize(file_get_contents('tests/arrTable.tbl'));

$arrRD = db_select_by_field('d_r_d_available','id');

$arrReq = db_select('d_r_d_requires r', 'r_d_id IN (SELECT r_d_id FROM d_r_d_requires GROUP BY r_d_id HAVING COUNT(1) >= 2)');
$arrRequires = array();
foreach ( $arrReq AS $rd ) {
	$arrRequires[$rd['r_d_id']][] = $arrRD[$rd['r_d_requires_id']]['name'];
}
//echo '<pre>'; print_r($arrRequires);

$arrExcl = db_select('d_r_d_excludes');
$arrExcludes = array();
foreach ( $arrExcl AS $rd ) {
	$arrExcludes[$rd['r_d_id']][] = $arrRD[$rd['r_d_excludes_id']]['name'];
}

?>
<table id="techtree" class="techtree" border="0" cellpadding="3" cellspacing="1" width="100%">
<tbody>
<?php

foreach ( $arrTable AS $arrRow ) {
	echo '<tr valign="top">'."\n";
	foreach ( $arrRow AS $arrCell ) {
		$szColspan = !empty($arrCell['colspan']) && 1 < (int)$arrCell['colspan'] ? ' colspan="'.(int)$arrCell['colspan'].'"' : '';
		// header
		if ( !empty($arrCell['class']) && 'header' == $arrCell['class'] ) {
			echo '<td'.$szColspan.' class="header">'.$arrCell['content'].'</td>';
		}
		// R&D
		else if ( !empty($arrCell['class']) ) {
			$rd = $arrRD[$arrCell['content']];
			$szRequires = '';
			if ( isset($arrRequires[$rd['id']]) ) {
				$req = $arrRequires[$rd['id']];
				$r = array_pop($req);
				$szRequires = '<br />(<u style="font-weight:bold;">requires</u> ' . ( count($req) ? '<i>`'.implode('`</i>, <i>`', $req) . '`</i> <b>and</b> ' : '' ) . '<i>`' . $r . '`</i>';
//				$szRequires = '<br />(<u>requires</u> <i>`'.implode('`</i>, <i>`', $req) . '`</i> <b>and</b> <i>`' . $r . '`</i>';
			}
			$szExcludes = '';
			if ( isset($arrExcludes[$rd['id']]) ) {
				$exc = $arrExcludes[$rd['id']];
				$r = array_pop($exc);
				$szExcludes = '<br />(<u style="font-weight:bold;color:red;">excludes</u> ' . ( count($exc) ? '<i>`'.implode('`</i>, <i>`', $exc) . '`</i> <b>and</b> ' : '' ) . '<i>`' . $r . '`</i>';
			}
			$arrEnables = array();
			if ( $u=db_select_fields('d_all_units', 'id,unit', 'r_d_required_id = '.(int)$rd['id']) && 0 < count($u) ) {
var_dump($u);
				$ena = $u;	
				$r = array_pop($ena);
				$arrEnables[] = 'Enables ' . ( count($ena) ? '`'.implode('`, `', $ena) . '` and ' : '' ) . '`' . $r . '`';
			}
echo db_error();
			if ( $u=db_select_fields('d_r_d_results', 'id,explanation', 'done_r_d_id = '.(int)$rd['id']) && 0 < count($u) ) {
				$arrEnables[] = implode("\n", $u);
			}
			$szEnables = count($arrEnables) ? ' title="'.implode("\n", $arrEnables).'"' : '';
			$szContent = '<b'.( !empty($arrExcludes[$rd['id']]) || ( isset($arrRequires[$rd['id']]) && 1 < count($arrRequires[$rd['id']]) ) ? ' style="text-decoration:underline;"' : '' ).'>'.$rd['name'].( $szExcludes ? '&nbsp;<b style="color:red;">!!</b>' : '' ).'</b><div style="display:'.( empty($bShowExplanation) ? 'none' : '' ).';">'.$rd['id'].'. '.$rd['explanation'].$szExcludes.$szRequires.'</div>';
			$szClass = ' class="'.$arrCell['class'].'"';
			$szOnClick = ' onclick="var d=this.getElementsByTagName(\'div\')[0].style;d.display=\'none\'!=d.display?\'none\':\'\';"';
			echo '<td'.$szEnables.$szColspan.$szOnClick.' class="'.$arrCell['class'].'">'.$szContent.'</td>';
		}
		// empty
		else {
//			$szContent = '';
//			$szClass = '';
//			$szOnClick = '';
			echo '<td'.$szColspan.'></td>';
		}
//		echo '<td '.$szClass.$szColspan.$szOnClick.'>'.$szContent.'</td>'."\n";
	}
	echo '</tr>'."\n";
}

$arrNumbers = db_select_fields('d_r_d_available', 'lower(T),count(1)', '1 group by T');

?>
</tbody>
</table>

<br />

<table class="techtree" border="0" cellpadding="5" cellspacing="2" width="100%" style="color:black;">
<tr>
	<td class="development" width="50%" style="text-align:center;cursor:default;height:55px;"><b style="font-size:13px;">DEVELOPMENTS (<?php echo $arrNumbers['d']; ?>)</b></td>
	<td class="research" width="50%" style="text-align:center;cursor:default;height:55px;"><b style="font-size:13px;">RESEARCHES (<?php echo $arrNumbers['r']; ?>)</b><!--<br /><u>Research</u> -> you have to give up something for this -><br>eg.: "(<b><i><font color=red>excludes Fleet Signatures</font></i></b>)"--></td>
</tr>
</table>
</body>

</html>
