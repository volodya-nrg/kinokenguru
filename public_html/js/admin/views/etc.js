/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette',
			'text!templates/etc.html'
		], 
	function($,			_,			Bb,			Mn,
			tpl
													){				
		// создадим вид списка
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tpl),
			ui: {
				btnUpdateSitemap: '#btn-update-sitemap',
				btnSendNews: '#btn-send-news',
				btnClearCache: '#btn-clear-cache'
			},
			events: {
				'click @ui.btnUpdateSitemap': 'updateSitemap',
				'click @ui.btnSendNews': 'sendNews',
				'click @ui.btnClearCache': 'clearCache'
			},
			regions: {
			},
			initialize: function(){
			},
			onRender: function(){
			},
			updateSitemap: function(){
				var that = this;
				
				this.getUI('btnUpdateSitemap').prop('disabled', true);
				$('#preloader').show();
				
				$.post('/admin/update-sitemap', {}, function(response){
					that.getUI('btnUpdateSitemap').prop('disabled', false);
					$('#preloader').hide();
					
				}, 'json');
			},
			sendNews: function(){
				var that = this;
				
				this.getUI('btnSendNews').prop('disabled', true);
				$('#preloader').show();
				
				$.post('/admin/send-news', {}, function(response){
					that.getUI('btnSendNews').prop('disabled', false);
					$('#preloader').hide();
					
				}, 'json');
			},
			clearCache: function(){
				var that = this;
				
				this.getUI('btnClearCache').prop('disabled', true);
				$('#preloader').show();
				
				$.post('/admin/clear-cache', {}, function(response){
					that.getUI('btnClearCache').prop('disabled', false);
					$('#preloader').hide();
					
				}, 'json');
			},
		});
	
		return output;
	}
);