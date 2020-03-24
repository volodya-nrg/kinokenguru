/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette', "ckeditor.jquery",
			'text!templates/product.html',
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
			myFiles: [],
			ui: {
				inputFile: 'input[type=file]',
				inputVideoFile: 'input[name=video_file]',
				inputName: 'input[name=name]',
				inputNameOriginal: 'input[name=name_original]',
				textareaDescription: 'textarea[name=description]',
				inputLink: 'input[name=link]',
				inputDuration: 'input[name=duration]',
				inputYear: 'input[name=year]',
				inputRatingImdb: 'input[name=rating_imdb]',
				inputRatingKinopoisk: 'input[name=rating_kinopoisk]',
				inputBudget: 'input[name=budget]',
				inputImages: 'input[name="old_images[]"]',
				inputSlogan: 'input[name=slogan]',
				
				selectQualityVideo: 'select[name=quality_video_id]',
				selectQualityВubbing: 'select[name=quality_dubbing_id]',
				selectOld: 'select[name=old]',
				
				checkboxIsHide: 'input[name=is_hide]',
				checkboxInQueue: 'input[name=in_queue]',
				
				multiselectIconPlus: '.multiselect-tpl-plus .fa',
				multiselectIconMinus: '.multiselect-tpl-minus .fa',
				multiselectIconClose: '.multiselect-item-close .fa',
				
				contriesVarPlace: '#contries-var-place',
				producersVarPlace: '#producers-var-place',
				actorsVarPlace: '#actors-var-place',
				trailersVarPlace: '#trailers-var-place',
				catsVarPlace: '#cats-var-place',
				
				closeThumb: '.img-thumbnail-close-thumb',
				closeFrame: '.img-thumbnail-close-frame'
			},
			events: {
				'click @ui.closeThumb': function(e){
					removeThumbnail(e, this); // global fn
				},
				'click @ui.closeFrame': function(e){
					removeFrames(e, this); // global fn
				},
				'change @ui.inputFile': function(){
					handleFiles(this); // global fn
				},
				'click @ui.multiselectIconPlus': function(e){
					multiselectToggle(e, '+'); // global fn
				},
				'click @ui.multiselectIconMinus': function(e){
					multiselectToggle(e, '-'); // global fn
				},
				'click @ui.multiselectIconClose': function(e){
					$(e.currentTarget).parent().parent().remove();
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
				
				this.getUI('textareaDescription').ckeditor();
			},
			write: function(e){
				var aContries = [];
				var aProducers = [];
				var aActors = [];
				var aTrailers = [];
				var aCats = [];
				
				_.each(this.$('input[name="countries[]"]'), function(item, key){
					aContries.push(parseInt($(item).val()));
				});
				_.each(this.getUI('contriesVarPlace').find('select'), function(select, key){
					aContries.push(parseInt($(select).val()));
				});
				
				_.each(this.$('input[name="producers[]"]'), function(item, key){
					aProducers.push(parseInt($(item).val()));
				});
				_.each(this.getUI('producersVarPlace').find('select'), function(select, key){
					aProducers.push(parseInt($(select).val()));
				});
				
				_.each(this.$('input[name="actors[]"]'), function(item, key){
					aActors.push(parseInt($(item).val()));
				});
				_.each(this.getUI('actorsVarPlace').find('select'), function(select, key){
					aActors.push(parseInt($(select).val()));
				});
				
				_.each(this.$('input[name="cats[]"]'), function(item, key){
					aCats.push(parseInt($(item).val()));
				});
				_.each(this.getUI('catsVarPlace').find('select'), function(select, key){
					aCats.push(parseInt($(select).val()));
				});
				
				_.each(this.getUI('trailersVarPlace').find('input[name="trailers[]"]'), function(item, key){
					aTrailers.push($.trim($(item).val()));
				});
				
				// images, frames - автоматически удаляются при закрытии того или иного файла
				var m = this.model;
				m.set({
					name: $.trim(this.getUI('inputName').val()),
					name_original: $.trim(this.getUI('inputNameOriginal').val()),
					year: $.trim(this.getUI('inputYear').val()),
					link: $.trim(this.getUI('inputLink').val()),
					duration: $.trim(this.getUI('inputDuration').val()),
					description: $.trim(this.getUI('textareaDescription').val()),
					quality_video_id: this.getUI('selectQualityVideo').val(),
					quality_dubbing_id: this.getUI('selectQualityВubbing').val(),
					budget: $.trim(this.getUI('inputBudget').val()), 
					rating_imdb: $.trim(this.getUI('inputRatingImdb').val()),
					rating_kinopoisk: $.trim(this.getUI('inputRatingKinopoisk').val()),
					video_file: $.trim(this.getUI('inputVideoFile').val()),
					slogan: $.trim(this.getUI('inputSlogan').val()),
					old: this.getUI('selectOld').val(),

					is_hide: (this.getUI('checkboxIsHide').is(':checked')? 1: 0),
					in_queue: (this.getUI('checkboxInQueue').is(':checked')? 1: 0),
					
					/* arrays */
					files: this.myFiles,
					countries: aContries,
					trailers: aTrailers,
					producers: aProducers,
					actors: aActors,
					cats: aCats
				});
				
				var is_new = m.isNew()? 1: 0;
				
				if(m.isValid() === false){
					alert(m.validationError);

				} else {
					$('#preloader').show();
					e.getUI('btnUpdate').prop('disabled', true);
					
					m.save(null, {
						success: function(model, response){
							$('#preloader').hide();
							e.getUI('btnUpdate').prop('disabled', false);
							
							window.app.view.productsList.collection.set(model, {remove: false});
							
							if(is_new){
								window.app.view.productsList.collection.sort();
							}
							
							Bb.history.navigate('/admin/products', {trigger: true});
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
							
						window.app.view.productsList.collection.remove(model);
						Bb.history.navigate('/admin/products', {trigger: true});
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