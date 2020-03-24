/* view */

define([	'jquery', 'underscore', 'backbone', 'backbone.marionette',
			
			'text!templates/row_empty.html',
			'text!templates/row_preloader.html',
			
			'text!templates/quality_dubbings_list.html',
			'text!templates/quality_dubbings_list_row.html'
		], 
	function($,			_,			Bb,			Mn,
			tplRowEmpty,
			tplRowPreloader,
			
			tplQualityDubbingsList,
			tplQualityDubbingsListRow
													){
		// создадим вид списка
		var output = Mn.View.extend({
			el: '#app',
			template: _.template(tplQualityDubbingsList),
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
				if(window.app.col.qualityDubbings.length === 0){
					var target = Mn.View.extend({
						tagName: 'tbody',
						template: _.template(tplRowEmpty)
					});

				} else {
					var RowItemView = Mn.View.extend({
						tagName: 'tr',
						template: _.template(tplQualityDubbingsListRow)
					});

					var target = Mn.CollectionView.extend({
						tagName: 'tbody',
						collection: window.app.col.qualityDubbings,
						childView: RowItemView
					});
				}
				
				this.showChildView('targetRegion', new target());
			}
		});
	
		return output;
	}
);