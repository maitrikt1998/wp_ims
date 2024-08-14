document.addEventListener('DOMContentLoaded', function () {
    if (popupPhpData.popupIDs) {
        popupPhpData.popupIDs.forEach(function (pId) {
            if (popupPhpData.popups_php_data && popupPhpData.popups_php_data[pId]) { 
                var popupTriggers = document.querySelectorAll('.popup-trigger-' + pId);
                var triggerOption = popupPhpData.popups_php_data[pId].popup_trigger;
                var displayMode = popupPhpData.popups_php_data[pId].popup_display_mode;
                var inactivityTime = parseInt(popupPhpData.popups_php_data[pId].popup_inactivity_time) * 1000;
                
                var inactivityTimeout;
                
                function resetInactivityTimeout() {
                    clearTimeout(inactivityTimeout);
                    inactivityTimeout = setTimeout(function () {
                        showPopupPHP(pId);
                    }, inactivityTime);
                }

                if (displayMode === "once_per_session" && sessionStorage.getItem('popupShown_' + pId)) {
                    return;
                } else if (displayMode === "once" && localStorage.getItem('popupShown_' + pId)) {
                    return;
                }

                // Trigger based on option selected
                switch (triggerOption) {
                    case 'load':
                        if (!(popupPhpData.popups_php_data[pId].disable_mobile_editor && isMobileDevice())) {
                            setTimeout(function () {
                                showPopupPHP(pId);
                            }, parseInt(popupPhpData.popups_php_data[pId].popup_delay) * 1000);
                        }
                        break;
                    case 'click':
                        popupTriggers.forEach((trigger, index) => {
                            trigger.addEventListener('click', function() {
                                event.preventDefault();
                                showPopupPHP(pId);
                            });
                        });
                        break;
                    case 'hover':
                        popupTriggers.forEach((trigger) => {
                            trigger.addEventListener('mouseenter', function() {
                                showPopupPHP(pId);
                            });
                        });
                        break;
                    case 'exit_intent':
                        document.addEventListener('mouseout', function(event) {
                            if (!event.toElement && !event.relatedTarget && event.clientY < 10) {
                                showPopupPHP(pId);
                            }
                        });
                        break;
                    case 'inactivity':
                        // For inactivity, directly set the timeout without initial delay
                        resetInactivityTimeout();
                        ['mousemove', 'scroll', 'keypress'].forEach(function (event) {
                            document.addEventListener(event, resetInactivityTimeout);
                        });
                        break;
                }              
            }

        });

    }
});

function showPopupPHP(pId = false) {
    
    if (popupPhpData.popups_php_data[pId] && !popupPhpData.popups_php_data[pId].disable_mobile_php && isMobileDevice()) {
        return;
    }
    const selector = pId !== false ? `.popup-${pId}` : '.popup';
    var popups = document.querySelectorAll(selector);
    popups.forEach(function (popup) {

        let needMemoryUpdate = jQuery(popup).data("needMemoryUpdate");
        if( needMemoryUpdate ) {
            jQuery.ajax({
                url: needMemoryUpdate,
                type: "get",
                dataType: "html",
                success: function (data) {
                    let styleRegex = new RegExp("<style[^>]*id[\\s]?=[\\s]?['\"]core-block-supports-inline-css['\"][\\s\\S]*?<\/style>").exec(data);
                    let saveHtml = '';
                    if( styleRegex[0] ) {
                        jQuery(popup).find('.popup-content').append(styleRegex[0]);
                        saveHtml += styleRegex[0];
                    }
                    if( jQuery(data).find('#opb-popup-content') ) {
                        saveHtml += jQuery(data).find('#opb-popup-content').html();
                        jQuery(popup).find('.popup-content').append(jQuery(data).find('#opb-popup-content').html());
                    }

                    var dataToSave = {
                        action: 'save_popup_content',
                        post_id: pId,
                        nonce: popupPhpData.nonce,
                        content: saveHtml,
                    };
                    // console.log(dataToSave)
                    jQuery.post(popupPhpData.ajaxUrl, dataToSave, function(response) {});

                    insideShowPopupPHP(popup, pId);
                },
            });
        } else {
            insideShowPopupPHP(popup, pId);
        }
    })
}

function insideShowPopupPHP(popup, pId){

    var displayMode = popupPhpData.popups_php_data[pId].popup_display_mode;
    var popup_content = jQuery(popup).find('.popup-content')[0];
    var popupOverlay = document.querySelector('.popup-overlay');
    var overlayEnabled = popupPhpData.popups_php_data[pId].popup_overlay_enabled;
    var overlayColor = popupPhpData.popups_php_data[pId].popup_overlay_color;

    if (popup && popupOverlay) {
        popup.classList.add('open');

        if (overlayEnabled == '0') {
            popupOverlay.style.display = 'none';
        } else {
            popupOverlay.style.display = 'block';
            popupOverlay.style.backgroundColor = overlayColor;
        }

        var popupSize = popupPhpData.popups_php_data[pId].popup_size;
        if (popupSize) {
            if (popupSize !== 'custom') {
                popup.style.width = popupSize;
                popup_content.style.width = popupSize;
            } else if (popupSize === 'custom') {
                var customWidth = popupPhpData.popups_php_data[pId].popup_width;
                var customHeight = popupPhpData.popups_php_data[pId].popup_height;
                if (customWidth && customHeight) {
                    popup.style.width = customWidth + 'px';
                    popup_content.style.width = customWidth + 'px';
                    popup.style.height = customHeight + 'px';
                    popup_content.style.height = customHeight + 'px';
                }
            }
        }

        document.body.classList.add('popup-open');
        popup.style.display = 'flex';
        popup.classList.add('open');

        // Add event listener for close button
        var closeOption = popupPhpData.popups_php_data[pId].popup_close_button;

        if (closeOption === 'close') {
            var closeButton = popup.querySelector('.popup-close-button');
            if (closeButton) {
                closeButton.style.display = 'flex';
                closeButton.addEventListener('click', function() {
                    hidePopupPHP(pId);
                });
            }
        }
        if (closeOption === 'escape') {
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    hidePopupPHP(pId);
                }
            });
        }

        // Apply positioning class based on popup_position value
        var popupPosition = popupPhpData.popups_php_data[pId].popup_position;
        popup.classList.add('popup-' + popupPosition);

        var autocloseDelay = parseInt(popupPhpData.popups_php_data[pId].popup_autoclose_delay);
        if (autocloseDelay > 0) {
            setTimeout(function () {
                hidePopupPHP(pId);
            }, autocloseDelay * 1000); // multiply by 1000 to convert seconds to milliseconds
        }
    }
    if (displayMode === "once_per_session") {
        sessionStorage.setItem('popupShown_' + pId, true);
    } else if (displayMode === "once") {
        localStorage.setItem('popupShown_' + pId, true);
    }
}

function hidePopupPHP(pId = false) {
    const selector = pId !== false ? `.popup-${pId}` : '.popup';
    var popup = document.querySelector(selector);
    var popupOverlay = document.querySelector('.popup-overlay');
    if (popup && popupOverlay) {
        popup.style.display = 'none';
        popup.classList.remove('open');
        popupOverlay.style.display = 'none';
        document.body.classList.remove('popup-open');
    }
}

function isMobileDevice() {
    return /Mobi|Android/i.test(navigator.userAgent);
}