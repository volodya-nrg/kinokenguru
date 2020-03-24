/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', "ckeditor.jquery",
			'text!templates/info.html',
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
				inputTitle: "input[name=title]",
				inputH1: "input[name=h1]",
				textareaDescription: "textarea[name=description]",
				checkboxIsHide: 'input[name=is_hide]',
				textareaMetaKeywords: "textarea[name=meta_keywords]",
				textareaMetaDesc: "textarea[name=meta_desc]",
				selectCatId: "select[name=cat_id]"
			},
			events: {
			},
			modelEvents: {
			},
			childViewEvents: {
				'triggerBtnWrite': 'write',
				'triggerBtnDelete': 'delete'
			},
			regions: {
				regionBtns: '#btns-cud'
			},
			initialize: function(){
			},
			onRender: function(){
				// обновим кнопки
				this.showChildView('regionBtns', new btnsCudView({
					model: new btnsCudModel({is_new: this.model.has('id')? 0: 1})
				}));
				
				this.getUI('textareaDescription').ckeditor();
			},
			templateContext: function(){
				return {
					modelIsNew: this.model.isNew()
				};
			},
			write: function(viewChild){
				var m = this.model;
				m.set({
					title: $.trim(this.getUI('inputTitle').val()),
					h1: $.trim(this.getUI('inputH1').val()),
					description: $.trim(this.getUI('textareaDescription').val()),
					is_hide: (this.getUI('checkboxIsHide').is(':checked')? 1: 0),
					meta_keywords: $.trim(this.getUI('textareaMetaKeywords').val()),
					meta_desc: $.trim(this.getUI('textareaMetaDesc').val()),
					cat_id: this.getUI('selectCatId').val()
				});
				
				if(m.isValid() === false){
					alert(m.validationError);

				} else {
					$('#preloader').show();
					viewChild.getUI('btnUpdate').prop('disabled', true);
					
					m.save(null, {
						success: function(model, response){
							$('#preloader').hide();
							viewChild.getUI('btnUpdate').prop('disabled', false);
							
							window.app.view.infosList.collection.set(model, {remove: false});
							Bb.history.navigate('/admin/infos', {trigger: true});
						},
						error: function(model, response){
							$('#preloader').hide();
							viewChild.getUI('btnUpdate').prop('disabled', false);
							
							alert(response.responseJSON.error);
						},
						wait: true
					});
				}
			},
			delete: function(viewChild){
				var m = this.model;
				
				$('#preloader').show();
				viewChild.getUI('btnDelete').prop('disabled', true);
				
				m.destroy({
					success: function(model, response){
						$('#preloader').hide();
						viewChild.getUI('btnDelete').prop('disabled', false);
						
						window.app.view.infosList.collection.remove(model);
						Bb.history.navigate('/admin/infos', {trigger: true});
					},
					error: function(model, response){
						$('#preloader').hide();
						viewChild.getUI('btnDelete').prop('disabled', false);
						
						alert(response.responseJSON.error);
					},
					wait: true
				});
			}
		});
		
		return output;
	}
);