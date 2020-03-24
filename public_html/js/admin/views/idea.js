/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', 
			'text!templates/idea.html',
			'models/btns_cud',
			'views/btns_cud'
		], 
	function($,			_,			Bb,			Mn,					
			tpl,
			btnsCudModel,
			btnsCudView
													){										
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tpl),
			myFiles: [],
			ui: {
				textareaText: 'textarea[name=text]',
				textareaAnswer: 'textarea[name=answer]'
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
					model: new btnsCudModel({is_new: 0})
				}));
			},
			write: function(e){
				var m = this.model;
				m.set({
					status: this.$('input[name=status]:checked').val(),
					text: $.trim(this.getUI('textareaText').val()),
					answer: $.trim(this.getUI('textareaAnswer').val())
				});
				
				if(m.isValid() === false){
					alert(m.validationError);

				} else {
					$('#preloader').show();
					e.getUI('btnUpdate').prop('disabled', true);
					
					m.save(null, {
						success: function(model, response){
							$('#preloader').hide();
							e.getUI('btnUpdate').prop('disabled', false);
							
							window.app.view.ideasList.collection.set(model, {remove: false});
							Bb.history.navigate('/admin/ideas', {trigger: true});
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
				var m = this.model;
				
				$('#preloader').show();
				e.getUI('btnDelete').prop('disabled', true);
				
				m.destroy({
					success: function(model, response){
						$('#preloader').hide();
						e.getUI('btnDelete').prop('disabled', false);

						window.app.view.ideasList.collection.remove(model);
						Bb.history.navigate('/admin/ideas', {trigger: true});
					},
					error: function(model, response){
						$('#preloader').hide();
						e.getUI('btnDelete').prop('disabled', false);
						
						alert(response.responseJSON.error);
					},
					wait: true
				});
			}
		});
		
		return output;
	}
);