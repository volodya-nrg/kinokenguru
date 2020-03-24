/* collection */

define([	'backbone', 'models/idea'], 
	function(Bb,			IdeaModel)
	{
		var col = Bb.Collection.extend({
			model: IdeaModel,
			url: '/admin/get-json-ideas-list'
		});

		return col;
	}
);