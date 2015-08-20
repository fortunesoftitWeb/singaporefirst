/**
 * JMediaManager behavior for media component
 */
(function() {
var KrakenImageManager = this.KrakenImageManager = {

	initialize: function()
	{
		this.folderframe	= document.id('folderframe');
		this.folderpath		= document.id('folderpath');

		this.updatepaths	= $$('input.update-folder');

		this.frame		= window.frames['folderframe'];
		this.frameurl	= this.frame.location.href;
	},

	submit: function(task)
	{
		form = window.frames['folderframe'].document.id('krakenimagemanager-form');
		form.task.value = task;
		if (document.id('username')) {
			form.username.value = document.id('username').value;
			form.password.value = document.id('password').value;
		}
		if(task == "folder.reduceurl") {
			var input = document.createElement('input');
		    input.type = 'hidden';
		    input.name = 'imageurl';
		    input.value = document.id('imgUrl').value;
		    form.appendChild(input);
		}
		form.submit();
	},

	onloadframe: function()
	{
		// Update the frame url
		this.frameurl = this.frame.location.href;

		var folder = this.getFolder();
		if (folder) {
			this.updatepaths.each(function(path){ path.value =folder; });
			this.folderpath.value = basepath+'/'+folder;
		} else {
			this.updatepaths.each(function(path){ path.value = ''; });
			//this.folderpath.value = basepath;
		}
        
		//document.id(viewstyle).addClass('active');
	},

	setViewType: function(type)
	{
		document.id(type).addClass('active');
		document.id(viewstyle).removeClass('active');
		viewstyle = type;
		var folder = this.getFolder();
		this._setFrameUrl('index.php?option=com_krakenimage&view=mediaList&tmpl=component&folder='+folder+'&layout='+type);
	},
    
    getViewType: function()
	{
        return viewstyle;		
	},

	refreshFrame: function()
	{
		this._setFrameUrl();
	},

	getFolder: function()
	{
		var url	 = this.frame.location.search.substring(1);
		var args	= this.parseQuery(url);

		if (args['folder'] == "undefined") {
			args['folder'] = "";
		}

		return args['folder'];
	},

	parseQuery: function(query)
	{
		var params = new Object();
		if (!query) {
			return params;
		}
		var pairs = query.split(/[;&]/);
		for ( var i = 0; i < pairs.length; i++ )
		{
			var KeyVal = pairs[i].split('=');
			if ( ! KeyVal || KeyVal.length != 2 ) {
				continue;
			}
			var key = unescape( KeyVal[0] );
			var val = unescape( KeyVal[1] ).replace(/\+ /g, ' ');
			params[key] = val;
	   }
	   return params;
	},

	_setFrameUrl: function(url)
	{
		if (url != null) {
			this.frameurl = url;
		}
		this.frame.location.href = this.frameurl;
	},

	_getQueryObject: function(q) {
		var vars = q.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.each(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
		});
		return rs;
	},

	_getUriObject: function(u){
		var bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		return (bits)
			? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
			: null;
	}
};
})(document.id);

window.addEvent('domready', function(){
	// Added to populate data on iframe load
	KrakenImageManager.initialize();
	KrakenImageManager.trace = 'start';
	KrakenImageManager.onloadframe();
});
