$(document).ready(function() {

    $('.custom-file-input').on('change', function(event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    });

    $('body').on('click','.add-day', function (event) {
        event.preventDefault();
        var countDays = $('div.days').children('.day-item').length + 1;
        console.log(countDays);
        $('div.days').append('<div class="day-item">\n' +
            '<input class="order-value" type="text" name="routine_day[]" value="'+ countDays +'" disabled/>\n' +
            '</div>');
    });

});

function routineSub(path, id) {
    console.log(path, id);
    $.ajax({
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('div.routine-sub-' + id).html("Вы подписаны");
        }
        console.log('Success');
    }).fail(function (textStatus,errorThrown) {
        console.log('fail');
    });
}

function deleteRoutine(path, index) {
    $.ajax({
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('.card-footer-' + index).html("Удалено");
        } else {
            $('.card-footer-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Ошибка удаления</p>");
        }
    }).fail(function (textStatus,errorThrown) {
        $('.card-footer-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Ошибка удаления</p>");
    });
}

function deleteDay(path, index) {
    $.ajax({
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('td.day-' + index).html("Удалён");
        } else {
            $('td.day-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Ошибка удаления</p>");
        }
    }).fail(function (textStatus,errorThrown) {
        $('td.day-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Ошибка удаления</p>");
    });
}

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
        // console.log('in success ' + result);
    }).fail(function (textStatus,errorThrown) {
        $('div#collapseCard'+id).append(
            '<div class="alert alert-danger" role="alert">\n' +
                'Произошла ошибка' +
            '</div>');
        // console.log(textStatus + errorThrown);
    });
}