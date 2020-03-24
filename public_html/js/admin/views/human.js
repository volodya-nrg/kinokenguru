/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', "ckeditor.jquery", 'inputMask',
			'text!templates/human.html',
			'models/btns_cud',
			'views/btns_cud'
		], 
	function($,			_,			Bb,			Mn,						ckeditor,		   inputMask,	
			tpl,
			btnsCudModel,
			btnsCudView
													){										
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tpl),
			myFiles: [],
			ui: {
				inputFile: 'input[type=file]',
				inputFioRu: 'input[name=fio_ru]',
				inputFioOriginal: 'input[name=fio_original]',
				inputAbout: 'textarea[name=about]',
				inputBirthday: 'input[name=birthday]',
				inputImages: 'input[name="old_images[]"]',
				selectCountryId: 'select[name=country_id]',
				closeThumb: '.img-thumbnail-close'
			},
			events: {
				'click @ui.closeThumb': function(e){
					removeThumbnail(e, this); // global fn
				},
				'change @ui.inputFile': function(){
					handleFiles(this); // global fn
				}
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
				
				this.getUI('inputAbout').ckeditor();
				this.getUI('inputBirthday').mask("9999.99.99", {placeholder: "гггг.мм.дд"});
			},
			write: function(e){
				var m = this.model;
				m.set({
					fio_ru: $.trim(this.getUI('inputFioRu').val()),
					fio_original: $.trim(this.getUI('inputFioOriginal').val()),
					about: $.trim(this.getUI('inputAbout').val()),
					birthday: $.trim(this.getUI('inputBirthday').val()),
					country_id: this.getUI('selectCountryId').val(),
					
					/* arrays */
					files: this.myFiles
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
							
							window.app.col.humans.set(model, {remove: false});
							Bb.history.navigate('/admin/humans', {trigger: true});
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
							
						window.app.col.humans.remove(model);
						Bb.history.navigate('/admin/humans', {trigger: true});
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