/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', "ckeditor.jquery",
			'text!templates/page.html',
			'models/btns_cud',
			'views/btns_cud'
		], 
	function($,			_,			Bb,			Mn,						ckeditor,
			tpl,
			btnsCudModel,
			btnsCudView
													){
		
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tpl),
			ui: {
				inputTitle: 'input[name=title]',
				textareaMetaKeywords: 'textarea[name=meta_keywords]',
				textareaMetaDesc: 'textarea[name=meta_desc]',
				textareaDescription: 'textarea[name=description]'
			},
			events: {
			},
			modelEvents: {
			},
			childViewEvents: {
				'triggerBtnWrite': 'write'
			},
			regions: {
				regionBtns: '#btns-cud'
			},
			initialize: function(){
			},
			onRender: function(){
				var view = new btnsCudView({
					model: new btnsCudModel({is_new: 0}) // новой не бывает
				});
				
				// обновим кнопки
				this.showChildView('regionBtns', view);
				// выключим на всякий случай кнопку "удалить"
				view.ui.btnDelete.prop('disabled', true); 
				
				this.getUI('textareaDescription').ckeditor(); 
			},
			write: function(e){
				this.model.set({
					title: $.trim(this.getUI('inputTitle').val()),
					meta_keywords: $.trim(this.getUI('textareaMetaKeywords').val()),
					meta_desc: $.trim(this.getUI('textareaMetaDesc').val()),
					description: $.trim(this.getUI('textareaDescription').val())
				});

				if(this.model.isValid() === false){
					alert(this.model.validationError);

				} else {
					$('#preloader').show();
					e.getUI('btnUpdate').prop('disabled', true);
					
					this.model.save(null, {
						success: function(model, response){
							$('#preloader').hide();
							e.getUI('btnUpdate').prop('disabled', false);
							
							window.app.view.pagesList.collection.set(model, {remove: false});
							Bb.history.navigate('/admin/pages', {trigger: true});
						},
						error: function(model, response){
							$('#preloader').hide();
							e.getUI('btnUpdate').prop('disabled', false);
							
							alert(response.responseJSON.error);
						},
						wait: true
					});
				}
			}
		});
		
		return output;
	}
);