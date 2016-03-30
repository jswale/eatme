var UnitMapping = {
		'sucre vanillé' : 'sachet',
		'levure' : 'sachet',
		
		'lait' : 'ml',
		'eau' : 'ml',
		'huile' : 'ml',
		
		'poudre' : 'g',
		'farine' : 'g',
		'maizena' : 'g',
		'maïzena' : 'g',
		'beurre' : 'g',
		'sucre' : 'g',
		'cassonade' : 'g',
		'miel' : 'g',
		'chocolat' : 'g',
		'fromage' : 'g',
		'pâte' : 'g',
		'pate' : 'g',
		'sauce' : 'g',
		
		'poulet' : 'g',
		'veau' : 'g',
		'porc' : 'g',
		'poisson' : 'g',
		
		'sel' : 'pincée',		
}

var RecipieManager = {
		
		counter : {
			image : 0,
			timer : 0,
			step : 0,
			stepGroup : 0,
			ingredient : 0,
			ingredientGroup : 0,
		},
		
		addImage : function (image) {
			var internal_counter = this.counter.image++;
			
			var tbody = $("div.images > table > tbody");
			
			$.ajax({
			  method: "POST",
			  url: recipieImageLineTemplateUrl,
			  data: { 
			  	counter: internal_counter,
			  	id : image && image.id || -1,
			  	name : image && image.name || ""
			  }
			}).done(function( data ) {
			  var line = $(data);
			  tbody.append(line);
			  
			  if(!image) {
					line.find("input[type='text']:empty").focus();			
				}			  
			}); 
		},
		
		addTimer : function (timer) {
			var internal_counter = this.counter.timer++;
			
			var tbody = $("div.timers > table > tbody");
			
			$.ajax({
			  method: "POST",
			  url: recipieTimerLineTemplateUrl,
			  data: { 
			  	counter: internal_counter,
			  	id : timer && timer.id || -1,
			  	name : timer && timer.name || "",
			  	value : timer && timer.value || "",
			  }
			}).done(function( data ) {
			  var line = $(data);
			  tbody.append(line);
			  
			  if(!timer) {
					line.find("input[type='text']:empty").focus();			
				}			  
			}); 
		},

		addIngredientGroup : function(ingredientGroup) {
			var internal_counter = this.counter.ingredientGroup++;
			
			$.ajax({
			  method: "POST",
			  url: recipieIngredientGroupTemplateUrl,
			  data: { 
			  	counter: internal_counter,
			  	id : ingredientGroup && ingredientGroup.id || -1,
			  	name : ingredientGroup ? ingredientGroup.name : "",
			  }
			}).done(function( data ) {
			  var ingredientGroupCtn = $(data);
			  $("div.ingredientGroups").append(ingredientGroupCtn);
			  
				if(ingredientGroup) {
					$.each(ingredientGroup.ingredients, function(i, ingredient) {
						RecipieManager.addIngredient(internal_counter, ingredient);
					});		
				} else {
					RecipieManager.addIngredient(internal_counter);
					ingredientGroupCtn.find("input[type='text']:first").focus();	
				}			  
			}); 			
		},		
		
		bindIngredientNameField : function(field) {
			field.on('change', function() {
				var unitField = field.parents("tr:first").find("[data-field='unit']");
				if(! unitField.val() ) {
					var name = field.val().toLowerCase();											
					$.each(UnitMapping, function(keyword, unit) {
						if(name.indexOf(keyword) != -1) {
							unitField.val(unit);
							return false;
						}
					})											
				}										
			});
		},

		addIngredient : function(ingredientGroupId, ingredient) {
			var internal_counter = this.counter.ingredient++;
			var parent = $("[data-ingredientGroup-id=" + ingredientGroupId + "]");
			
			var tbody = parent.find("table > tbody");

			$.ajax({
			  method: "POST",
			  url: recipieIngredientLineTemplateUrl,
			  data: { 
			  	counter: internal_counter,
			  	id : ingredient && ingredient.id || -1,
			  	group : ingredientGroupId,
			  	name : ingredient && ingredient.name || "",
			  	quantity : ingredient && ingredient.quantity || "",
			  	unit : ingredient && ingredient.unit || "",
			  	ref : ingredient && ingredient.ref || "",
			  }
			}).done(function( data ) {
			  var line = $(data);
			  tbody.append(line);
			  
			  RecipieManager.bindIngredientNameField(line.find("[data-field='name']"));
			  
			  if(!ingredient) {
			  	line.find("[data-field='name']").focus();			
				}			  
			});			
		},		

		addStepGroup : function(stepGroup) {
			var internal_counter = this.counter.stepGroup++;
			
			$.ajax({
			  method: "POST",
			  url: recipieStepGroupTemplateUrl,
			  data: { 
			  	counter: internal_counter,
			  	id : stepGroup && stepGroup.id || -1,
			  	name : stepGroup ? stepGroup.name : ""
			  }
			}).done(function( data ) {
			  var stepGroupCtn = $(data);
			  $("div.stepGroups").append(stepGroupCtn);
			  
				if(stepGroup) {
					$.each(stepGroup.steps, function(i, step) {
						RecipieManager.addStep(internal_counter, step);
					});		
				} else {
					RecipieManager.addStep(internal_counter);
					stepGroupCtn.find("input[type='text']:first").focus();	
				}			  
			}); 			
		},
		
		addStep : function(stepGroupId, step) {
			var internal_counter = this.counter.step++;
			var parent = $("[data-stepGroup-id=" + stepGroupId + "]");
			
			var tbody = parent.find("table > tbody");

			$.ajax({
			  method: "POST",
			  url: recipieStepLineTemplateUrl,
			  data: { 
			  	counter: internal_counter,
			  	id : step && step.id || -1,
			  	group : stepGroupId,
			  	description : step && step.description || "",
			  }
			}).done(function( data ) {
			  var line = $(data);
			  tbody.append(line);
			  
			  if(!step) {
			  	line.find("[data-field='description']").focus();			
				}			  
			});
		},		

		deleteParent : function(target, selector) {
			$(target || event.target).parents(selector+":first").remove();
		},
		
		init : function() {
			$.each($.find(".ingredientGroup > table [data-field='name']"), function(){
				RecipieManager.bindIngredientNameField($(this));				
			});
		}
		
};