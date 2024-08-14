(function ($) {
    $(document).ready(function () {
        // Show the first tab by default
        $('.vertical-tab-panel:first-child').addClass('active');
        $('.vertical-tab-navigation li:first-child').attr('aria-selected', 'true');

        // Handle vertical tab navigation
        $('.vertical-tab-navigation a').on('click', function (e) {
            e.preventDefault();

            var panelID = $(this).attr('href');

            // Remove "active" class from all tabs and panels
            $('.vertical-tab-navigation li').attr('aria-selected', 'false');
            $('.vertical-tab-navigation a').removeClass('active');
            $('.vertical-tab-panel').removeClass('active');

            // Add "active" class to the clicked tab and panel
            $(this).addClass('active');
            $(panelID).addClass('active');
            $(this).parent().attr('aria-selected', 'true');
        });

        // Get references to the checkbox and color container
        var overlayEnabledCheckbox = $('#popup-overlay-enabled');
        var overlayColorContainer = $('#overlay-color-container');

        // Initially hide the color container if overlay is not enabled
        if (!overlayEnabledCheckbox.prop('checked')) {
            overlayColorContainer.hide();
        }

        // Add an event listener to toggle the color container visibility
        overlayEnabledCheckbox.change(function () {
            if (overlayEnabledCheckbox.prop('checked')) {
                overlayColorContainer.show();
            } else {
                overlayColorContainer.hide();
            }
        });

        var ShowOnWholeSiteCheckbox = $('#popup-show-on-whole-site');
        var SelectPagesContainer = $('#select-pages-container');

        if (ShowOnWholeSiteCheckbox.prop('checked')) {
            SelectPagesContainer.hide();
        }

        ShowOnWholeSiteCheckbox.change(function () {
            if (!ShowOnWholeSiteCheckbox.prop('checked')) {
                SelectPagesContainer.show();
            } else {
                SelectPagesContainer.hide();
            }
        });

        // Function to toggle inactivity time field visibility
        function toggleInactivityTimeField() {
            if ($('#popup-trigger').val() === 'inactivity') {
                $('.inactivity-time').show();
                $('.popup-delay-option').hide();
            } else {
                $('.inactivity-time').hide();
                $('.popup-delay-option').show();
            }
        }

        // Initially set the inactivity time field visibility
        toggleInactivityTimeField();

        // Event listener for trigger option change
        $('#popup-trigger').change(function () {
            toggleInactivityTimeField();
        });

        $('#popup-show-on-whole-site').change(function() {
            if ($(this).is(':checked')) {
                $('#exclude-pages-container').show();
            } else {
                $('#exclude-pages-container').hide();
            }
        });

        // Initialize the color picker with alpha support
        // $('.color-picker-alpha-field').wpColorPicker({
        //     mode: 'rgba',
        // });
        $(".color-picker-alpha-field").wpColorPicker();
    });
})(jQuery);