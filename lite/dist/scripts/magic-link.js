const {__, _x, _n, _nx} = wp.i18n;

jQuery(document).ready(function ($) {
    $('.generate-magic-link').on('click', function () {
        var userId = $(this).data('user-id');
        var $linkContainer = $('#magic-link-' + userId);

        $.ajax({
            url: magicLinkAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'generate_magic_link',
                user_id: userId,
                nonce: magicLinkAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    var magicLink = response.data.magic_link;
                    $linkContainer.html(
                        '<input type="text" value="' + magicLink + '" readonly><button class="copy-magic-link button">' + __('Copy', 'magic-link') + '</button>'
                    );
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Copy the magic link to clipboard
    $(document).on('click', '.copy-magic-link', function () {
        var $input = $(this).prev('input');
        $input.select();
        document.execCommand('copy');
        var message = __('Magic link copied to clipboard!', 'magic-link');
        alert(message);
    });
});
