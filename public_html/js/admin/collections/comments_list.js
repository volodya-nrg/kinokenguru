/* collection */

define([	'backbone', 'models/comment'], 
	function(Bb,			CommentModel)
	{
		var col = Bb.Collection.extend({
			model: CommentModel,
			url: '/admin/get-json-comments-list'
		});

		return col;
	}
);