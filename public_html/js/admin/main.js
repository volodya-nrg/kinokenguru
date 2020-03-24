var app			= {};
	app.model	= {};
	app.view	= {};
	app.col		= {};
	app.router	= {};

function handleFiles(obj){
	obj.myFiles = [];
	var files = obj.getUI('inputFile').get(0).files;
	var allowed_types = ['image/jpg','image/jpeg']; //'image/png',

	for (var i = 0; i < files.length; i++) {
		var file = files[i];

		if(allowed_types.indexOf(file.type) === -1 || file.length === 0){
			continue;
		}
		
		var reader = new FileReader();
		reader.onload = (function(aFiles, tmpFile) { return function(e) {
			aFiles.push({
				name: tmpFile.name,
				size: tmpFile.size,
				data: e.target.result
			});
		};})(obj.myFiles, file);
		reader.readAsDataURL(file);
	}
}
function removeThumbnail(e, curObj){
	var obj = e.currentTarget;
	var m = curObj.model;
	var imgDeleted = $(obj).data('img');
	var newArray = [];

	_.each(m.get('images'), function(image){
		if(image !== imgDeleted){
			newArray.push(image);
		}	
	});

	$(obj).parent().fadeOut();
	m.set('images', newArray);
}
function removeFrames(e, curObj){
	var obj = e.currentTarget;
	var m = curObj.model;
	var imgDeleted = $(obj).data('img');
	var newArray = [];

	_.each(m.get('frames'), function(image){
		if(image !== imgDeleted){
			newArray.push(image);
		}	
	});

	$(obj).parent().fadeOut();
	m.set('frames', newArray);
}
function multiselectToggle(e, opt){
	var $fa = $(e.currentTarget);
	var $grandfather = $fa.closest('.multiselect');
	var $tpl = $grandfather.find('> .multiselect-tpl');
	var $tpl_select = $tpl.find('select');
	var $tpl_input = $tpl.find('input[type=text]');
	
	if(opt === '-'){
		var $parent_wrapper = $fa.closest('.multiselect-tpl-is-new');
		var $select = $parent_wrapper.find('select');
		
		// только для select-a
		if($select.length && $tpl_select.length){
			var $option = $select.find('option:checked');
			$tpl_select.append($option).val(0);
			
			if($tpl.css('display') === 'none'){
				$tpl.show();
			}
		}
		
		$parent_wrapper.remove();
		
	} else {
		var $target = $grandfather.find('.multiselect-var-place');
		var $parent_wrapper = $fa.closest('.multiselect-tpl');
		var $clone = $parent_wrapper.clone();
		var $select_orig = $parent_wrapper.find('select');
		var $input_orig = $parent_wrapper.find('input[type=text]');
		
		if($select_orig.length){
			var $select_new = $clone.find('select');
			var $option = $select_orig.find('option:checked');
			var value =  $select_orig.val();
			
			if(value === "" || value === "0" || value === 0){
				return;
			}
			
			$select_orig.find('option:checked').remove();
			$select_new.find('option').remove();
			$select_new.append($option);
			
			if($select_orig.find('option').length <= 1){
				$parent_wrapper.hide();
			}
			
		} else if($input_orig.length) {
			$input_orig.val("");
		}

		$clone.addClass('multiselect-tpl-is-new');
		$target.append($clone);
	}
}

requirejs.config({
	baseUrl: '/js/admin',
	paths: {
	   // libs
	   jquery:					"../../vendor/jquery/jquery-3.1.1.min",
	   underscore:				"../../vendor/marionette/underscore",
	   backbone:				"../../vendor/marionette/backbone",
	   "backbone.radio":		"../../vendor/marionette/backbone.radio",
	   "backbone.marionette":	"../../vendor/marionette/backbone.marionette.min",
	   text:					"../../vendor/require/text",
	   domReady:				"../../vendor/require/domReady",
	   
	   "ckeditor.jquery":		"../../vendor/ckeditor/adapters/jquery",
	   "ckeditor.core":			"../../vendor/ckeditor/ckeditor",
	   inputMask:				"../../vendor/jquery/jquery.maskedinput.min"
	},
	shim: {
		"ckeditor.jquery": {
			// вначале грузим плагин jquery-адаптор, а потом ckeditor
			deps: ["jquery", "ckeditor.core"]
		}
	}
});

requirejs(['jquery', 'underscore', 'backbone', 'backbone.marionette',	
			'router',
			
			'models/country',
			'models/product',
			'models/info_cat',
			'models/quality_video',
			'models/quality_dubbing',
			'models/human'
		], 
	function($,		  _,			Bb,			Mn,						
			Router,
			
			CountryModel,
			ProductModel,
			InfoCatModel,
			QualityVideoModel,
			QualityDubbingModel,
			HumanModel
			){
		
		// установим для каждого запроса токен
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		
		var App = Mn.Application.extend({
			onStart: function() {
				window.app.router = new Router;
					
				// запустим историю
				Bb.history.start({pushState: true});
				// переназначим ссылки
				$(document.body).on('click', 'a.a-change-route', function(e){
					e.preventDefault();
					Bb.history.navigate(e.currentTarget.pathname, {trigger: true});
				});
			}
		});
		
		var app0 = new App; // к глобальному не нужно привязывать
		
		var loadInitialData = function() {
			return Promise.resolve();
		};
		loadInitialData().then(function() {
			var countries = Bb.Collection.extend({ model: CountryModel });
			var productCats = Bb.Collection.extend({ model: ProductModel });
			var infoCats = Bb.Collection.extend({ model: InfoCatModel });
			var qualityVideos = Bb.Collection.extend({ model: QualityVideoModel });
			var qualityDubbings = Bb.Collection.extend({model: QualityDubbingModel });
			var humans = Bb.Collection.extend({ model: HumanModel });

			window.app.col.countries = new countries(window.outer_countries);
			window.app.col.productCats = new productCats(window.outer_productCats);
			window.app.col.infoCats = new infoCats(window.outer_infoCats);
			window.app.col.qualityVideos = new qualityVideos(window.outer_qualityVideos);
			window.app.col.qualityDubbings = new qualityDubbings(window.outer_qualityDubbings);
			window.app.col.humans = new humans(window.outer_humans);
			
			app0.start();
		});
	}
);