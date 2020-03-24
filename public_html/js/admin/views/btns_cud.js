/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette',
			'text!templates/btns_cud.html'
		], 
	function($,			_,			Bb,			Mn,
			tpl
													){
		var output = Mn.View.extend({
			template: _.template(tpl),
			tagName: 'table',
			attributes: {
				border: 0,
				cellspacing: 0, 
				cellpadding: 0,
				width: "100%"
			},
			ui: {
				btnUpdate: '.btn-update',
				btnDelete: '.btn-delete'
			},
			triggers: {
				'click @ui.btnUpdate' : 'triggerBtnWrite',
				'click @ui.btnDelete' : 'triggerBtnDelete'
			},
			modelEvents: {
			},
			events: {
			},
			initialize: function(){
			},
			onRender: function(){
			}
		});
		
		return output;
	}
);