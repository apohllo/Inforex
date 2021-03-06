function callFunction(fn){
	if(fn && $.isFunction(fn)){
		fn();
	}
}

function handleError(error_code, error_msg, errorCallback, loginCallback, error_data){
	if (error_code == "ERROR_AUTHORIZATION" && loginCallback){
			loginForm(false, function(success){
				if (success){
					if (loginCallback && $.isFunction(loginCallback)){
						loginCallback();
					}
				}else{
					if (errorCallback && $.isFunction(errorCallback)){
						errorCallback();
					}
					$dialogObj = $(".deleteDialog");
					if ($dialogObj.length>0){
						$dialogObj.dialog("destroy").remove();
					}
				}
			});
	} else if(error_code == "ERROR_AUTHORIZATION"){
        generateAccessErrorModal(error_data);
	} else if (error_code == "ERROR_APPLICATION"){
		generateErrorModal("Application error: ", error_msg, error_code, errorCallback);
	} else if (error_code == "ERROR_TRANSMISSION"){
        generateErrorModal("Data transmission error: ", error_msg, error_code, errorCallback);
	} else if (error_code == "ERROR_USER_DATA"){
		generateErrorModal(error_msg, null, error_code, errorCallback);
	} else {
        generateErrorModal("Unknown error type ["+error_code+"]: ", error_msg, error_code, errorCallback);
	}
}

function errorWrapper(message, errorCallback){
	if(errorCallback && $.isFunction(errorCallback)){
		errorCallback();
	}

}

function successWrapper(data, success, error, loginCallback){
	if(!data){
		handleError("ERROR_TRANSMISSION", "Empty response", error, loginCallback);
	}
	else if(data['error']){
		handleError(data['error_code'], data['error_msg'], error, loginCallback, data);
	}
	else{
		if(success && $.isFunction(success))
			success(data['result']);
	}
}

function showLoader(loaderElement){
	if(loaderElement && $.isFunction(loaderElement.addClass))
		loaderElement.addClass("loading");
}

function hideLoader(loaderElement){
	if(loaderElement && $.isFunction(loaderElement.removeClass))
		loaderElement.removeClass("loading");
}

/**
 * Hides the element and show ajax indicator.
 * @param element
 */
function ajaxIndicatorShow(element){
    var html = "<img class='ajax-indicator' src='gfx/ajax.gif'/>";
    $(element).hide();
    $(element).after(html);
}

/**
 * Show the element and hide the ajax indicator.
 * @param element
 */
function ajaxIndicatorHide(element){
    $(element).next(".ajax-indicator").remove();
    $(element).show();
}

/**
 * Dodane dla płynnego przejścia z "ajaxErrorHandler'a"
 * @param action - wywoływana akcja (dawniej parametr "ajax")
 * @param params - parametry wywołania akcji
 * @param success - callback dla pomyślnego wywołania
 * @param loginCallback - callback wywoływany w przypadku konieczności zalogowania się
 * po pomyślnym zalogowaniu
 */
function doAjaxSyncWithLogin(action, params, success, loginCallback){
	doAjaxSync(action, params, success, null, null, null, loginCallback);
}

/**
 * vide doAjaxSyncWithLogin - wersja asynchroniczna
 */
function doAjaxWithLogin(action, params, success, loginCallback){
	doAjax(action, params, success, null, null, null, loginCallback);
}

/**
 * vide doAjax - opakowanie wywołania wersji synchronicznej
 */
function doAjaxSync(action, params, success, error, complete, loaderElement, loginCallback){
	doAjax(action, params, success, error, complete, loaderElement, loginCallback, true);
}

/**
 * Wywołanie żądania AJAX'owego
 * @param action - wywoływana akcja (dawniej parametr "ajax")
 * @param params - parametry wywołania akcji
 * @param success - callback dla pomyślnego wywołania
 * @param error - callback dla błędów
 * @param complete - callback po otrzymaniu i przetworzeniu odpowiedzi
 * @param loaderElement - element HTML, któremu zostanie nadana klasa "loading"
 * - dla wyświetlania loaderów
 * @param loginCallback - callback wywoływany w przypadku konieczności zalogowania się
 * po pomyślnym zalogowaniu
 * @param sync - określa czy żądanie ma być wykonane synchronicznie (domyślnie asynchroniczne)
 */
function doAjax(action, params, success, error, complete, loaderElement, loginCallback, sync){
	params['ajax'] = action;
	var async = !sync;

	var urlParams = "";

	if(params['url']){
		urlParams = "?"+params['url'];
		params['url'] = null;
	}

	showLoader(loaderElement);

	$.ajax({

		async:  async,
		type: 	'POST',
		url: 	"index.php"+urlParams,
		data:	params,
		success: function(data){
			successWrapper(data, success, error, loginCallback)
		},
		error: function(request, textStatus, errorThrown){
			handleError("ERROR_TRANSMISSION", request.responseText, error, loginCallback);
		},
		complete: function(){
			hideLoader(loaderElement);
			callFunction(complete);
		},
		dataType:"json"				
	});

	
}

