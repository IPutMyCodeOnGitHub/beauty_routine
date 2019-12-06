$(document).ready(function() {

    $('.custom-file-input').on('change', function(event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    });

});

function validExpert(id) {
    $.ajax({
        type: "POST",
        url: '/admin/manage-user/experts/validation',
        data: {'id': id},
        dataType: 'html',
    }).done(function (html) {
        alert('Success' + html);
    }).fail(function (textStatus,errorThrown) {
        console.log(textStatus + errorThrown);
    });
}