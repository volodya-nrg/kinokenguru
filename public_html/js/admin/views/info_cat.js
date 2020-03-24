/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', "ckeditor.jquery",
			'text!templates/info_cat.html',
			'models/info_cat',
			'models/btns_cud',
			'views/btns_cud'
		], 
	function($,			_,			Bb,			Mn,						ckeditor,
			tpl,
			InfoCatModel,
			btnsCudModel,
			btnsCudView
													){										
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tpl),
			ui: {
				inputName: "input[name=name]",
				textareaDescription: "textarea[name=description]",
				inputPos: "input[name=pos]",
				selectParentId: "select[name=parent_id]",
				checkboxIsHide: 'input[name=is_hide]'
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
			write: function(viewChild){
				var m = this.model;
				m.set({
					name: $.trim(this.getUI('inputName').val()),
					description: $.trim(this.getUI('textareaDescription').val()),
					pos: parseInt($.trim(this.getUI('inputPos').val())),
					parent_id: parseInt(this.getUI('selectParentId').val()),
					is_hide: (this.getUI('checkboxIsHide').is(':checked')? 1: 0)
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
							
							// тут надо обновить коллекцию с учетом месторасположения категории
							var parent_id = model.get('parent_id');
							var reciver = [];
							var is_model_pushed = false;
							_.each(window.app.col.infoCats.models, function(tmp_model){
								reciver.push(tmp_model);
								
								if(tmp_model.get('id') === parent_id){
									reciver.push(model);
									is_model_pushed = true;
								}
							});
							
							// если это новая, не имеющая ни одно родителя, категория, то ее поместим в конец
							if(is_model_pushed === false){
								reciver.push(model);
							}
							
							var tmpInfoCats = Bb.Collection.extend({ model: InfoCatModel });
							window.app.col.infoCats = new tmpInfoCats(reciver);
							
							delete tmpInfoCats;
							
							Bb.history.navigate('/admin/info-cats', {trigger: true});
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
						
						// response - массив id-шек
						// удалим модели, включаяя потомков (если есть) 
						window.app.col.infoCats.remove(response);
						Bb.history.navigate('/admin/info-cats', {trigger: true});
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