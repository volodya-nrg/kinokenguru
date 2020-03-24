/* collection */

define([	'backbone', 'models/user'], 
	function(Bb,			UserModel)
	{
		var col = Bb.Collection.extend({
			model: UserModel,
			url: '/admin/get-json-users-list'
		});

		return col;
	}
);