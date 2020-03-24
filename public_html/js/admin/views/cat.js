/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', "ckeditor.jquery",
			'text!templates/cat.html',
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
				inputSlug: 'input[name=slug]',
				inputName: 'input[name=name]',
				inputTitle: 'input[name=title]',
				textareaDescription: 'textarea[name=description]'
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
					model: this.model.has('id')? new btnsCudModel({is_new: 0}): new btnsCudModel()
				}));
				
				this.getUI('textareaDescription').ckeditor();
			},
			write: function(e){
				this.model.set({
					slug: $.trim(this.getUI('inputSlug').val()),
					name: $.trim(this.getUI('inputName').val()),
					title: $.trim(this.getUI('inputTitle').val()),
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
							
							window.app.col.productCats.set(model, {remove: false});
							Bb.history.navigate('/admin/cats', {trigger: true});
						},
						error: function(model, response){
							$('#preloader').hide();
							e.getUI('btnUpdate').prop('disabled', false);
							
							alert(response.responseJSON.error);
						},
						wait: true
					});
				}
			},
			delete: function(e){
				$('#preloader').show();
				e.getUI('btnDelete').prop('disabled', true);
				
				this.model.destroy({
					success: function(model, response){
						$('#preloader').hide();
						e.getUI('btnDelete').prop('disabled', false);
							
						window.app.col.productCats.remove(model);
						Bb.history.navigate('/admin/cats', {trigger: true});
					},
					error: function(model, response){
						$('#preloader').hide();
						e.getUI('btnDelete').prop('disabled', false);
					},
					wait: true
				});
			}
		});
		
		return output;
	}
);