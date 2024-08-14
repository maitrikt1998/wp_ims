// Document Ready
jQuery(document).ready(function ($) {

    function checkTabsOverflow() {
        var tabs = $('.tabs');
        if (tabs.length) {
            var tabsWidth = 0;
            tabs.find('.tab').each(function () {
                tabsWidth += $(this).outerWidth(true);
            });
            var containerWidth = tabs.outerWidth();
            var isOverflowing = tabsWidth > containerWidth;
            $('.tab-scroll .scroll-btn').css('display', isOverflowing ? 'flex' : 'none');
        }
    }

    function updateScrollButtonsVisibility() {
        var tabs = $('.tabs');
        if (tabs.length) {
            var scrollLeft = tabs.scrollLeft();
            var tabsWidth = tabs.get(0).scrollWidth;
            var containerWidth = tabs.outerWidth();
            $('.left-scroll').css('visibility', scrollLeft > 0 ? 'visible' : 'hidden');
            $('.right-scroll').css('visibility', (scrollLeft + containerWidth) < tabsWidth ? 'visible' : 'hidden');
        }
    }

    $(document.body).on('op_panel_loaded', function (event) {
        // Initial check for overflow and update button visibility
        checkTabsOverflow();
        updateScrollButtonsVisibility();

        // Re-check on window resize
        $(window).resize(function() {
            checkTabsOverflow();
            updateScrollButtonsVisibility();
        });

        // Event listeners for tab radio buttons
        $('.tabs input[type="radio"]').on('change', function() {
            $('.ocean-tp-tab-content').removeClass('active');
            var contentId = 'content-' + this.id.split('-')[1];
            $('#' + contentId).addClass('active');
        });

        // Make the first tab active by default
        var firstRadioButton = $('.tabs input[type="radio"]').first();
        if (firstRadioButton.length) {
            firstRadioButton.prop('checked', true).trigger('change');
        }

        // Scrolling buttons functionality
        if ($('.tabs').length) {
            $('.left-scroll').on('click', function () {
                $('.tabs').animate({scrollLeft: '-=200'}, 300, 'swing', updateScrollButtonsVisibility);
            });
    
            $('.right-scroll').on('click', function () {
                $('.tabs').animate({scrollLeft: '+=200'}, 300, 'swing', updateScrollButtonsVisibility);
            });
        }
    });

      // Function to scroll the selected tab into view
      function scrollToActiveTab() {
        var tabs = $('.tabs');
        if (tabs.length && $(window).width() <= 700) {
            var activeTab = tabs.find('input[type="radio"]:checked').next('.tab');
            if (activeTab.length) {
                tabs.animate({
                    scrollLeft: activeTab.position().left + tabs.scrollLeft() - tabs.offset().left
                }, 300);
            }
        }
    }

    // Bind scrollToActiveTab to tab change event
    $('.tabs input[type="radio"]').change(scrollToActiveTab);

    // Call scrollToActiveTab on initial load
    if ($('.tabs').length) {
        $('.tabs input[type="radio"]').change(scrollToActiveTab);
        // Call scrollToActiveTab on initial load
        scrollToActiveTab();
        // Recheck when window is resized
        $(window).resize(scrollToActiveTab);
    }

    // Recheck when window is resized
    $(window).resize(scrollToActiveTab);
});
