jQuery(document).ready(function ($) {
    $('.color-picker').wpColorPicker({
        change: updatePreview
    });

    $('#dynamic_loader_type').on('change', function () {
        if ($(this).val() === 'image') {
            $('#image_upload_row').show();
            $('#color_row').hide();
        } else {
            $('#image_upload_row').hide();
            $('#color_row').show();
        }
        updatePreview();
    });

    $('#upload_image_button').click(function (e) {
        e.preventDefault();
        var mediaUploader;

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Select Loader Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#dynamic_loader_image').val(attachment.url);
            $('#image_preview').html('<img src="' + attachment.url + '" style="max-width:100px;">');
            updatePreview();
        });

        mediaUploader.open();
    });

    $('#dynamic_loader_bg_opacity').on('input', function () {
        $('#opacity_value').text($(this).val());
        updatePreview();
    });

    $('#dynamic_loader_size').on('input', function () {
        $('#size_value').text($(this).val() + 'px');
        updatePreview();
    });

    $('#test_preview').on('click', function () {
        $('#preview-loader').fadeOut(300).fadeIn(300);
    });

    updatePreview();

    function updatePreview() {
        var type = $('#dynamic_loader_type').val();
        var image = $('#dynamic_loader_image').val();
        var color = $('#dynamic_loader_color').val() || '#0073aa';
        var bgColor = $('#dynamic_loader_bg_color').val() || '#ffffff';
        var size = $('#dynamic_loader_size').val() + 'px';
        var opacity = $('#dynamic_loader_bg_opacity').val();

        $('#preview-loader').css({
            'background-color': bgColor,
            'opacity': opacity
        });

        $('#preview-loader').empty();

        var spinner;

        switch (type) {
            case 'image':
                if (image) {
                    spinner = $('<img>', {
                        src: image,
                        css: { 'max-width': size, 'max-height': size }
                    });
                } else {
                    spinner = $('<div>').text('Please upload an image');
                }
                break;

            case 'circle':
                spinner = $('<div>', { class: 'spinner-circle', css: { 'width': size, 'height': size, 'color': color } });
                break;

            case 'ring':
                spinner = $('<div>', { class: 'spinner-ring', css: { 'width': size, 'height': size, 'color': color } });
                break;

            case 'pulse':
                spinner = $('<div>', { class: 'spinner-pulse', css: { 'width': size, 'height': size, 'color': color } });
                break;

            case 'dots':
                spinner = $('<div>', { class: 'spinner-dots', css: { 'width': size, 'height': size, 'color': color } });
                break;
        }

        $('#preview-loader').append(spinner);
    }
});
