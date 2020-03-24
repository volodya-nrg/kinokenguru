var app			= {};
	app.model	= {};
	app.view	= {};
	app.col		= {};
	app.router	= {};

requirejs.config({
	baseUrl: '/js/public',
	paths: {
	   // libs, пути должны быть относительные
	   jquery:					"../../vendor/jquery/jquery-3.1.1.min",
	   jqueryUI:				"../../vendor/jquery/jquery-ui.min",
	   
	   underscore:				"../../vendor/marionette/underscore",
	   backbone:				"../../vendor/marionette/backbone",
	   "backbone.radio":		"../../vendor/marionette/backbone.radio",
	   "backbone.marionette":	"../../vendor/marionette/backbone.marionette.min",
	   text:					"../../vendor/require/text",
	   domReady:				"../../vendor/require/domReady",
	   
	   // plugins
	   inputMask:				"../../vendor/jquery/jquery.maskedinput.min",
	   
	   // custom
	   custom:					"custom",
	   slick:					"../../vendor/slick-1.6.0/slick/slick.min"
	},
	shim: {
		custom: {
			deps: ['jquery', 'jqueryUI', 'slick']
		}
	}
});	

requirejs(['jquery', 'underscore', 'backbone', 'backbone.marionette', "custom", 'router'], 
	function($,		  _,			Bb,			Mn,					   custom,	 Router){
		
		// установим для каждого запроса токен
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		
		// пока нет необходимости использовать, поэтому выйдем
		return;
		
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
			app0.start();
		});
	}
);