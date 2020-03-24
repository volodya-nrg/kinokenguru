/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette',
			
			'text!templates/row_empty.html',
			'text!templates/row_preloader.html',
			
			'text!templates/products_list.html',
			'text!templates/products_list_row.html',
			
			'collections/products_list'
		], 
	function($,			_,			Bb,			Mn,
			tplRowEmpty,
			tplRowPreloader,
			
			tplProductsList,
			tplProductsListRow,
			
			ProductsListCol
													){				
		// создадим вид списка
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tplProductsList),
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
			collection: new ProductsListCol(),
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
						template: _.template(tplProductsListRow)
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