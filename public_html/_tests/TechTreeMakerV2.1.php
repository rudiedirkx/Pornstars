<?php

require_once '../inc.config.php';

// @todo

?>
<html>

<head>
<title>TechTree V2.1</title>
<style>
body {
	background: #222;
	margin: 5px;
}
body, table {
	font-family: verdana;
	font-size: 11px;
}

#techtree {
	border-collapse: collapse;
}
#techtree td {
	width: 90px;
	height: 35px;
	text-align: left;
	cursor: pointer;
	padding: 5px 7px;
	border: solid 3px #222;

	font-weight: bold;
}
#techtree td.empty {
	background-color: #333;
}
#techtree td.header {
	background-color: midnightblue;
	color: white;
	text-align: center;
	font-weight: bold;
	vertical-align: middle;
}
#techtree td.tt-cell.r {
	background-color: #ffb0b0;
}
#techtree td.tt-cell.d {
	background-color: lightgreen;
}
#techtree td.tt-cell.selection {
	background-color: lime;
}
#techtree td.tt-cell:not(.empty):not([data-rd-id]) {
	opacity: 0.5;
}

#floater a:hover,
#floater a {
	color: white;
	text-decoration: none;
}

pre {
	white-space: normal;
}

option.assigned {
	color: green;
}
option.unassigned {
	color: red;
}
option.doubly {
	font-weight: bold;
}

/**
 * DEBUG
 */

.focus-parent,
.focus-child {
	opacity: 0.75;
}

</style>
</head>

<body>

<table border="1" cellpadding="5" cellspacing="0" width="100%"><tr valign="top"><td width="95%">

<table id="techtree">
<?php

$szTable = file_get_contents(__DIR__ . '/techtree.html');
if ( $szTable ) {
	echo $szTable;
}
else {
	echo str_repeat('<tr>' . str_repeat('<td class="tt-cell empty"></td>', 9) . '</tr>', 11);
}

?>
</table>

</td><td width="5%">

<div id="floater" style="border:solid 1px white;background-color:green;width:200px;padding:2px;">
	<table border="0" width="100%" style="color:white;">
		<tr><th>Resultaat</th></tr>
		<tr><td><a href="#" onclick="return false">Dump table</a></td></tr>
		<tr><td><a href="#" onclick="return false">Dump table (JS)</a></td></tr>
		<tr><td>&nbsp;</td></tr>
		<!--
		<tr><th>Types</th></tr>
		<tr><td><a href="#" onclick="return assignType('h');"><u>H</u>eader</a></td></tr>
		<tr><td><a href="#" onclick="return assignType('r');"><u>R</u>esearch</a></td></tr>
		<tr><td><a href="#" onclick="return assignType('d');"><u>D</u>evelopment</a></td></tr>
		<tr><td><a href="#" onclick="return assignType('');"><u>L</u>eeg</a></td></tr>
		<tr><th>Acties</th></tr>
		<tr><td><a href="#" onclick="return doAction('r');"><u>V</u>erwijder</a></td></tr>
		<tr><td><a href="#" onclick="return doAction('t');"><u>T</u>ekst</a></td></tr>
		-->
	</table>

	<pre style="color: white" id="summary"></pre>

	<br />
	<br />

	<form style="margin:0;padding:0;">
		<select style="width:100%;" size="15" id="r_d">
		<?php

		$RD = db_select('d_r_d_available', '1 ORDER BY T, id');
		foreach (['r' => 'RESEARCH', 'd' => 'DEVELOPMENT'] as $type => $typeName) {
			echo '<optgroup label="' . html($typeName) . '">';
			foreach ( $RD AS $rd ) {
				if ( $rd['T'] == $type ) {
					echo '<option data-type="' . $rd['T'] . '" data-name="' . html($rd['name']) . '" value="' . $rd['id'] . '">' . strtoupper($rd['T']) . ' ' . html($rd['name']) . '</option>';
				}
			}
			echo '</optgroup>';
		}

		?>
		</select>
	</form>
</div>

</td></tr></table>

<pre id="export" style="color: white"></pre>

<script>
Object.defineProperty(HTMLTableCellElement.prototype, 'realCellIndex', {get: function() {
	var cell = this;
	var index = 0;
	while (cell = cell.previousElementSibling) {
		index += cell.colSpan;
	}
	return index;
}});
HTMLElement.prototype.addClass = function(className) {
	this.classList.add(className);
};
HTMLElement.prototype.removeClass = function(className) {
	this.classList.remove(className);
};
NodeList.prototype.forEach = Array.prototype.forEach;
Array.prototype.invoke = NodeList.prototype.invoke = function(method, args) {
	this.forEach(function(item) {
		item[method].apply(item, args);
	});
};



/**
 * Logic
 */

var tt = {};

tt.makeSelection = function(cell) {
	var old = tt.cancelSelection();
	if (cell != old) {
		cell.addClass('selection');
	}
};

tt.getSelection = function() {
	return document.querySelector('.selection');
};

tt.cancelSelection = function() {
	var selection = tt.getSelection();
	if ( selection ) {
		selection.removeClass('selection');
	}
	return selection;
};

