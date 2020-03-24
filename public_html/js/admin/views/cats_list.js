/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette',
			
			'text!templates/row_empty.html',
			'text!templates/row_preloader.html',
			
			'text!templates/cats_list.html',
			'text!templates/cats_list_row.html'
		], 
	function($,			_,			Bb,			Mn,
			tplRowEmpty,
			tplRowPreloader,
			
			tplCatsList,
			tplCatsListRow
													){
		// создадим вид списка
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tplCatsList),
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
			initialize: function(){
			},
			onRender: function(){
				if(window.app.col.productCats.length === 0){
					var target = Mn.View.extend({
						tagName: 'tbody',
						template: _.template(tplRowEmpty)
					});

				} else {
					var RowItemView = Mn.View.extend({
						tagName: 'tr',
						template: _.template(tplCatsListRow)
					});

					var target = Mn.CollectionView.extend({
						tagName: 'tbody',
						collection: window.app.col.productCats,
						childView: RowItemView
					});
				}
				
				this.showChildView('targetRegion', new target());
			}
		});
	
		return output;
	}
);