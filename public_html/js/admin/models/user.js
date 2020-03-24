/* model */

define([	'backbone'], 
	function(Bb)
	{
		var model = Bb.Model.extend({
			defaults: {
			},
			urlRoot: '/admin/users',
			initialize: function(){
			},
			validate: function(attrs){
			}
		});
	
		return model;
	}
);