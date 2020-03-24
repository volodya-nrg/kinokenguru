/* collection */

define([	'backbone', 'models/product'], 
	function(Bb,			ProductModel)
	{
		var col = Bb.Collection.extend({
			model: ProductModel,
			url: '/admin/get-json-products-list',
			sortParam: 'id',
			sortMode: 1,
			comparator: function(a, b){
				if(a.get(this.sortParam) > b.get(this.sortParam)){
					return -1 * this.sortMode;	
				}
				if(a.get(this.sortParam) < b.get(this.sortParam)){
					return this.sortMode;	
				}

				return 0;
			}
		});

		return col;
	}
);