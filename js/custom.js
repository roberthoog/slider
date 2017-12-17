/**
 * On document ready.
 */
$(document).ready(function () {
    /*
     * ============================
     * Activate Foundation plugins.
     * ============================
     */
    $(document).foundation();

    /*
     * ============================================
     * When "View Image" button clicked, open modal 
     * dialog to display the selected image.
     * ============================================
     */
    $('.master-table tbody').on('click', '.view-image-button', function () {
        var ajax = $.ajax({
            method: 'post',
            dataType: 'html',
            url: 'view-image.php',
            data: {
                'id': $(this).val()
            }
        });
        ajax.done(function (response, textStatus, jqXHR) {
            $('#viewImageModal').html(response).foundation('open');
        });
        ajax.fail(function (jqXHR, textStatus, errorThrown) {
            alert(textStatus + '\n' + errorThrown);
        });
        ajax.always(function (response, textStatus, jqXHR) {
           //... 
        });
    });

    /*
     * ==============================
     * Activate confirmation dialogs.
     * ==============================
     */
    $('body').on('click', '.button-action-confirm', function () {
        return confirm('Är du säker?');
    });
});