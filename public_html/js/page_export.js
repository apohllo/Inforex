/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
	
	$(".flags img").live("click", function(){
		$(this).toggleClass("selected");
	});
	
	$("i.close").live("click", function(){
		$(this).parent().remove();
	});
	
	$(".new_selector").click(function(){
		var form = $(".flag_template").html();		
		$("td.flags").append(form);		
	});

	$(".new_extractor").click(function(){
		var form = $(".extractor_template").html();		
		$("td.extractors").append(form);		
	});
	
	$("#newExportButton").click(function(){
		$(this).hide();
		$("#newExportForm").show();
		$("#history").hide();
	});

	$("#cancel").click(function(){
		$("#newExportButton").show();
		$("#newExportForm").hide();
		$("#history").show();
	});

	$("#export").click(function(){
		$(".instant_error").remove();
		
		var description = $("textarea[name=description]").val().trim();
		if ( description.length == 0 ){
			$("textarea[name=description]").after(get_instante_error_box("Enter description of the export"));
		}
		
		var selectors = collect_selectors();		
		var extractors = collect_extractors();
		
		if ( $(".instant_error").size() > 0 ){
			$(".buttons").append(get_instante_error_box("There were some errors. Please correct them first before submitting the form."))
		}
		else{
			submit_new_export(description, selectors, extractors, "");
		}
	});
});

/**
 * 
 * @param description
 * @param selectors
 * @param extractors
 * @param indices
 * @returns
 */
function submit_new_export(description, selectors, extractors, indices){
	var params = {};	
	params['url'] = $.url(window.location.href).attr("query");
	params['description'] = description;
	params['selectors'] = selectors;
	params['extractors'] = extractors;
	params['indices'] = indices;
	doAjaxWithLogin("export_new", params, function(){
		window.location.reload(true);
	}, function(){
		submit_new_export(description, selectors, extractors, indices);
	});
	
}

/**
 * Zbiera opis zdefiniowanych selectorów.
 * @returns
 */
function collect_selectors(){
	var url = $.url(window.location.href);
	var corpus_id = url.param("corpus");
	var selectors = "";
	$("td.flags div.flags").each(function(){		
		selectors += (selectors.length > 0 ? "\n" : "") + "corpus_id=" + corpus_id + "&flag:" + parse_flag(this);
	});
	if ( selectors.length == 0){
		selectors = "corpus_id=" + corpus_id;
	}
	return selectors;
}

/**
 * Zbiera opisy zdefiniowanych ekstraktorów treści.
 * @returns
 */
function collect_extractors(){
	var extractors = "";
	$("td.extractors div.extractor").each(function(){
		var flag = parse_flag($(this).find("div.flags"));
		var elements = "";
		$(this).find("div.elements .annotation_layers_and_subsets input.group_cb:checked").each(function(){
			elements += (elements.length>0?"&":"") + "annotation_set_id=" + $(this).val(); 
		});
		$(this).find("div.elements .annotation_layers_and_subsets input.subset_cb:checked").each(function(){
			elements += (elements.length>0?"&":"") + "annotation_subset_id=" + $(this).val(); 
		});
		
		if ( elements.length == 0 ){
			$(this).append(get_instante_error_box("No elements to expert were defined"));					
		}
		else{
			extractors += (extractors.length > 0 ? "\n" : "") + flag + ":" + elements;
		}
	});
	return extractors;
}

/**
 * Parsuje obiekt reprezentujący formularz opisujący wartości wybranej flagi
 * @param flag -- obiekt dom 
 */
function parse_flag(flag){
	if ($(flag).find("select[name=corpus_flag_id] option:selected").val() == ""){
		$(flag).append(get_instante_error_box("Flag name not set"));
	}
	var name = $(flag).find("select[name=corpus_flag_id] option:selected").text();		
	var values = "";
	$(flag).find("img.selected").each(function(){
		values += (values.length > 0 ? "," : "") + $(this).attr("value");
	});
	if ( values == "" ){
		$(flag).append(get_instante_error_box("Flag value(s) not set"));		
	}
	return name + "=" + values;
}

/**
 * 
 * @param msg
 * @returns
 */
function get_instante_error_box(msg){
	return "<div class='error instant_error ui-corner-all ui-state-error'>" + msg +"</div>"
}