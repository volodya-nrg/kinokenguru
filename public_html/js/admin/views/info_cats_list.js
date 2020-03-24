/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette',
			
			'text!templates/row_empty.html',
			'text!templates/row_preloader.html',
			
			'text!templates/info_cats_list.html',
			'text!templates/info_cats_list_row.html'
		], 
	function($,			_,			Bb,			Mn,
			tplRowEmpty,
			tplRowPreloader,
			
			tplInfoCatsList,
			tplInfoCatsListRow
													){				
		// создадим вид списка
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tplInfoCatsList),
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
				if(window.app.col.infoCats.length === 0){
					var target = Mn.View.extend({
						tagName: 'tbody',
						template: _.template(tplRowEmpty)
					});

				} else {
					var RowItemView = Mn.View.extend({
						tagName: 'tr',
						template: _.template(tplInfoCatsListRow)
					});

					var target = Mn.CollectionView.extend({
						tagName: 'tbody',
						collection: window.app.col.infoCats,
						childView: RowItemView
					});
				}
				
				this.showChildView('targetRegion', new target());
			}
		});
	
		return output;
	}
);