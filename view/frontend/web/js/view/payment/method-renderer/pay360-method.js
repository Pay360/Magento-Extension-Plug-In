define([
  'jquery',
  'Magento_Checkout/js/view/payment/default',
  'mage/url',
  'Magento_Customer/js/customer-data'
  ],
  function ($, Component, url) {
    var checkoutConfig = window.checkoutConfig.payment;
    'use strict';
    return Component.extend(
        {
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Pay360_Payments/payment/pay360'
            },
            getMethodImage: function () {
                return checkoutConfig.image[this.item.method];
            },
            getInstructions: function () {
                return checkoutConfig[this.item.method].description;
            },
            afterPlaceOrder: function () {
                window.location.replace(url.build('pay360/gateway/redirect/'));
            }
        }
    );
});
