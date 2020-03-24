/* collection */

define([	'backbone', 'models/page'], 
	function(Bb,			model)
	{
		var col = Bb.Collection.extend({
			model: model,
			url: '/admin/get-json-pages-list'
		});

		return col;
	}
);