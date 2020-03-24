/* model */

define([	'backbone'], 
	function(Bb)
	{
		var model = Bb.Model.extend({
			defaults: {
				slug: "",
				name: "",
				year: 0,
				link: "",
				duration: 0,
				description: "",
				slogan: "",
				old: "",
				see: 0,
				quality_video_id: 0,
				quality_dubbing_id: 0,
				budget: 0,
				rating_imdb: 0,
				rating_kinopoisk: 0,
				video_file: "",
				is_hide: 0,
				in_queue: 1,
				name_original: "",
				
				files: [],
				images: [],
				frames: [],
				countries: [],
				trailers: [],
				producers: [],
				actors: [],
				cats: []
			},
			urlRoot: '/admin/products',
			initialize: function(){
			},
			validate: function(attrs){
				var aErrs = [];
				
				if(this.isNew() && attrs.video_file === ""){
					aErrs.push('video_file is empty');
				}
				if(attrs.name === ""){
					aErrs.push('name is empty');
				}
				if(attrs.cats.length === 0){
					aErrs.push('cats is empty');
				}
				if(attrs.link === ""){
					aErrs.push('link is empty');
				}

				return aErrs.join("\n");
			}
		});
	
		return model;
	}
);