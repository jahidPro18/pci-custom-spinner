<?php
/**
 * Plugin Name:       Custom Spinner Loader
 * Plugin URI:        https://procoder.ca//plugins/pci-custom-spinner/
 * Description:       Replace WooCommerce default loader with custom spinners (image, circle, ring, pulse) with full color customization.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.2
 * Author:            procoder
 * Author URI:        https://procoder.ca/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pci-custom-spinner
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 *
 * @package Pcicustomspinner
 */
/**  
 * Custom Spinner Loader is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU General Public License as published by  
 * the Free Software Foundation, either version 2 of the License, or  
 * any later version.  
 *  
 * Custom Spinner Loader is designed to enhance the shopping experience  
 * by allowing store owners to customize loading spinners within their WooCommerce store.  
 *  
 * This plugin is distributed in the hope that it will be useful,  
 * but WITHOUT ANY WARRANTY; without even the implied warranty of  
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the  
 * GNU General Public License for more details.  
 *  
 * You should have received a copy of the GNU General Public License  
 * along with WooCommerce Custom Spinner. If not, see https://www.gnu.org/licenses/gpl-2.0.html.  
 */  
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add a new menu item in the WordPress admin dashboard.
 */
function dynamic_loader_menu()
{
    add_menu_page('Dynamic Loader Settings', 'Dynamic Loader', 'manage_options', 'dynamic-loader-settings', 'dynamic_loader_settings_page', 'dashicons-update');
}
add_action('admin_menu', 'dynamic_loader_menu');

/**
 * Enqueue scripts and styles in the admin area for the Custom Spinner Loader plugin.
 *
 * @param string $hook The current admin page.
 */
