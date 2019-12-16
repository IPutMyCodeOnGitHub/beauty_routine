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

function unsubRoutine(path, id) {
    console.log(path, id);
    $.ajax({
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('div.routine-sub-' + id).html("You unsubscribe");
        }
        console.log('Success');
    }).fail(function (textStatus,errorThrown) {
        console.log('fail');
    });
}

function routineSub(path, id) {
    $('div.routine-sub-' + id).append('<div class="spinner-border' + id +'" role="status">\n' +
    '  <span class="sr-only">Loading...</span>\n' +
    '</div>');
    console.log(path, id);
    $.ajax({
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('div.routine-sub-' + id).html("You subscribe");
        }
        console.log('Success', result);
    }).fail(function (textStatus,errorThrown) {
        console.log('fail');
        $('div.spinner-border' + id).remove();
    });
}

function deleteRoutine(path, index) {
    $.ajax({
        type: "POST",
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('.card-footer-' + index).html("Deleted");
        } else {
            $('.card-footer-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Error</p>");
        }
    }).fail(function (textStatus,errorThrown) {
        $('.card-footer-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Error</p>");
    });
}

function deleteFromTable(path, id) {
    $.ajax({
        type: "POST",
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('td.exist-' + id).html("");
            $('td.exist-' + id).filter( ':last' ).html("Удалён");
        } else {
            $('td.exist-' + id).filter( ':last' ).append("<p class='pt-2 pb-2'>Ошибка удаления</p>");
        }
    }).fail(function (textStatus,errorThrown) {
        $('td.exist-' + id).filter( ':last' ).append("<p class='pt-2 pb-2'>Ошибка удаления</p>");
    });
}

function deleteDay(path, index) {
    $.ajax({
        url: path,
        dataType: 'text',
    }).done(function (result) {
        if (result == 1) {
            $('td.day-' + index).html("Deleted");
        } else {
            $('td.day-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Error</p>");
        }
    }).fail(function (textStatus,errorThrown) {
        $('td.day-' + index).filter( ':last' ).append("<p class='pt-2 pb-2'>Error</p>");
    });
}

function validExpert(path, id) {
    $.ajax({
        type: "POST",
        url: path,
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