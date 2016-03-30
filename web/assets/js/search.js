var Search = {
	addCriteria : function (name) {
		var criteria = $("<div/>")
		.css("margin-bottom", "5px")
		.addClass("form-group")
		.append(
				$("<div/>")
				.addClass("input-group")
				.append(
						$("<div/>")
						.addClass("input-group-addon")
						.css("text-transform", "capitalize")
						.text(name)
				)					
				.append(
						$("<input/>")
						.attr("type", "text")
						.addClass("form-control")
						.attr("name", name+"[]")
						.attr("placeholder", "Nom de l'ingrédient")
				)
				.append(
						$("<div/>")
						.addClass("input-group-addon")
						.css("cursor", "pointer")
						.append(
								$("<span/>")
								.addClass("glyphicon glyphicon-trash")
						)
						.on('click', function(){
							var target = $(event.target);
							target.parents(".form-group:first").remove();
						})
				)					
		); 
		$(".criterias").append(criteria);
		
		criteria.find("input").focus();
	}
};