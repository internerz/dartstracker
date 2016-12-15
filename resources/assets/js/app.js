$(document).ready(function () {
    var users = [];
    var opponentList = $('#opponentList');

    $('#opponentSearch').autocomplete({
        source: '/user/find',
        minLength: 1,
        select: function (event, ui) {
            if (users.indexOf(ui.item.id) == -1) {
                users.push(ui.item.id);

                var element = opponentList.find('a.hidden').clone();
                element
                    .removeClass('hidden')
                    .data('id', ui.item.id)
                    .find('.name').text(ui.item.value);

                element.click(function () {
                    users.splice(users.indexOf(ui.item.id), 1);
                    $(this).remove();
                    $('#opponents').val(JSON.stringify(users));
                    return false;
                });
                element.appendTo(opponentList);
            }
        },
        close: function () {
            this.value = '';
            $('#opponents').val(JSON.stringify(users));
        }
    });
});