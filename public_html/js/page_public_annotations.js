var url = $.url(window.location.href);
var corpus_id = url.param('corpus');


$(function() {

    $(".show_public").click(function(){

        var annotation_set_id = $(this).closest('tr').attr('id');

        getPublicCorpora(annotation_set_id);
    });

    $(".tableContent").on("click", "tbody > tr", function (element) {


        if(!$(element.target).hasClass("public_corpora_button")){
            $(this).siblings().removeClass("hightlighted");
            $(this).addClass("hightlighted");
            var containerType = $(this).parents(".tableContainer:first").attr('id');
            if (containerType == "annotationSetsContainer") {

                $('#annotationSubsetsContainer').css('visibility', 'visible');
                $("#annotationTypesContainer").css('visibility', 'hidden');

                $("#annotationSetsCorporaContainer").css('visibility', 'visibile');
                $("#corpusContainer").css('visibility', 'visible');
                $("#annotationTypesContainer span").hide();
                $("#annotationTypesContainer table > tbody").empty();
            }
            else if (containerType == "annotationSubsetsContainer") {
                $("#annotationTypesContainer").css('visibility', 'visible');
            }
            get($(this));
        }
    });
});

function getPublicCorpora(annotation_set_id){

    $("#browse_public_corpora_modal").modal("show");

    var _data = {
        'annotation_set_id': annotation_set_id
    };

    var success = function (data) {
        var tableHtml = "";
        $.each(data, function(index, value){
            tableHtml += "<tr>" +
                            "<td>"+value.name+"</td>"+
                            "<td><div class = 'annotation_description'>"+value.description+"</div></td>"+
                            "<td class = 'text-center'><span class='badge'>"+value.count_uses+"</span></td>";
        });
        $("#public_corpora_table").html(tableHtml);
    };
    var login = function (data) {
        getPublicCorpora(annotation_set_id);
    };
    doAjaxSyncWithLogin("public_annotation_sets", _data, success, login);
}

function get($element) {
    var $container = $element.parents(".tableContainer:first");
    var containerName = $container.attr("id");
    var childId = "";
    if (containerName == "annotationSetsContainer" || containerName == "annotationSubsetsContainer") {
        var _data = {
            parent_id: $element.attr('id')
        };
        if (containerName == "annotationSetsContainer") {
            childId = "annotationSubsetsContainer";
            _data.parent_type = 'annotation_set';
        }
        else {
            childId = "annotationTypesContainer";
            _data.parent_type = 'annotation_subset';
        }

        var success = function (data) {
            var tableRows = "";
            $.each(data, function (index, value) {
                //for annotation_set the last two objects contains data from annotation_sets_corpora and corpora
                if (_data.parent_type == "annotation_set" && index < data.length - 2) {
                    tableRows +=
                        '<tr id = "'+value.id+'">' +
                        '<td>' + value.name + '</td>' +
                        '<td><div class = "annotation_description">' + (value.description == null ? "" : value.description) + '</div></td>' +
                        '</tr>';
                }
                else if (_data.parent_type == "annotation_subset")
                    tableRows +=
                        '<tr id = '+value.id+'>' +
                        '<td><span style="' + (value.css == null ? "" : value.css) + '">' + value.name + '</span></td>' +
                        '<td><div class = "annotation_description">' + (value.description == null ? "" : value.description) + '</div></td>' +
                        '<td class = "text-center"><span class="badge">'+ value.number_used +'</span></td>'+
                        '<td style="display:none">' + (value.css == null ? "" : value.css) + '</td>' +
                        '</tr>';
            });
            $("#" + childId + " table > tbody").html(tableRows);

            if (_data.parent_type == "annotation_set") {
                //annotation_sets_corpora:
                tableRows = "";
                $.each(data[data.length - 2], function (index, value) {
                    tableRows +=
                        '<tr>' +
                        '<td class = "column_id">' + value.id + '</td>' +
                        '<td>' + value.name + '</td>' +
                        '<td>' + (value.description == null ? "" : value.description) + '</td>' +
                        '</tr>';
                });
                $("#annotationSetsCorporaContainer table > tbody").html(tableRows);
                //corpora:
                tableRows = "";
                $.each(data[data.length - 1], function (index, value) {
                    tableRows +=
                        '<tr>' +
                        '<td class = "column_id">' + value.id + '</td>' +
                        '<td>' + value.name + '</td>' +
                        '<td>' + (value.description == null ? "" : value.description) + '</td>' +
                        '</tr>';
                });
                $("#corpusContainer table > tbody").html(tableRows);
            }
        };
        var login = function (data) {
            get($element);
        };
        doAjaxSyncWithLogin("annotation_edit_get", _data, success, login);
    }

}
