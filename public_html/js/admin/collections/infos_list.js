/* collection */

define([	'backbone', 'models/info'], 
	function(Bb,			InfoModel)
	{
		var col = Bb.Collection.extend({
			model: InfoModel,
			url: '/admin/get-json-infos-list'
		});

		return col;
	}
);