<?php

require_once('../inc.connect.php');

?>
<html>

<head>
<title>TechTree V2</title>
<style type="text/css">
table {
	border			: solid 1px #555;
	border-width	: 0 0 1px 1px;
}
td {
	padding			: 0 4px;
}
</style>
</head>

<body>

<?php echo getChilds(); ?>

</body>

</html>
<?php

function getChilds( $f_iParent = null, &$arrDone = array() ) {
	$szHtml = '';
	if ( is_null($f_iParent) ) {
		$RD = db_select('r_d_available a','0=(SELECT COUNT(1) FROM r_d_requires WHERE r_d_id=a.id)');
	}
	else {
		$RD = db_select( 'r_d_available a, r_d_requires r','a.id = r.r_d_id AND r.r_d_requires_id = '.(int)$f_iParent.' AND a.id NOT IN(\''.implode("','", $arrDone).'\')' );
	}
	if ( count($RD) )
	{
		$szHtml .= '<table border="0" cellpadding="0" cellspacing="0">';
		foreach ( $RD AS $rd ) {
			$arrDone[$rd['id']] = $rd['id'];
			$szHtml .= '<tr valign="top"><td><b>'.$rd['name'].'</b></td>';
			$c = getChilds($rd['id'], $arrDone);
			if ( $c ) {
				$szHtml .= '<td>'.$c.'</td>';
			}
			$szHtml .= '</tr>';
		}
		$szHtml .= '</table>';
	}
	return $szHtml;
}

function db_select($szTable,$szWhere='') {
	return db_fetch('SELECT * FROM '.$szTable.''.($szWhere?' WHERE '.$szWhere:'').';');
}
function db_fetch($szQuery) {
	$q = mysql_query($szQuery) or die(mysql_error());
	$a = array();
	while ( $r = mysql_fetch_assoc($q) ) {
		$a[] = $r;
	}
	return $a;
}

?>