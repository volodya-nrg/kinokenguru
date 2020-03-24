/* model */

define([	'backbone'], 
	function(Bb)
	{
		var output = Bb.Model.extend({
			defaults: {
				name: "",
				description: ""
			},
			urlRoot: '/admin/quality-videos',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];

				if(attrs.name === ""){
					aErrs.push('name is empty');
				}

				return aErrs.join("\n");
			}
		});
	
		return output;
	}
);