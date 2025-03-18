jQuery(document).ready(function($) {
    // Access the localized variables from PHP
    const loaderColor = dynamicLoaderVars.loader_color;
    const loaderBgColor = dynamicLoaderVars.loader_bg_color;
    const loaderSize = dynamicLoaderVars.loader_size;
    const loaderBgOpacity = dynamicLoaderVars.loader_bg_opacity;
    const loaderType = dynamicLoaderVars.loader_type;
    const loaderImage = dynamicLoaderVars.loader_image;

    // Function to show/hide loader
    function showDynamicLoader() {
        $('#dynamic-fullscreen-loader').css('visibility', 'visible');
    }

    function hideDynamicLoader() {
        $('#dynamic-fullscreen-loader').css('visibility', 'hidden');
    }

    // Dynamically update the loader's background color and opacity
    $('#dynamic-fullscreen-loader').css({
        'background-color': loaderBgColor,
        'opacity': loaderBgOpacity,
    });

    // Dynamically generate and insert spinner HTML based on the loader type
    let spinnerHtml = '';
    switch (loaderType) {
        case 'image':
            // If an image is set as the loader
            spinnerHtml = '<div class="spinner-image" style="width: ' + loaderSize + 'px; height: ' + loaderSize + 'px; background-image: url(' + loaderImage + '); background-size: cover;"></div>';
            break;

        case 'circle':
            // Circle spinner
            spinnerHtml = '<div class="spinner-circle" style="width: ' + loaderSize + 'px; height: ' + loaderSize + 'px; border-color: ' + loaderColor + ';"></div>';
            break;

        case 'ring':
            // Ring spinner
            spinnerHtml = '<div class="spinner-ring" style="width: ' + loaderSize + 'px; height: ' + loaderSize + 'px; border-color: ' + loaderColor + ';"></div>';
            break;

        case 'pulse':
            // Pulse spinner
            spinnerHtml = '<div class="spinner-pulse" style="width: ' + loaderSize + 'px; height: ' + loaderSize + 'px; background-color: ' + loaderColor + ';"></div>';
            break;

        case 'dots':
            // Dots spinner
            spinnerHtml = '<div class="spinner-dots" style="width: ' + loaderSize + 'px; height: ' + loaderSize + 'px; color: ' + loaderColor + ';"></div>';
            break;

        default:
            // Default to circle if no valid loader type is set
            spinnerHtml = '<div class="spinner-circle" style="width: ' + loaderSize + 'px; height: ' + loaderSize + 'px; border-color: ' + loaderColor + ';"></div>';
            break;
    }

    // Insert the spinner HTML into the loader container
    $('#dynamic-fullscreen-loader').html(spinnerHtml);

    // Show loader on AJAX start for WooCommerce pages.
    $(document).on('ajaxStart wc_fragment_refresh wc_cart_button_updated wc_checkout_update_order_review wc_ajax_send', function() {
        showDynamicLoader();
    });

    // Hide loader when AJAX stops.
    $(document).on('ajaxStop wc_fragments_refreshed updated_checkout', function() {
        hideDynamicLoader();
    });

    // Ensure immediate loader appearance on various WooCommerce buttons.
    $(document).on('click', 
        'wc-block-components-panel__content, ' + 
        'wc-block-components-totals-coupon__content, ' + 
        'form.wc-block-components-totals-coupon__form, ' +
        '.wc-block-components-totals-coupon__input-coupon, ' +
        'form.woocommerce-cart-form button, ' + 
        'form.checkout.woocommerce-checkout button, ' +
        '.woocommerce-cart-form button[name="apply_coupon"], ' +
        '.woocommerce-cart-form button[name="update_cart"], ' +
        '.wc-block-components-button, ' + 
        '.wp-element-button, ' +
        '.wc-block-cart__submit-button, ' +
        '.checkout.woocommerce-checkout button[name="woocommerce_checkout_place_order"], ' +
        '.wc-block-components-checkout-place-order-button, ' +
        '.wc-block-checkout__actions_row, ' + 
        '.wc-block-components-button, ' + 
        '.wp-element-button, ' +
        '.wc-block-components-checkout-place-order-button', 
        function() {
            showDynamicLoader();

            // Auto-hide after 10 seconds (failsafe).
            setTimeout(function() {
                hideDynamicLoader();
            }, 10000);
        }
    );

    // Hide loader when page is completely loaded.
    $(window).on('load', function() {
        hideDynamicLoader();
    });
});
