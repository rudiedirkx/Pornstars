/**
 * General JS library
 * 
 * CHANGELOG
 * =================
 * DATE				VERSION			AUTHOR					DESCRIPTION
 * 2007-08-31		1.2.1			Rudie Dirkx				- Added function: getPosition()
 * 2007-09-13		1.2.2			Rudie Dirkx				- Removed functions: empty(), isset()
 *															- Added function: getFormVars()
 * 2007-10-22		1.2.4			Rudie Dirkx				- Updated function getFormVars(), it handles radiobuttons correctly now too
 *															- Added function postForm(), which uses getFormVars() to post a form with Ajax
 * 2007-11-09		1.2.5			Rudie Dirkx				- Updated function getFormVars() so it handles multiple selects now too
 *															- Added function foreach() to fake a PHP foreach construction
 * 2007-12-24		1.2.6			Rudie Dirkx				- Added function setInnerHTML() to enable <script>s loaded by AJAX
 * 2007-12-29		1.2.7			Rudie Dirkx				- Overall cleanup of misc vars laying around; all used vars are 'private' now
 *															- Updated setInnerHTML() so that it appends the scripts to the appointed element
 *															- Removed function $F
 *															- Added a try block in setInnerHTML() for IE
 * 
 */

function $( f_src ) {
	if ( 'object' != typeof f_src ) {
		f_src = document.getElementById(f_src);
	}
	return f_src;
}

function setCookie( f_szName, f_szValue ) {
	var e = new Date();
	e.setTime((new Date()).getTime()+3600000*24*999);
	document.cookie = f_szName + '=' + f_szValue + ';expires='+e.toGMTString();
}

function addEventHandler( a, b, c, d ) {
	if ( a.addEventListener ) {
		return a.addEventListener( b, c, !!d );
	}
	else if ( a.attachEvent ) {
		return a.attachEvent( 'on'+b, c );
	}
	a['on'+b] = c;
}

function getPosition( f_obj ) {
	var curleft = curtop = 0;
	if (f_obj.offsetParent) {
		curleft = f_obj.offsetLeft
		curtop = f_obj.offsetTop
		while (f_obj = f_obj.offsetParent) {
			curleft += f_obj.offsetLeft
			curtop += f_obj.offsetTop
		}
	}
	return [curleft,curtop];
}

function getFormVars( f_objForm ) {
	var f = $(f_objForm), v = '';
	for ( var i=0; i<f.elements.length; i++ ) {
		var e = f.elements[i], t = e.type.toLowerCase();
		// Special handling for checkboxes AND radiobuttons (we need an array of selected checkboxes..)!
		if ( !e.name || ( ( t == 'checkbox' || t == 'radio' ) && !e.checked ) ||  t == 'file' ) {
//			continue;
		}
		else if ( 'select' == e.nodeName.toLowerCase() && e.multiple ) {
			var o = e.options;
			for ( var j=0; j<o.length; j++ ) {
				if ( o[j].selected ) {
					v += '&' + e.name + '=' + o[j].value;
				}
			}
//			continue;
		}
		else {
			v += '&' + e.name + '=' + e.value;
		}
	}
	return v.substr(1);
}

function postForm( f_objForm, f_funcHandler )
{
	if ( 'function' != typeof f_funcHandler ) {
		f_funcHandler = function(){};
	}
	var f = $(f_objForm), q = getFormVars(f);
	if ( 'function' == typeof Ajax && 'undefined' != Ajax.version ) {
		new Ajax(f.getAttribute('action'), {
			method		: f.getAttribute('method').toUpperCase(),
			params		: q,
			onComplete	: f_funcHandler
		});
		return false;
	}
}

//var g_arrJSParams = {};
function JSParams( f_szParams ) {
	if ( !f_szParams ) {
		f_szParams = document.location.hash;
	}
	if ( f_szParams.substr(0,1) == '#' ) {
		f_szParams = f_szParams.substr(1);
	}
	var arrParams = f_szParams.split('&'), objParams = {};
	for ( var i=0; i<arrParams.length; i++ ) {
		var x = arrParams[i].split('=', 2);
		if ( x.length == 1 ) x[1] = true;
		objParams[x[0]] = x[1];
	}
//	g_arrJSParams = objParams;
	return objParams;
}

function setInnerHTML(o,h) {
	var regex = /^([\s\S]*?)<script([\s\S]*?)>([\s\S]*?)<\/script>([\s\S]*)$/i;
	var regex_src = /src=["'](.*?)["']/i;
	var matches, id, script, output = '', subject = h;
	var scripts = [];
	while (true) {
		matches = regex.exec(subject);
		if ( !matches || !matches[0] ) {
			break;
		}
		subject = subject.replace(/(<script([\s\S]*?)>([\s\S]*?)<\/script>)/, '');
		var src_match = regex_src.exec(matches[2]);
		var src = null;
		if ( src_match && src_match[0] ) {
			src = src_match[1];
		}
		scripts.push([ matches[3], src ]);
	}
	var i = scripts.length, head = document.getElementsByTagName('head')[0];
	o.innerHTML = subject;
	while ( i-- ) {
		var s = document.createElement('script');
		s.setAttribute( 'type', 'text/javascript' );
		if ( scripts[i][1] ) {
			s.setAttribute( 'src', scripts[i][1] );
		}
		try{s.innerHTML = scripts[i][0];}catch(e){}
		o.appendChild(s);
	}
	return o;
}