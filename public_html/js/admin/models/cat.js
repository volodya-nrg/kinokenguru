/* model */

define([	'backbone'], 
	function(Bb)
	{
		var output = Bb.Model.extend({
			defaults: {
				slug: "",
				name: "",
				description: "",
				title: ""
			},
			urlRoot: '/admin/cats',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];

				if(attrs.slug === ""){
					aErrs.push('slug is empty');
				}
				if(attrs.name === ""){
					aErrs.push('name is empty');
				}

				return aErrs.join("\n");
			}
		});
	
		return output;
	}
);