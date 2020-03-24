/* model */

define([	'backbone'], 
	function(Bb)
	{
		var model = Bb.Model.extend({
			defaults: {
			},
			urlRoot: '/admin/comments',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];
				
				if(attrs.text === ""){
					aErrs.push('text is empty');
				}

				return aErrs.join("\n");
			}
		});
	
		return model;
	}
);