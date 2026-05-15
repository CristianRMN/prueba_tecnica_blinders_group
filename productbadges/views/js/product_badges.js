$(document).ready(function () {

    var controllerUrl = adminProductBadgesUrl;

    function renderBadges(badges) {
        var container = $('#assigned-badges');
        container.empty();

        if (!badges || badges.length === 0) {
            container.html('<p id="no-badges-msg">No hay badges asignadas</p>');
            return;
        }

        var html = '<div class="badges-list">';
        for (var i = 0; i < badges.length; i++) {
            var b = badges[i];
            html += '<span class="badge" style="background:' + b.background_color + '; color:' + b.text_color + '; padding:5px; margin:3px; display:inline-block; border-radius:4px;">';
            html += b.name;
            html += ' <button type="button" class="remove-badge" data-id-product="' + b.id_product + '" data-id-badge="' + b.id_badge + '" style="margin-left:5px; background:none; border:none; color:inherit; cursor:pointer;">✕</button>';
            html += '</span>';
        }
        html += '</div>';
        container.html(html);
    }

    // Añadir badges
    $(document).on('click', '#add-badges', function () {
        var idProduct = $(this).data('id-product');
        var selectedBadges = $('#badge-selector').val();

        if (!selectedBadges || selectedBadges.length === 0) {
            return;
        }

        $.ajax({
            url: controllerUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: 1,
                action: 'addBadges',
                id_product: idProduct,
                badges: selectedBadges
            },
            success: function (response) {
                if (response.success) {
                    renderBadges(response.badges);
                    $('#badge-selector').val([]);
                }
            }
        });
    });

    // Eliminar badge
    $(document).on('click', '.remove-badge', function () {
        var idProduct = $(this).data('id-product');
        var idBadge = $(this).data('id-badge');

        $.ajax({
            url: controllerUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: 1,
                action: 'removeBadge',
                id_product: idProduct,
                id_badge: idBadge
            },
            success: function (response) {
                if (response.success) {
                    renderBadges(response.badges);
                }
            }
        });
    });

});
