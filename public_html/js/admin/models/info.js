/* model */

define([	'backbone'], 
	function(Bb)
	{
		var model = Bb.Model.extend({
			defaults: {
				slug: "",
				title: "",
				h1: "",
				description: "",
				is_hide: 0,
				meta_keywords: "",
				meta_desc: "",
				cat_id: 0
			},
			urlRoot: '/admin/infos',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];
				
				if(attrs.title === ""){
					aErrs.push('title is empty');
				}
				if(attrs.h1 === ""){
					aErrs.push('h1 is empty');
				}

				return aErrs.join("\n");
			}
		});
	
		return model;
	}
);