document.addEventListener('DOMContentLoaded', function () {
    if (popupData.popupIDs) {

        popupData.popupIDs.forEach(function (pId) {
            if (popupData.popups_data && popupData.popups_data[pId]) { 
                var popupTriggers = document.querySelectorAll('.popup-trigger-' + pId);
                var triggerOption = popupData.popups_data[pId].popup_trigger;
                var displayMode = popupData.popups_data[pId].popup_display_mode;    
                var popupTriggers = document.querySelectorAll('.popup-trigger-' + pId);
                var inactivityTime = parseInt(popupData.popups_data[pId].popup_inactivity_time) * 1000;
                
                var inactivityTimeout;
                
                function resetInactivityTimeout() {
                    clearTimeout(inactivityTimeout);
                    inactivityTimeout = setTimeout(function () {
                        showPopup(pId);
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
                        if (!(popupData.popups_data[pId].disable_mobile_editor && isMobileDevice())) {
                            setTimeout(function () {
                                showPopup(pId);
                            }, parseInt(popupData.popups_data[pId].popup_delay) * 1000);
                        }
                        break;
                    case 'click':
                        popupTriggers.forEach((trigger, index) => {
                            trigger.addEventListener('click', function() {
                                event.preventDefault();
                                showPopup(pId);
                            });
                        });
                        break;
                    case 'hover':
                        popupTriggers.forEach((trigger) => {
                            trigger.addEventListener('mouseenter', function() {
                                showPopup(pId);
                            });
                        });
                        break;
                    case 'exit_intent':
                        document.addEventListener('mouseout', function(event) {
                            if (!event.toElement && !event.relatedTarget && event.clientY < 10) {
                                showPopup(pId);
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


function showPopup(pId = false) {
    if (popupData.popups_data[pId].disable_mobile_editor && isMobileDevice()) {
        return;
    }
    const selector = pId !== false ? `.popup-${pId}` : '.popup';
    var popups = document.querySelectorAll(selector);
    popups.forEach(function (popup) {

        // Check if the popup is already open
        if (!popup.classList.contains('open')) {
            let needMemoryUpdate = jQuery(popup).data("needMemoryUpdate");
            if( needMemoryUpdate ) {
                jQuery.ajax({
                    url: needMemoryUpdate,
                    type: "get",
                    dataType: "html",
                    success: function (data) {
                        let styleRegex = new RegExp("<style[^>]*id[\\s]?=[\\s]?['\"]core-block-supports-inline-css['\"][\\s\\S]*?<\/style>").exec(data);
                        let saveHtml = '';
                        if (styleRegex && styleRegex[0]) {
                            jQuery(popup).find('.popup-content').append(styleRegex[0]);
                            saveHtml += styleRegex[0];
                        }
                        if (jQuery(data).find('#opb-popup-content').length) {
                            saveHtml += jQuery(data).find('#opb-popup-content').html();
                            jQuery(popup).find('.popup-content').append(jQuery(data).find('#opb-popup-content').html());
                        }

                        var dataToSave = {
                            action: 'save_popup_content',
                            post_id: pId,
                            nonce: popupData.nonce,
                            content: saveHtml,
                        };
                        // console.log(dataToSave)
                        jQuery.post(popupData.ajaxUrl, dataToSave, function(response) {});

                        insideShowPopup(popup, pId);
                    },
                });
            } else {
                insideShowPopup(popup, pId);
            }
        }
    })
}

function insideShowPopup(popup, pId){
    var displayMode = popupData.popups_data[pId].popup_display_mode;
    var popup_content = jQuery(popup).find('.popup-content')[0];
    var popupOverlay = document.querySelector('.popup-overlay');
    var overlayEnabled = popupData.popups_data[pId].popup_overlay_enabled;
    var overlayColor = popupData.popups_data[pId].popup_overlay_color;

    if (popup && popupOverlay) {
        popup.classList.add('open');

        if (overlayEnabled == '0') {
            popupOverlay.style.display = 'none';
        } else {
            popupOverlay.style.display = 'block';
            popupOverlay.style.backgroundColor = overlayColor;
        }

        var popupSize = popupData.popups_data[pId].popup_size;
        if (popupSize) {
            if (popupSize !== 'custom') {
                popup.style.width = popupSize;
                popup_content.style.width = popupSize;
            } else if (popupSize === 'custom') {
                var customWidth = popupData.popups_data[pId].popup_width;
                var customHeight = popupData.popups_data[pId].popup_height;
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
        var closeOption = popupData.popups_data[pId].popup_close_button;

        if (closeOption === 'close') {
            var closeButton = popup.querySelector('.popup-close-button');
            if (closeButton) {
                closeButton.style.display = 'flex';
                closeButton.addEventListener('click', function() {
                    hidePopup(pId);
                });
            }
        }
        if (closeOption === 'escape') {
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    hidePopup(pId);
                }
            });
        }

        // Apply positioning class based on popup_position value
        var popupPosition = popupData.popups_data[pId].popup_position;
        popup.classList.add('popup-' + popupPosition);

        var autocloseDelay = parseInt(popupData.popups_data[pId].popup_autoclose_delay);
        if (autocloseDelay > 0) {
            setTimeout(function () {
                hidePopup(pId);
            }, autocloseDelay * 1000); // multiply by 1000 to convert seconds to milliseconds
        }
    }
    if (displayMode === "once_per_session") {
        sessionStorage.setItem('popupShown_' + pId, true);
    } else if (displayMode === "once") {
        localStorage.setItem('popupShown_' + pId, true);
    }
}

function hidePopup(pId = false) {
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