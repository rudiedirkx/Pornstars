<?php

require_once('../inc.connect.php');

if ( isset($_POST['table']) ) {
	fwrite(($fp=fopen('szTable.tbl','w')), trim(base64_decode(str_replace(' ','+',$_POST['table']))));
	fclose($fp);
	exit;
}
else if ( isset($_POST['phptable']) ) {
	$arrTable = array();
	$r = 0;
	$arrRows = explode('|', $_POST['phptable']);
	foreach ( $arrRows AS $szRow ) {
		$arrCells = explode(':', $szRow);
		$arrTable[$r] = array();
		$c = 0;
		foreach ( $arrCells AS $szCell ) {
			$x = explode('~', $szCell);
			$arrCell = array();
			foreach ( $x AS $p ) {
				$p = explode('=', $p, 2);
				$arrCell[$p[0]] = $p[1];
			}
			$arrTable[$r][$c] = $arrCell;
			$c++;
		}
		$r++;
	}
	fwrite(($fp=fopen('arrTable.tbl','w')), serialize($arrTable));
	fclose($fp);
	print_r( $arrTable );
	exit;
}

?>
<html>

<head>
<title>TechTree V2</title>
<style type="text/css">
body, table {
	font-family		: verdana;
	font-size		: 11px;
}
#techtree td {
	width			: 90px;
	height			: 35px;
	text-align		: left;
	cursor			: pointer;

font-weight:bold;
}
#techtree td.empty {
	background-color: #333;
}
#techtree td.header {
	background-color: midnightblue;
	color			: white;
	text-align		: center;
	font-weight		: bold;
	vertical-align	: middle;
}
#techtree td.research {
	background-color: #ffb0b0;
}
#techtree td.development {
	background-color: lightgreen;
}
#floater a:hover,
#floater a {
	color			: white;
	text-decoration	: none;
}
</style>
<script type="text/javascript" src="http://games.jouwmoeder.nl/ajax_1_2_1.js"></script>
<script type="text/javascript">
<!--//
var g_objTD = null, g_bKeyDown = true;
function assignTD(o) {
	cancelTD();
	g_objTD = o;
	g_objTD.style.backgroundColor = 'lime';
	return false;
}
function cancelTD() {
	if ( g_objTD ) {
		g_objTD.style.backgroundColor = null;
		g_objTD = null;
	}
	return false;
}
function saveTbl(js) {
	cancelTD();
	if ( !js ) {
		var t = $('techtree').innerHTML.replace(/ style=""/gi,'').replace(/ colspan="1"/gi,'').replace(/ r_d_id=""/gi,'');
		new Ajax('?', {
			params : 'table=' + base64encode(t)
		});
		return false;
	}
	var rows = $('techtree').rows, t = [];
	for ( i=0; i<rows.length; i++ ) {
		t[i] = '';
		for ( j=0; j<rows[i].cells.length; j++ ) {
			c = rows[i].cells[j];
			t[i] += ':class=' + c.className + '~colspan=' + ( c.getAttribute('colspan') ? c.getAttribute('colspan') : '1' ) + '~content=' + ( c.getAttribute('r_d_id') ? c.getAttribute('r_d_id') : c.innerHTML );
		}
		t[i] = t[i].substring(1);
	}
	t = t.join('|');
//	$('__tbl').innerHTML = t;
	new Ajax('?', {
		params		: 'phptable=' + escape(t),
		onComplete	: function(a) {
			var t = a.responseText;
			$('__tbl').innerHTML = t;
//			alert(t);
		}
	});
	return false;
}
function assignType(t) {
	if ( !g_objTD ) {
		return false;
	}
	switch(t) {
		case 'h':
			g_objTD.className = 'header';
		break;
		case 'r':
			g_objTD.className = 'research';
		break;
		case 'd':
			g_objTD.className = 'development';
		break;
		case '':
			g_objTD.className = '';
			g_objTD.innerHTML = '';
			g_objTD.setAttribute('r_d_id', '');
			g_objTD.title = '';
		break;
	}
	return cancelTD();
}
function doAction(a) {
	if ( !g_objTD ) {
		return false;
	}
	switch(a) {
		case 'r':
			if ( 0 == g_objTD.cellIndex || 1 >= g_objTD.parentNode.cells.length ) {
				alert('Invalid request');
				return false;
			}
			var td = g_objTD, sib = td.parentNode.cells[td.cellIndex-1];
			td.parentNode.removeChild(td);
			c = parseInt(sib.getAttribute('colspan')?sib.getAttribute('colspan'):1) + parseInt(td.getAttribute('colspan')?td.getAttribute('colspan'):1);
			sib.setAttribute( 'colspan', ""+c+"" );
		break;
		case 't':
			var t = prompt('Text:', g_objTD.innerHTML);
			g_objTD.innerHTML = t ? t : g_objTD.innerHTML;
		break;
		case 'e':
			g_objTD.innerHTML = '';
		break;
	}
	return cancelTD();
}

