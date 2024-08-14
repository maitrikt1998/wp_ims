(function ($) {
    'use strict';
})(jQuery);
class Ocean_Pixel_Helper {
    constructor(options = {}) {
        this.options = {};
        if (options) {
            Object.assign(this.options, options);
        }
    }

    singleProductAddToCartEventBind(variations_data) {
        var single_btn = document.querySelectorAll("button[class*='btn-buy-shop'],button[class*='single_add_to_cart_button'], button[class*='add_to_cart']");
        if (single_btn.length > 0) {
            const $this = this;
            single_btn.forEach(function (el_button) {
                el_button.addEventListener("click", (event) => {
                    $this.add_to_cart_click(event, variations_data)
                });
            });
        }
    }

    get_variation_data_by_id(variations_data, variation_id) {
        var r_val = "";
        if (variations_data.available_variations.length > 0) {
            variations_data.available_variations.forEach((element, index) => {
                if (element.variation_id == variation_id) {
                    r_val = element;
                }
            });
            return r_val;
        }
    }

    /*
     * below code run while add to cart on product page. 
     * ( Event=>  add_to_cart)
     */
    add_to_cart_click(event, variations_data) {
        var this_var = event.currentTarget;

        var varPrice = ocean_track_pdetails.ocean_track_price;
        var vari_data = "";
        var variation_id = "";
        var variation_id_obj = document.getElementsByClassName("variation_id");
        if (variation_id_obj.length > 0) {
            variation_id = document.getElementsByClassName("variation_id")[0].value;
        }

        if (variation_id != "") {
            vari_data = this.get_variation_data_by_id(variations_data, variation_id);

            if (vari_data.display_price) {
                varPrice = vari_data.display_price;
            } else if (vari_data.display_regular_price) {
                varPrice = vari_data.display_regular_price;
            }
        }

        if (this.options.gtm_active != "" && this.options.gtm_add_to_cart != "") {
            const gtmData = {
                'event': 'addToCart',
                'ecommerce': {
                    'currencyCode': this.options.currency,
                    'add': {
                        'products': [{
                            'id': ocean_track_pdetails.ocean_track_identify,
                            'name': ocean_track_pdetails.ocean_track_name,
                            'price': varPrice,
                            'quantity': jQuery(this_var).parent().find("input[name=quantity]").val(),
                        }]
                    }
                }
            };
            dataLayer.push(gtmData);
        }

        /* facebook pixel*/
        if (this.options.fb_pixel_active != "" && this.options.fb_pixel_add_to_cart != "") {
            var quantity = jQuery(this_var).parent().find("input[name=quantity]").val();

            const fb_track_data = {
                content_type: 'product',
                content_name: ocean_track_pdetails.ocean_track_name,
                content_ids: [ocean_track_pdetails.ocean_track_id],
                currency: this.options.currency,
                value: varPrice,
                contents: [{ id: ocean_track_pdetails.ocean_track_id, quantity: quantity }]

            };

            fbq('track', 'AddToCart', fb_track_data);
        }

        /* tiktok pixel*/
        if (this.options.tiktok_pixel_active != "" && this.options.tiktok_pixel_add_to_cart != "") {
            var quantity = jQuery(this_var).parent().find("input[name=quantity]").val();
            var productId = ocean_track_pdetails.ocean_track_id;
            var productName = ocean_track_pdetails.ocean_track_name;
            var productPrice = varPrice; 
        
            const tiktok_track_data = {
                contents: [{
                    content_id: productId,
                    content_name: productName,
                    quantity: parseInt(quantity, 10),
                    price: parseFloat(productPrice).toFixed(2),
                }],
                content_type: 'product',
                value: parseFloat(productPrice * quantity).toFixed(2),
                currency: this.options.currency
            };
        
            ttq.track('AddToCart', tiktok_track_data);
        }

        /* pinterest pixel*/
        if (this.options.pinterest_tag_active != "" && this.options.pinterest_tag_track_addtocart != "") {
            var quantity = jQuery(this_var).parent().find("input[name=quantity]").val();
            var productId = ocean_track_pdetails.ocean_track_id;
            var productName = ocean_track_pdetails.ocean_track_name;
            var productPrice = varPrice;
        
            var pinterest_track_data = {
                value: parseFloat(productPrice).toFixed(2),
                order_quantity: parseInt(quantity, 10),
                currency: this.options.currency
            };
        
            pintrk('track', 'addtocart', pinterest_track_data);
        }
        
    }
}