/* model */

define([	'backbone'], 
	function(Bb)
	{
		var output = Bb.Model.extend({
			defaults: {
				is_new: 1
			}
		});
	
		return output;
	}
);