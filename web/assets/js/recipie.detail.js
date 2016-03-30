var QuantityMapping = {
		'0.25' : '1/4',
		'0.5' : '1/2',
		'0.75' : '3/4',
}


var RecipieDetail = {
		
	toggle : function() {
		$(".quantityChange").toggle();
	},
	
	init : function() {
		$(".quantityChange > button").click();
	},
	
	reset : function(target) {
		var spanSelector = "span[data-current-quantity]";

		var btn = $(target || event.target);
		var ctn = btn.parents("dl:first");
		var quantities = ctn.find(spanSelector);
		
		var that = this;
		$.each(quantities, function(){
			var field = $(this);
			var current = field.data("current-quantity");
			var val = field.data("default-quantity");
			that.updateQuantityField(field, val);
		});
	},
	
	updateQuantityField : function(field, val) {
		// Grand chiffre
		var rv = Math.round(val);
		if (val > 10) {
			val = rv;
		} else {
			val = Math.round(val * 100) / 100;
		}
		
		var text = val;
		
		// Chiffre à virgule
		if(rv != val) {
			var fv = Math.floor(val);
			var rest = val - fv;
			var trans = QuantityMapping[''+rest];
			if(trans) {
				text = (fv > 0 ? (fv + " et ") : "") + trans; 
			}
		}
		
		field.text(text);
		field.data("current-quantity", val);
	},
	
	changeQuantity : function(target, add) {
		var spanSelector = "span[data-current-quantity]";

		var btn = $(target || event.target);
		var quantity = btn.parents("dd:first").find(spanSelector).data("default-quantity");
		var ctn = btn.parents("dl:first");
		var quantities = ctn.find(spanSelector);

		var p = 0;
		var rv = Math.floor(quantity);
		if (quantity < 1) {
			p = 100;
		} else if (quantity != rv) {
			p = 100 / quantity * (quantity - rv);
		} else if (quantity < 10) {
			p = 100 / quantity;
		} else if (quantity % 25 == 0) {
			p = 100 / (quantity / 25);
		} else if (quantity % 10 == 0) {
			p = 100 / (quantity / 10);
		} else if (quantity % 5 == 0) {
			p = 100 / (quantity / 5);
		} else {
			p = 1;
		}
		
		if (!add) {
			p = p * -1;
		}

		var that = this;
		$.each(quantities, function() {
			var field = $(this);
			var current = field.data("current-quantity");
			var val = current + (field.data("default-quantity") * p / 100);
			console.log("current", current, "val", val);			that.updateQuantityField(field, val);
		})

	}
};