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
                '<label class="pl-2 pr-2">Произошла ошибка</label>');
        } else {
            $('div#collapseCard'+id).html(
                '<div class="card card-body">' +
                '<label class="pl-2 pr-2">Эксперт подтверждён</label>' +
                '</div>');
            $('.invalid' + id).remove();
        }
        console.log('in success ' + result);
    }).fail(function (textStatus,errorThrown) {
        $('div#collapseCard'+id).append(
            '<label class="pl-2 pr-2">Произошла ошибка</label>');
        console.log(textStatus + errorThrown);
    });
}