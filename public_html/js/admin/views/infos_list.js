/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette',
			
			'text!templates/row_empty.html',
			'text!templates/row_preloader.html',
			
			'text!templates/infos_list.html',
			'text!templates/infos_list_row.html',
			
			'collections/infos_list'
		], 
	function($,			_,			Bb,			Mn,
			tplRowEmpty,
			tplRowPreloader,
			
			tplInfosList,
			tplInfosListRow,
			
			InfosListCol
													){				
		// создадим вид списка
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tplInfosList),
			ui: {
			},
			events: {
			},
			regions: {
				targetRegion: {
					el: '.table-zebra tbody',
					replaceElement: true
				}
			},
			collection: new InfosListCol(),
			initialize: function(){
			},
			onRender: function(){
				if(this.collection.length === 0){
					var target = Mn.View.extend({
						tagName: 'tbody',
						template: _.template(tplRowEmpty)
					});

				} else {
					var RowItemView = Mn.View.extend({
						tagName: 'tr',
						template: _.template(tplInfosListRow)
					});

					var target = Mn.CollectionView.extend({
						tagName: 'tbody',
						collection: this.collection,
						childView: RowItemView
					});
				}
				
				this.showChildView('targetRegion', new target());
			},
			fetchRender: function(){
				var that = this;
				
				this.showPreloader(); // покажем загрузчик данных
				this.collection.fetch({
					success: function(){
						that.render();
					},
					error: function(){}
				});
			},
			showPreloader: function(){
				var PreloaderView = Mn.View.extend({
					tagName: 'tbody',
					template: _.template(tplRowPreloader)
				});
				
				this.showChildView('targetRegion', new PreloaderView());
			}
		});
	
		return output;
	}
);