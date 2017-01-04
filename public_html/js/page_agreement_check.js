/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$("a.filter_by_category_name").click(function(){		
		$("#agreement td").css("background",  "");
		$(this).closest("table").find("td").css("background",  "");
		$(this).closest("tr").children("td").css("background",  "#ffcccc");
		$("#agreement td." + $(this).text()).parent("tr").children("td").css("background",  "#ffcccc");
	});	
});