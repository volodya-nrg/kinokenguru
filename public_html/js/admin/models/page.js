/* model */

define([	'backbone'], 
	function(Bb)
	{
		var output = Bb.Model.extend({
			defaults: {
				title: "",
				meta_keywords: "",
				meta_desc: "",
				description: ""
			},
			urlRoot: '/admin/pages',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];

				if(attrs.title === ""){
					aErrs.push('title is empty');
				}

				return aErrs.join("\n");
			}
		});
	
		return output;
	}
);