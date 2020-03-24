/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', 
			'text!templates/user.html',
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
				inputName: 'input[name=name]'
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
			},
			write: function(e){
				var m = this.model;
				m.set({
					name: $.trim(this.getUI('inputName').val())
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
							
							window.app.view.usersList.collection.set(model, {remove: false});
							Bb.history.navigate('/admin/users', {trigger: true});
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