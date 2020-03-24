/* model */

define([	'backbone'], 
	function(Bb)
	{
		var model = Bb.Model.extend({
			defaults: {
				slug: "",
				name: "",
				description: "",
				pos: 1,
				parent_id: 0,
				is_hide: 0,
				parent_ids: []
			},
			urlRoot: '/admin/info-cats',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];
				
				if(attrs.name === ""){
					aErrs.push('name is empty');
				}
				
				if(this.has('id') && this.get('id') === attrs.parent_id){
					aErrs.push('parent_id not correct');
				}

				return aErrs.join("\n");
			}
		});
	
		return model;
	}
);