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
        url: '/admin/experts/' + id + '/validation',
        dataType: 'html',
    }).done(function (result) {
        if ((result == '0') || (result != id) ) {
            $('div#collapseCard'+id).append(
                '<div class="alert alert-danger" role="alert">\n' +
                    'Произошла ошибка' +
                '</div>');
        } else {
            $('div#collapseCard'+id).html(
                '<div class="card card-body">' +
                    '<div class="alert alert-success" role="alert">' +
                        'Эксперт подтверждён' +
                    '</div>' +
                '</div>');
            $('.invalid' + id).remove();
        }
        console.log('in success ' + result);
    }).fail(function (textStatus,errorThrown) {
        $('div#collapseCard'+id).append(
            '<div class="alert alert-danger" role="alert">\n' +
                'Произошла ошибка' +
            '</div>');
        console.log(textStatus + errorThrown);
    });
}