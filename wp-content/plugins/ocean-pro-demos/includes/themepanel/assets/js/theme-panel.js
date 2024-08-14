// Document Ready
jQuery(document).ready(function ($) {

    $(document.body).on('op_panel_loaded', function (event) {
        if ($('#owp_freepik_integration').length) {
            jQuery('#owp_freepik_integration').trigger('change');
        }
        if ($('#owp_freepik_image_width').length) {
            jQuery('#owp_freepik_image_width').trigger('change');
        }
        if ($('#owp_flaticon_integration').length) {
            jQuery('#owp_flaticon_integration').trigger('change');
        }

        //#region: Pixel Options
        function toggleFacebookOptions() {
            var isChecked = $('#enable-facebook-pixel').is(':checked');
            if (isChecked) {
                $('#content-1 .control-group:not(:first)').show();
            } else {
                $('#content-1 .control-group:not(:first)').hide();
            }
        }
    
        function toggleGoogleTagOptions() {
            var isChecked = $('#enable-google-tag-manager').is(':checked');
            if (isChecked) {
                $('#content-2 .control-group:not(:first)').show();
            } else {
                $('#content-2 .control-group:not(:first)').hide();
            }
        }
    
        function toggleTikTokTagOptions() {
            var isChecked = $('#enable-tiktok-pixel').is(':checked');
            if (isChecked) {
                $('#content-3 .control-group:not(:first)').show();
            } else {
                $('#content-3 .control-group:not(:first)').hide();
            }
        }
    
        function toggleLinkedinTagOptions() {
            var isChecked = $('#enable-linkedin-insight-tag').is(':checked');
            if (isChecked) {
                $('#content-4 .control-group:not(:first)').show();
            } else {
                $('#content-4 .control-group:not(:first)').hide();
            }
        }
    
        function togglePinterestTagOptions() {
            var isChecked = $('#enable-pinterest-tag').is(':checked');
            if (isChecked) {
                $('#content-5 .control-group:not(:first)').show();
            } else {
                $('#content-5 .control-group:not(:first)').hide();
            }
        }
        
        toggleFacebookOptions();
        toggleGoogleTagOptions();
        toggleTikTokTagOptions();
        toggleLinkedinTagOptions();
        togglePinterestTagOptions();
    
        $('#enable-facebook-pixel').change(function () {
            toggleFacebookOptions();
        });
    
        $('#enable-google-tag-manager').change(function () {
            toggleGoogleTagOptions();
        });
    
        $('#enable-tiktok-pixel').change(function () {
            toggleTikTokTagOptions();
        });
    
        $('#enable-linkedin-insight-tag').change(function () {
            toggleLinkedinTagOptions();
        });
    
        $('#enable-pinterest-tag').change(function () {
            togglePinterestTagOptions();
        });

        $(document.body).on('change', '#oceanwp-switch-pixel-tracker-enable', function (event) {
            event.preventDefault();
            var optionVal = $(this).prop('checked') ? 'yes' : 'no';
            runSaveSingleOption('opd_pixel_tracker_enable', optionVal);
            if ($(this).prop('checked')) {
                $('.save_pro_demos_pixel_settings').fadeIn();
            } else {
                $('.save_pro_demos_pixel_settings').fadeOut();
            }
        });
    });



    //#region: Ocean Images
    $(document.body).on('change', '#owp_api_images_integration', function () {
        jQuery(this).val() === '0' ? jQuery('.api-ingegrations').hide() : jQuery('.api-ingegrations').show();
    });

    $(document.body).on('change', '#owp_freepik_integration', function () {
        jQuery(this).val() === '0' ? jQuery('#owp_freepik_image_width_tr').hide() : jQuery('#owp_freepik_image_width_tr').show();
    });

    $(document.body).on('change', '#owp_flaticon_integration', function () {
        jQuery(this).val() === '0' ? jQuery('#owp_flaticon_image_width_tr').hide() : jQuery('#owp_flaticon_image_width_tr').show();
    });

    $(document.body).on('change', '#oceanwp-switch-popup-builder-enable', function (event) {
        event.preventDefault();
        var optionVal = $(this).prop('checked') ? 'yes' : 'no';
        runSaveSingleOption('opd_popup_builder_enable', optionVal);
    });

    $(document.body).on('change', '#oceanwp-switch-cpt-manager-enable', function (event) {
        event.preventDefault();
        var optionVal = $(this).prop('checked') ? 'yes' : 'no';
        runSaveSingleOption('opd_cpt_manager_enable', optionVal);
    });

    $(document.body).on('change', '#oceanwp-switch-pixel-tracker-enable', function (event) {
        event.preventDefault();
        var optionVal = $(this).prop('checked') ? 'yes' : 'no';
        runSaveSingleOption('opd_cpt_manager_enable', optionVal);
    });

    function runSaveSingleOption(optionName, value) {
        if( optionName !== '' && optionName !== undefined ) {
            $.ajax({
                url: ajaxurl,
                method: "POST",
                data: {
                    _nonce: ExtraThemePanelOptions.ocean_save_single_option_nonce,
                    option_name: optionName,
                    value: value,
                    action: 'oceanwp_cp_save_single_option',
                },
                beforeSend: function () {
                    window['showNotify']('success', oceanwp_cp_textdomain.saving_settings);
                },
                success: function (data) {
                    window['showNotify'](data.success, data.data.message);
                },
                error: function (xhr, status, error) {
                },
                complete: function () {
                }
            });
        }
    }

    $(document.body).on('submit', 'form.save_pro_demos_pixel_settings', function (event) {
        event.preventDefault();
        runSavingPixelSettings($(this));
    });

    function runSavingPixelSettings($form) {
        $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                form_fields: $form.serialize(),
                action: 'oceanwp_cp_save_pro_demos_pixel_settings',
                nonce: ExtraThemePanelOptions.nonce
            },
            beforeSend: function () {
                window['showNotify']('success', oceanwp_cp_textdomain.saving_settings);
            },
            success: function (data) {
                window['showNotify'](data.success, data.data.message);
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    }

});