tt.assignRD = function(option) {
	var selection = tt.getSelection();

	selection.dataset.rdId = option.value;
	selection.innerText = option.dataset.name;
	selection.className = 'tt-cell ' + option.dataset.type;

	tt.autosave();
};

tt.removeRD = function() {
	var selection = tt.getSelection();

	delete selection.dataset.rdId;
	selection.innerText = '';
	selection.className = 'tt-cell empty';

	tt.autosave();
};

tt.moveRD = function(target) {
	var selection = tt.getSelection();
	if (target.dataset.rdId) return tt.cancelSelection();

	target.dataset.rdId = selection.dataset.rdId;
	target.innerText = selection.innerText;
	target.className = selection.className;

	delete selection.dataset.rdId;
	selection.innerText = '';
	selection.className = 'tt-cell empty';

	tt.cancelSelection();
	tt.autosave();
};

tt.rdChildren = function(cell) {
	var childRow = cell.parentNode.nextElementSibling;
	if ( !childRow ) return;

	return [].filter.call(childRow.children, function(child) {
		return child.dataset.rdId && child.realCellIndex >= cell.realCellIndex && child.realCellIndex < cell.realCellIndex + cell.colSpan;
	});
};

tt.rdParent = function(cell) {
	var parentRow = cell.parentNode.previousElementSibling;
	if ( !parentRow ) return;

	var parents = parentRow.children;
	for (var i = 0; i < parents.length; i++) {
		if ( parents[i].realCellIndex == cell.realCellIndex ) {
			return parents[i];
		}
		if ( parents[i].realCellIndex > cell.realCellIndex ) {
			return parents[i - 1];
		}
	}
};

tt.summarize = function() {
	var assigned = document.querySelectorAll('.tt-cell[data-rd-id]');
	var doubles =[];
	var unique = [].reduce.call(assigned, function(list, cell) {
		var id = parseInt(cell.dataset.rdId);
		list.indexOf(id) < 0 ? list.push(id) : doubles.push(id);
		return list;
	}, []);

	var options = document.querySelector('#r_d').options;
	[].forEach.call(options, function(option) {
		var id = parseInt(option.value);
		option.className = unique.indexOf(id) < 0 ? 'unassigned' : 'assigned';
		if ( doubles.indexOf(id) >= 0 ) {
			option.className += ' doubly';
		}
	});

	document.querySelector('#summary').textContent = [
		unique.length + ' / ' + options.length + ' assigned',
		doubles.length + ' doubly: ' + (doubles.join(', ') || '-'),
	].join('\n');

	var exported = [].reduce.call(assigned, function(exported, cell) {
		exported.push([
			parseInt(cell.dataset.rdId),
			tt.rdChildren(cell).map(function(child) {
				return parseInt(child.dataset.rdId);
			})
		]);
		return exported;
	}, []);
	document.querySelector('#export').textContent = JSON.stringify(exported);
};

tt.autosave = function() {
	tt.cancelSelection();
	localStorage.rdTableV21 = document.querySelector('#techtree').innerHTML;

	tt.summarize();
};

tt.autoload = function() {
	if (localStorage.rdTableV21) {
		document.querySelector('#techtree').innerHTML = localStorage.rdTableV21;
	}

	tt.summarize();
};



/**
 * Event handlers
 */

document.onclick = function(e) {
	if (e.target.classList.contains('tt-cell')) {
		tt.makeSelection(e.target);
	}
};

document.oncontextmenu = function(e) {
	if (e.target.classList.contains('tt-cell')) {
		if (tt.getSelection()) {
			e.preventDefault();
			tt.moveRD(e.target);
		}
	}
};

document.querySelector('#r_d').oninput = function(e) {
	var selection = tt.getSelection();
	var option = tt.getRD(this.value);
	if ( selection && option ) {
		tt.assignRD(option);
	}

	this.form.reset();
	tt.cancelSelection();
};

var keyActions = {
	// esc - cancel selection
	27: function() {
		tt.cancelSelection();
	},
	// delete - remove R&D
	46: function(selection) {
		if ( selection ) {
			tt.removeRD(selection);
		}
	},

	// right - colspan++
	39: function(selection) {
		if ( selection ) {
			selection.colSpan++;
			tt.autosave();
		}
	},

	// left - colspan--
	37: function(selection) {
		if ( selection ) {
			if ( selection.colSpan > 1 ) {
				selection.colSpan--;
				tt.autosave();
			}
		}
	},
};
document.onkeydown = function(e) {
	console.log('keyCode', e.keyCode);

	if ( keyActions[e.keyCode] ) {
		keyActions[e.keyCode].call(null, tt.getSelection());
	}
};

window.onload = function() {
	tt.autoload();
};



/**
 * DEBUG
 */

document.onmouseover = function(e) {
	if ( e.target.classList.contains('tt-cell') && e.target.dataset.rdId ) {
		// Parent
		var parent = tt.rdParent(e.target);
		parent && parent.addClass('focus-parent');

		// Children
		tt.rdChildren(e.target).invoke('addClass', ['focus-child']);
	}
};
document.onmouseout = function(e) {
	document.querySelectorAll('.focus-parent').invoke('removeClass', ['focus-parent']);
	document.querySelectorAll('.focus-child').invoke('removeClass', ['focus-child']);
};

</script>

</body>

</html>
