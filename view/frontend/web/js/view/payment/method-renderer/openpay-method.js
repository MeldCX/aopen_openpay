/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Aopen_Openpay/payment/openpay'
            },
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    this.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(
                            function () {
                                if (self.redirectAfterPlaceOrder) {
                                    if (!location.origin) location.origin = location.protocol + "//" + location.host;
                                    window.location.replace(location.origin + '/openpay/handoverurl/index');
                                    return false;
                                }
                            }
                        );

                    return true;
                }

                return false;
            },
            /** Returns payment method instructions */
            getInstructions: function() {
                return window.checkoutConfig.payment.instructions[this.item.method];
            }
        });
    }
);
