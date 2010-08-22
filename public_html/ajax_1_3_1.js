/**
 * Ajax library, v1.3.1
 * 
 * CHANGELOG
 * =================
 * DATE				VERSION			AUTHOR					DESCRIPTION
 * 2007-05-16		1.1				Rudie Dirkx				- Removed the busy member of the object and replaced the busy setting by +1 and -1 in Ajax.busy
 * 2007-05-24		1.1.1			Rudie Dirkx				- Target created in the constructor.
 * 															- Target updated if it starts with a ?. Used to fail.
 * 2007-05-24		1.1.2			Rudie Dirkx				- Removed member 'target'. Target is now passed to the request() function directly.
 * 															- Removed member 'asynchronous'. All requests are now always async.
 * 2007-05-25		1.1.3			Rudie Dirkx				- Replaced '#' by '' in the ajax target if first char is '?'. Before, the '?' would come after the '#', not making it a query string.
 * 2007-06-05		1.1.4			Rudie Dirkx				- Remove everything after the first # in the target.
 * 2007-06-05		1.2.1			Rudie Dirkx				- Added function Ajax.doAjaxLink, which transforms all rel="ajax" html elements to an ajax trigger
 * 2007-12-26		1.3.1			Rudie Dirkx				- Massive update to the 'params' param of the Ajax call. GET params can now be sent using 'params' in the Ajax options.
 * 
 */

Function.prototype.bind = function( f_object )
{
	var __method = this, args = [];
	return function() {
		return __method.apply(f_object, args.concat(arguments));
	}
}

var Ajax = function( f_szTarget, f_arrOptions )
{
	// C O N S T R U C T O R //
	if ( !f_szTarget )
	{
		var t = document.location.href.split('#');
		f_szTarget = t[0];
	}
	else if ( '?' == f_szTarget.substr(0,1) )
	{
		szCurrentNoQuery = document.location.href.substr(0, document.location.href.length-document.location.search.length);
		f_szTarget = szCurrentNoQuery.replace(/#/g, '') + "" + f_szTarget;
	}
	var szTarget = "" + f_szTarget;

	if ( !this.getTransport() )
	{
		alert('No AJAX supported for this browser!');
	}

	this.setOptions(f_arrOptions);

	this.request(szTarget);
};

Ajax.version = '1.3.1';
Ajax.prototype = {
	// MEMBERS //

	// PROPERTIES
	/**
	 * @brief		xmlhttp
	 * @type		object
	 * @desc		The XmlHttp object that actually does the request. What kind of object this is, depends on the browser used.
	 * 
	 */
	xmlhttp			: null,
	/**/

	/**
	 * @brief		method
	 * @type		string
	 * @desc		The HTTP request method.
	 * 
	 */
	method			: 'POST',
	/**/

	/**
	 * @brief		params
	 * @type		string
	 * @desc		The query string to act as post body.
	 * 
	 */
	params			: '',
	/**/

	/**
	 * @brief		onComplete
	 * @type		function
	 * @desc		The function to execute when the ajax request is made and status 4 is returned.
	 * 
	 */
	onComplete		: function(){},
	/**/


	// METHODS //
	getTransport: function()
	{
		try {
			this.xmlhttp = new XMLHttpRequest();
		} catch (e1) {
			try {
				this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e2) {
				try {
					this.xmlhttp = new XMLHttpRequest("Microsoft.XMLHTTP");
				} catch (e3) {
					this.xmlhttp = false;
				}
			}
		}

		return !!this.xmlhttp;
	},

	setOptions: function( f_arrOptions )
	{
		// Method
		if ( 'string' == typeof f_arrOptions['method'] )
		{
			this.method = f_arrOptions['method'].toUpperCase();
		}

		// Parameters
		if ( 'string' == typeof f_arrOptions['params'] )
		{
			this.params = f_arrOptions['params'];
		}

		// Completion function
		if ( 'function' == typeof f_arrOptions['onComplete'] )
		{
			this.onComplete = function(){ f_arrOptions['onComplete']( this.xmlhttp ) };
		}

		return true;
	},

	request: function( f_szTarget )
	{
		// One more request is busy
		Ajax.busy += 1;

		if ( 'GET' == this.method && this.params ) {
			var x = f_szTarget.split('?');
			f_szTarget = x[0] + '?' + this.params;
			this.params = '';
		}

		// Start request
		this.xmlhttp.open(
			this.method,
			f_szTarget,
			true
		);
		if ( 'POST' == this.method )
		{
			this.xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		}
		this.xmlhttp.onreadystatechange = this.whileRequestHandler.bind(this);
		this.xmlhttp.send(this.params);
	},

	whileRequestHandler: function( f_object )
	{
		f_object = this;
		// Ajax statuses:
		// 0 - Means it's ready to go (uninitialized)
		// 1 - Loading
		// 2 - Finished loading
		// 3 - Almost ready for use
		// 4 - Loading is complete and ready to be dealt with.

		if ( 1 == f_object.xmlhttp.readyState )
		{
			Ajax.arrGlobalHandlers['onStart'](f_object.xmlhttp); // You might want to _not_ pass this.xmlhttp to the onStart handler function
		}
		else if ( 4 == f_object.xmlhttp.readyState )
		{
			// Execute the user's onComplete post-ajax function
			f_object.onComplete();

			Ajax.busy -= 1;

			// Execute the user's onComplete handler
			Ajax.arrGlobalHandlers['onComplete'](f_object.xmlhttp); // You might want to _not_ pass this.xmlhttp to the onComplete handler function
		}
	}

};

var CustomAjaxHandler2 = function(a) {
	console.debug(this);
	console.debug(a);
};

// STATIC METHODS //
Ajax.busy = 0;
Ajax.arrGlobalHandlers = { 'onStart' : function(){}, 'onComplete' : function(){} };
Ajax.setGlobalHandlers = function( f_arrHandlers )
{
	if ( "object" != typeof f_arrHandlers ) return false;

	if ( f_arrHandlers['onStart'] && "function" == typeof f_arrHandlers['onStart'] )
	{
		Ajax.arrGlobalHandlers.onStart = f_arrHandlers['onStart'];
	}

	if ( f_arrHandlers['onComplete'] && "function" == typeof f_arrHandlers['onComplete'] )
	{
		Ajax.arrGlobalHandlers.onComplete = f_arrHandlers['onComplete'];
	}

	return true;
};
Ajax.doAjaxLink = function( f_obj )
{
	if ( "undefined" == typeof f_obj.rel || "ajax" != f_obj.rel ) return false;

	f_obj.onclick = function()
	{
		href = f_obj.href + (-1 != f_obj.href.indexOf('?') ? '&' : '?') + 'ajax=1';
		new Ajax(href, {
			method		: 'get',
			onComplete	: function(a)
			{
				if ( f_obj.getAttribute('oncomplete') && window[f_obj.getAttribute('oncomplete')] )
				{
					window[f_obj.getAttribute('oncomplete')](a);
				}
			}
		});
		return false;
	}
	return true;
};