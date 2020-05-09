<?php

require '../inc.bootstrap.php';

?>
<html>

<head>
<title>TechTree V2</title>
<style>
body {
	margin-top: 40px;
}
#info {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	height: 40px;
	padding: 0 10px;
	line-height: 20px;
	white-space: pre-line;
	background-color: pink;
}
table {
	border-collapse: separate;
	border-spacing: 0 5px;
}
td, th {
	border: solid 1px #555;
	border-width: 1px 1px 1px 0;
	padding: 3px;
}
th {
	cursor: pointer;
}
th:not(.double) {
	background-color: #eee;
}
th.focus {
	background-color: #ccc;
}
</style>
</head>

<body>
<div id="info"></div>

<?= getChildren() ?>

<script>
document.onclick = function(e) {
	var cell = e.target;
	if (!cell.dataset.rdId) return;

	var unfocus = [].slice.call(document.querySelectorAll('.focus'));
	unfocus.forEach(function(cell) {
		cell.classList.remove('focus');
	});

	if (unfocus.indexOf(cell) < 0) {
		document.querySelector('#info').textContent = cell.textContent + "\n" + cell.dataset.explanation;

		var focus = [].slice.call(document.querySelectorAll('[data-rd-id="' + cell.dataset.rdId + '"]'));
		focus.forEach(function(cell) {
			cell.classList.add('focus');
		});
	}
	else {
		document.querySelector('#info').textContent = '';
	}
};
</script>

</body>

</html>
<?php

function getChildren( $f_iParent = null, &$arrDone = array(0) ) {
	global $db;

	if ( !$f_iParent ) {
		$RD = $db->select('d_r_d_available a','NOT EXISTS (SELECT * FROM d_r_d_requires WHERE r_d_id = a.id)');
	}
	else {
		$RD = $db->select('d_r_d_available a, d_r_d_requires r','a.id = r.r_d_id AND r.r_d_requires_id = ' . $f_iParent . " /*AND a.id NOT IN(" . implode(", ", $arrDone) . ")*/");
	}

	if ( $RD = $RD->all() ) {
		$szHtml = '<table>';
		foreach ( $RD AS $rd ) {
			$name = strtoupper($rd['T']) . '. ' . $rd['name'];

			if ( isset($arrDone[ $rd['id'] ]) ) {
				$szHtml .= '<tr><th data-explanation="' . html($rd['explanation']) . '" data-rd-id="' . $rd['id'] . '" class="double">' . html($name) . '</th></tr>';
			}
			else {
				$arrDone[ $rd['id'] ] = $rd['id'];
				$szHtml .= '<tr><th data-explanation="' . html($rd['explanation']) . '" data-rd-id="' . $rd['id'] . '">' . html($name) . '</th>';
				$szChildrenHtml = getChildren($rd['id'], $arrDone);
				if ( $szChildrenHtml ) {
					$szHtml .= '<td>' . $szChildrenHtml . '</td>';
				}
				$szHtml .= '</tr>';
			}
		}
		$szHtml .= '</table>';
		return $szHtml;
	}

	return '';
}
