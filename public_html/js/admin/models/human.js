/* model */

define([	'backbone'], 
	function(Bb)
	{
		var model = Bb.Model.extend({
			defaults: {
				fio_ru: "",
				fio_original: "",
				about: "",
				birthday: "",
				country_id: 0,
				city_id: 0,
				
				files: [],
				images: []
			},
			urlRoot: '/admin/humans',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];
				
				if(attrs.fio_ru === ""){
					aErrs.push('fio_ru is empty');
				}

				return aErrs.join("\n");
			}
		});
	
		return model;
	}
);