var $=function(o){if('object'!=typeof o){o=document.getElementById(o);}return o;}, base64encode = function(str){
	var b64ec = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	var out = "", i = 0, len = str.length;
	while( i < len ) {
	var c1 = str.charCodeAt( i++ ) & 0xff;
	if( i == len ) {
		out += b64ec.charAt( c1 >> 2 )
			+ b64ec.charAt( ( c1 & 0x3 ) << 4 )
			+ "==";
		break;
	}
	var c2 = str.charCodeAt( i++ );
	if( i == len ) {
		out += b64ec.charAt( c1 >> 2 )
			+ b64ec.charAt( ( ( c1 & 0x3 ) << 4 ) | ( ( c2 & 0xF0 ) >> 4 ) )
			+ b64ec.charAt( ( c2 & 0xF ) << 2 )
			+ "=";
		break;
	}
	var c3 = str.charCodeAt( i++ );
	out += b64ec.charAt( c1 >> 2 )
		+ b64ec.charAt( ( ( c1 & 0x3 ) << 4 ) | ( ( c2 & 0xF0 ) >> 4 ) )
		+ b64ec.charAt( ( ( c2 & 0xF ) << 2 ) | ( ( c3 & 0xC0 ) >> 6 ) )
		+ b64ec.charAt( c3 & 0x3F );
	}
	return out;
}
window.onkeydown = function(e) {
	if ( !g_bKeyDown ) {
		return false;
	}
	if ( !e ) {
		e = window.event;
	}
//console.debug(e.keyCode);
	switch ( e.keyCode )
	{
		case 88: // X
			var s = $('__menu').style;
			s.display = s.display == 'none' ? '' : 'none';
		break;

		case 27: // ESC
			cancelTD();
		break;

		case 72: // H
			assignType('h')
		break;
		case 82: // R
			assignType('r')
		break;
		case 68: // D
			assignType('d')
		break;
		case 76: // L
			assignType('')
		break;

		case 86: // V
			doAction('r')
		break;
		case 84: // T
			doAction('t')
		break;
		case 80: // P
			doAction('a')
		break;
		case 69: // E
//			doAction('e')
		break;
	}
}
//-->
</script>
</head>

<body bgcolor="#222222" style="margin:5px;">

<table border="1" cellpadding="5" cellspacing="0" width="100%"><tr valign="top"><td width="95%">

<table id="techtree" border="0" cellpadding="3" cellspacing="1" width="100%">
<?php

$szTable = file_get_contents('szTable.tbl');

if ( !empty($szTable) ) {
	echo trim($szTable)."\n";
}
else {
	echo str_repeat('<tr valign="top">'.str_repeat('<td colspan="1" onclick="assignTD(this);" class="empty"></td>'."\n", 9).'</tr>'."\n", 11);
}

?>
</table>

</td><td id="__menu" width="5%">

<div id="floater" style="border:solid 1px white;background-color:green;width:200px;padding:2px;">
<table border="0" width="100%" style="color:white;">
<tr><td><a href="#" onclick="return cancelTD();">Annuleer selectie</a></td></tr>
<tr><td><a href="#" onclick="return saveTbl();">Dump table</a></td></tr>
<tr><td><a href="#" onclick="return saveTbl(1);">Dump table (JS)</a></td></tr>
<tr><td>&nbsp;</td></tr>
	<tr><th>Types</th></tr>
<tr><td><a href="#" onclick="return assignType('h');"><u>H</u>eader</a></td></tr>
<tr><td><a href="#" onclick="return assignType('r');"><u>R</u>esearch</a></td></tr>
<tr><td><a href="#" onclick="return assignType('d');"><u>D</u>evelopment</a></td></tr>
<tr><td><a href="#" onclick="return assignType('');"><u>L</u>eeg</a></td></tr>
	<tr><th>Acties</th></tr>
<tr><td><a href="#" onclick="return doAction('a');"><u>A</u>ssign R&D</a></td></tr>
<tr><td><a href="#" onclick="return doAction('r');"><u>V</u>erwijder</a></td></tr>
<tr><td><a href="#" onclick="return doAction('t');"><u>T</u>ekst</a></td></tr>
<!--<tr><td><a href="#" onclick="return doAction('e');"><u>E</u>mpty</a></td></tr>-->
</table>
<br />
<br />
<form style="margin:0;padding:0;"><select style="width:100%;" size="15" id="__rd" onchange="if(g_objTD&&this.value){x=this.value.split(':',3);g_objTD.innerHTML=x[2];g_objTD.setAttribute('r_d_id', x[0]);g_objTD.className=x[1];g_objTD.title=x[0];}this.parentNode.reset();cancelTD();">
<?php

$RD = db_select('d_r_d_available', '1 ORDER BY id ASC');
foreach ( $RD AS $rd ) {
	echo '<option title="'.$rd['id'].'" value="'.$rd['id'].':'.( 'r'==$rd['T'] ? 'research' : 'development' ).':'.htmlspecialchars($rd['name']).'">'.strtoupper($rd['T']).' ('.$rd['id'].') '.$rd['name'].'</option>';
}

?>
</select></form>
</div>

</td></tr></table>

<pre id="__tbl" style="color:white;"></pre>

</body>

</html>