function dynamic_loader_enqueue_scripts($hook)
{
    if ($hook !== 'toplevel_page_dynamic-loader-settings') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('dynamic-loader-admin', plugins_url('admin.js', __FILE__), array('jquery', 'wp-color-picker'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'dynamic_loader_enqueue_scripts');

/**
 * Displays the settings page for the Custom Spinner Loader plugin in the WordPress admin.
 *
 * This function retrieves the current settings for the loader, such as the loader type, image, color, size,
 * background color, and opacity. It then displays a settings form in the admin area with options to customize
 * the loader. The settings are saved to the WordPress database and used to display the loader on the site.
 * 
 * The settings page also includes a live preview of the loader based on the selected options, allowing 
 * the admin to see changes in real-time. JavaScript is used to update the preview and handle events like
 * image upload, color picker, and range sliders for size and opacity.
 *
 * @since  1.0.0
 * @return void
 */
function dynamic_loader_settings_page()
{
    // Get saved options with defaults
    $loader_type = get_option('dynamic_loader_type', 'circle');
    $loader_image = get_option('dynamic_loader_image', '');
    $loader_color = get_option('dynamic_loader_color', '#0073aa');
    $loader_bg_color = get_option('dynamic_loader_bg_color', '#ffffff');
    $loader_size = get_option('dynamic_loader_size', '50');
    $loader_bg_opacity = get_option('dynamic_loader_bg_opacity', '0.7');
    ?>
    <div class="wrap">
        <h1>Dynamic Loader Settings</h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('dynamic_loader_save_settings', 'dynamic_loader_nonce'); ?>
            <input type="hidden" name="action" value="dynamic_loader_save">

            <div class="dynamic-loader-container">
                <div class="dynamic-loader-options">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Loader Type</th>
                            <td>
                                <select name="dynamic_loader_type" id="dynamic_loader_type">
                                    <option value="circle" <?php selected($loader_type, 'circle'); ?>>Circle</option>
                                    <option value="ring" <?php selected($loader_type, 'ring'); ?>>Ring</option>
                                    <option value="pulse" <?php selected($loader_type, 'pulse'); ?>>Pulse</option>
                                    <option value="dots" <?php selected($loader_type, 'dots'); ?>>Dots</option>
                                    <option value="image" <?php selected($loader_type, 'image'); ?>>Custom Image</option>
                                </select>
                            </td>
                        </tr>

                        <tr valign="top" id="image_upload_row" style="<?php echo ($loader_type === 'image') ? '' : 'display:none;'; ?>">
                            <th scope="row">Upload Custom Image</th>
                            <td>
                                <input type="text" name="dynamic_loader_image" id="dynamic_loader_image" value="<?php echo esc_attr($loader_image); ?>" style="width:300px;" />
                                <button type="button" id="upload_image_button" class="button">Upload</button>
                                <p class="description">Recommended to use GIF or SVG for better animation effects.</p>
                                <div id="image_preview" style="margin-top:10px;">
                                    <?php
                                        $loader_image_id = attachment_url_to_postid($loader_image);
                                    if ($loader_image_id) {
                                        echo wp_get_attachment_image($loader_image_id, 'full', false, ['style' => 'max-width:' . esc_attr($loader_size) . 'px; max-height:' . esc_attr($loader_size) . 'px;']);
                                    } 
                                        // else {
                                        //     echo '<img src="' . esc_url($loader_image) . '" alt="Loading..." style="max-width:' . esc_attr($loader_size) . 'px; max-height:' . esc_attr($loader_size) . 'px;" />';
                                        // }
                                    ?>
                                </div>
                            </td>
                        </tr>

                        <tr valign="top" id="color_row" style="<?php echo ($loader_type !== 'image') ? '' : 'display:none;'; ?>">
                            <th scope="row">Loader Color</th>
                            <td>
                                <input type="text" name="dynamic_loader_color" id="dynamic_loader_color" value="<?php echo esc_attr($loader_color); ?>" class="color-picker" />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">Background Color</th>
                            <td>
                                <input type="text" name="dynamic_loader_bg_color" id="dynamic_loader_bg_color" value="<?php echo esc_attr($loader_bg_color); ?>" class="color-picker" />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">Background Opacity</th>
                            <td>
                                <input type="range" name="dynamic_loader_bg_opacity" id="dynamic_loader_bg_opacity" min="0" max="1" step="0.1" value="<?php echo esc_attr($loader_bg_opacity); ?>" />
                                <span id="opacity_value"><?php echo esc_attr($loader_bg_opacity); ?></span>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">Loader Size (px)</th>
                            <td>
                                <input type="range" name="dynamic_loader_size" id="dynamic_loader_size" min="20" max="150" value="<?php echo esc_attr($loader_size); ?>" />
                                <span id="size_value"><?php echo esc_attr($loader_size); ?>px</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="dynamic-loader-preview">
                    <h2>Live Preview</h2>
                    <div id="preview-container" style="width: 100%; height: 250px; position: relative; border: 1px solid #ddd; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5;">
                        <div id="preview-loader" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: <?php echo esc_attr($loader_bg_color); ?>; opacity: <?php echo esc_attr($loader_bg_opacity); ?>;"></div>
                    </div>
                    <button type="button" id="test_preview" class="button button-primary">Test Animation</button>
                </div>
            </div>

            <?php submit_button('Save Settings'); ?>
        </form>
    </div>

    <style>
       .dynamic-loader-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        
        .dynamic-loader-options {
            flex: 1;
            min-width: 300px;
        }
        
        .dynamic-loader-preview {
            flex: 1;
            min-width: 300px;
            padding: 15px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        /* Spinner Types CSS */
        .spinner-circle {
            border-radius: 50%;
            width: 100%;
            height: 100%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top-color: currentColor;
            animation: spin 1s infinite linear;
        }
        
        .spinner-ring {
            border-radius: 50%;
            width: 100%;
            height: 100%;
            border: 5px solid currentColor;
            border-top-color: transparent;
            animation: spin 1s infinite linear;
        }
        
        .spinner-pulse {
            width: 100%;
            height: 100%;
            background-color: currentColor;
            border-radius: 50%;
            animation: pulse 1.2s infinite ease-in-out;
        }
        
        .spinner-dots {
            width: 100%;
            height: 100%;
            position: relative;
            animation: dots 1.4s infinite ease-in-out;
        }
        
        .spinner-dots:before, .spinner-dots:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 60%;
            border-radius: 50%;
            background-color: currentColor;
            animation: dots 1.4s infinite ease-in-out;
        }
        
        .spinner-dots:after {
            top: 40%;
            left: 40%;
            animation-delay: 0.7s;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(0); opacity: 0.5; }
            50% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes dots {
            0%, 100% { transform: scale(0.2); }
            50% { transform: scale(1); }
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('.color-picker').wpColorPicker({
                change: updatePreview
            });

            $('#dynamic_loader_type').on('change', function() {
                if ($(this).val() === 'image') {
                    $('#image_upload_row').show();
                    $('#color_row').hide();
                } else {
                    $('#image_upload_row').hide();
                    $('#color_row').show();
                }
                updatePreview();
            });

            $('#upload_image_button').click(function(e) {
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

                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#dynamic_loader_image').val(attachment.url);
                    $('#image_preview').html(wp.template('attachment-preview')({
                        id: attachment.id,
                        size: 'thumbnail'
                    }));
                    updatePreview();
                });

                mediaUploader.open();
            });

            $('#dynamic_loader_bg_opacity').on('input', function() {
                $('#opacity_value').text($(this).val());
                updatePreview();
            });

            $('#dynamic_loader_size').on('input', function() {
                $('#size_value').text($(this).val() + 'px');
                updatePreview();
            });

            $('#test_preview').on('click', function() {
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

                switch(type) {
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
    </script>
    <?php
}

/**
 * Handle the form submission for saving dynamic loader settings.
 */
function dynamic_loader_save_settings()
{
    // Unsanitize the POST data before checking the nonce
    if (isset($_POST['dynamic_loader_nonce']) && wp_verify_nonce(sanitize_key(wp_unslash($_POST['dynamic_loader_nonce'])), 'dynamic_loader_save_settings')) {

        // $loader_type = sanitize_text_field($_POST['dynamic_loader_type']);
        if (isset($_POST['dynamic_loader_type'])) {
            $loader_type = sanitize_text_field(wp_unslash($_POST['dynamic_loader_type']));
            update_option('dynamic_loader_type', $loader_type);
        }

        // $loader_image = esc_url_raw($_POST['dynamic_loader_image']);
        if (isset($_POST['dynamic_loader_image'])) {
            $loader_image = esc_url_raw(wp_unslash($_POST['dynamic_loader_image']));
            update_option('dynamic_loader_image', $loader_image);
        }

        // $loader_color = sanitize_hex_color($_POST['dynamic_loader_color']);
        if (isset($_POST['dynamic_loader_color'])) {
            $loader_color = sanitize_hex_color(wp_unslash($_POST['dynamic_loader_color']));
            update_option('dynamic_loader_color', $loader_color);
        }

        // $loader_bg_color = sanitize_hex_color($_POST['dynamic_loader_bg_color']);
        if (isset($_POST['dynamic_loader_bg_color'])) {
            $loader_bg_color = sanitize_hex_color(wp_unslash($_POST['dynamic_loader_bg_color']));
            update_option('dynamic_loader_bg_color', $loader_bg_color);
        }

        // $loader_size = absint($_POST['dynamic_loader_size']);
        if (isset($_POST['dynamic_loader_size'])) {
            $loader_size = absint($_POST['dynamic_loader_size']); // Sanitize as an integer
            update_option('dynamic_loader_size', $loader_size);
        }

        // $loader_bg_opacity = floatval($_POST['dynamic_loader_bg_opacity']);
        // $loader_bg_opacity = min(max(0, $loader_bg_opacity), 1); // Ensure between 0 and 1
        if (isset($_POST['dynamic_loader_bg_opacity'])) {
            $loader_bg_opacity = floatval($_POST['dynamic_loader_bg_opacity']); // Sanitize as float
            // Ensure the opacity is between 0 and 1
            $loader_bg_opacity = min(max(0, $loader_bg_opacity), 1);
            update_option('dynamic_loader_bg_opacity', $loader_bg_opacity);
        }

        wp_redirect(admin_url('admin.php?page=dynamic-loader-settings'));
        exit;
    } else {
        wp_die('Nonce verification failed.');
    }
}
add_action('admin_post_dynamic_loader_save', 'dynamic_loader_save_settings');


/**
 * Activates the Custom Spinner Loader plugin.
 *
 * This function is triggered when the plugin is activated. It checks if the `admin.js` file exists in the plugin directory.
 * If the file does not exist, the function creates it with the necessary JavaScript content to initialize the color picker
 * functionality for the plugin's settings page.
 *
 * The JavaScript code added to `admin.js` ensures that the color picker (provided by WordPress) is properly initialized
 * for any elements with the `color-picker` class on the settings page.
 *
 * @since 1.0.0
 */
function dynamic_loader_activate()
{
    $admin_js = plugin_dir_path(__FILE__) . 'admin.js';
    
    if (!file_exists($admin_js)) {
        $js_content = "
            jQuery(document).ready(function($) {
                // Color picker initialization.
                if ($.fn.wpColorPicker) {
                    $('.color-picker').wpColorPicker();
                }
            });
        ";
        
        file_put_contents($admin_js, $js_content);
    }
}
register_activation_hook(__FILE__, 'dynamic_loader_activate');

/**
 * Overrides the default loader behavior with a Custom Spinner Loader.
 *
 * This function allows for dynamic customization of the loading spinner based on the saved options.
 * It applies different loader styles based on user preferences and inserts the corresponding HTML 
 * and CSS into the page. It also listens for AJAX events related to WooCommerce and ensures the 
 * loader shows and hides appropriately.
 *
 * @since 1.0.0
 */
function dynamic_loader_override()
{
    // Get saved options with defaults.
    $loader_type = get_option('dynamic_loader_type', 'circle');
    $loader_image = get_option('dynamic_loader_image', '');
    $loader_color = get_option('dynamic_loader_color', '#0073aa');
    $loader_bg_color = get_option('dynamic_loader_bg_color', '#ffffff');
    $loader_size = get_option('dynamic_loader_size', '50');
    $loader_bg_opacity = get_option('dynamic_loader_bg_opacity', '0.7');
    
    // Determine which spinner HTML to use.
    $spinner_html = '';
    switch ($loader_type) {
    case 'image':
        $attachment_id = attachment_url_to_postid($loader_image);
        if ($attachment_id) {
            $spinner_html = wp_get_attachment_image(
                $attachment_id,
                'thumbnail',
                false,
                array(
                    'alt' => 'Loading...',
                    'style' => 'max-width:' . esc_attr($loader_size) . 'px; max-height:' . esc_attr($loader_size) . 'px;'
                )
            );
        } else {
              $spinner_html = '<div class="spinner-circle" style="width:' . esc_attr($loader_size) . 'px; height:' . esc_attr($loader_size) . 'px; color:' . esc_attr($loader_color) . ';"></div>';
        }
        
        break;
            
    case 'circle':
        $spinner_html = '<div class="spinner-circle" style="width:' . esc_attr($loader_size) . 'px; height:' . esc_attr($loader_size) . 'px; color:' . esc_attr($loader_color) . ';"></div>';
        break;
            
    case 'ring':
        $spinner_html = '<div class="spinner-ring" style="width:' . esc_attr($loader_size) . 'px; height:' . esc_attr($loader_size) . 'px; color:' . esc_attr($loader_color) . ';"></div>';
        break;
            
    case 'pulse':
        $spinner_html = '<div class="spinner-pulse" style="width:' . esc_attr($loader_size) . 'px; height:' . esc_attr($loader_size) . 'px; color:' . esc_attr($loader_color) . ';"></div>';
        break;
            
    case 'dots':
        $spinner_html = '<div class="spinner-dots" style="width:' . esc_attr($loader_size) . 'px; height:' . esc_attr($loader_size) . 'px; color:' . esc_attr($loader_color) . ';"></div>';
        break;
    }
    ?>
    <style>
        /* Hide WooCommerce default spinners */
        .blockUI, .blockOverlay, .loading,
        .wc-block-components-button--loading,
        .wc-block-components-checkout-place-order-button__text--visually-hidden {
            display: none !important;
        }
        
        /* Custom dynamic loader */
        #dynamic-fullscreen-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            visibility: hidden;
            background-color: <?php echo esc_attr($loader_bg_color); ?>;
            opacity: <?php echo esc_attr($loader_bg_opacity); ?>;
        }
        
        /* Spinner animations */
        .spinner-circle {
            border-radius: 50%;
            width: <?php echo esc_attr($loader_size); ?>px;
            height: <?php echo esc_attr($loader_size); ?>px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top-color: <?php echo esc_attr($loader_color); ?>;
            animation: spin 1s infinite linear;
        }
        
        .spinner-ring {
            border-radius: 50%;
            width: <?php echo esc_attr($loader_size); ?>px;
            height: <?php echo esc_attr($loader_size); ?>px;
            border: 5px solid <?php echo esc_attr($loader_color); ?>;
            border-top-color: transparent;
            animation: spin 1s infinite linear;
        }
        
        .spinner-pulse {
            width: <?php echo esc_attr($loader_size); ?>px;
            height: <?php echo esc_attr($loader_size); ?>px;
            background-color: <?php echo esc_attr($loader_color); ?>;
            border-radius: 50%;
            animation: pulse 1.2s infinite ease-in-out;
        }
        
        .spinner-dots {
            width: <?php echo esc_attr($loader_size); ?>px;
            height: <?php echo esc_attr($loader_size); ?>px;
            position: relative;
            animation: dots 1.4s infinite ease-in-out;
        }
        
        .spinner-dots:before, .spinner-dots:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 60%;
            border-radius: 50%;
            background-color: <?php echo esc_attr($loader_color); ?>;
            animation: dots 1.4s infinite ease-in-out;
        }
        
        .spinner-dots:after {
            top: 40%;
            left: 40%;
            animation-delay: 0.7s;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(0); opacity: 0.5; }
            50% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes dots {
            0%, 100% { transform: scale(0.2); }
            50% { transform: scale(1); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            #dynamic-fullscreen-loader img {
                max-width: <?php echo esc_attr($loader_size * 0.8); ?>px;
                max-height: <?php echo esc_attr($loader_size * 0.8); ?>px;
            }
            
            .spinner-circle, .spinner-ring, .spinner-pulse, .spinner-dots {
                width: <?php echo esc_attr($loader_size * 0.8); ?>px;
                height: <?php echo esc_attr($loader_size * 0.8); ?>px;
            }
        }
        
        @media (max-width: 480px) {
            #dynamic-fullscreen-loader img {
                max-width: <?php echo esc_attr($loader_size * 0.6); ?>px;
                max-height: <?php echo esc_attr($loader_size * 0.6); ?>px;
            }
            
            .spinner-circle, .spinner-ring, .spinner-pulse, .spinner-dots {
                width: <?php echo esc_attr($loader_size * 0.6); ?>px;
                height: <?php echo esc_attr($loader_size * 0.6); ?>px;
            }
        }
    </style>
    
    <div id="dynamic-fullscreen-loader">
        <?php echo wp_kses_post($spinner_html); ?>
    </div>
    
    <script>
    
    jQuery(document).ready(function($) {
    // Function to show/hide loader.
    function showDynamicLoader() {
        $('#dynamic-fullscreen-loader').css('visibility', 'visible');
    }
    
    function hideDynamicLoader() {
        $('#dynamic-fullscreen-loader').css('visibility', 'hidden');
    }
    
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
    });
    
    // Hide loader when page is completely loaded.
    $(window).on('load', function() {
        hideDynamicLoader();
    });
});
    </script>
    <?php
}
add_action('wp_head', 'dynamic_loader_override');
