/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', 
			'text!templates/quality_video.html',
			'models/btns_cud',
			'views/btns_cud'
		], 
	function($,			_,			Bb,			Mn,					
			tplQualityVideo,
			btnsCudModel,
			btnsCudView
													){
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tplQualityVideo),
			ui: {
				inputName: 'input[name=name]',
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
					model: new btnsCudModel({is_new: this.model.has('id')? 0: 1})
				}));
			},
			write: function(e){
				this.model.set({
					name: $.trim(this.getUI('inputName').val()),
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
							
							// {remove: false} - из коллекции не удаляем отстальное
							window.app.col.qualityVideos.set(model, {remove: false});
							Bb.history.navigate('/admin/quality-videos', {trigger: true});
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
						
						window.app.col.qualityVideos.remove(model);
						Bb.history.navigate('/admin/quality-videos', {trigger: true});
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