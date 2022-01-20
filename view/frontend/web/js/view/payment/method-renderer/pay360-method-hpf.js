define([ 'jquery',
  'Magento_Checkout/js/view/payment/default',
  'mage/url',
  'Magento_Paypal/js/model/iframe',
  'Magento_Checkout/js/model/full-screen-loader',
  'Magento_Customer/js/customer-data'
  ],
  function ($, Component, url, iframe, fullScreenLoader) {
    var checkoutConfig = window.checkoutConfig.payment;
    'use strict';
    return Component.extend(
        {
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Pay360_Payments/payment/pay360hpf',
            },
            isInAction: iframe.isInAction,

            getMethodImage: function () {
                return checkoutConfig.image[this.item.method];
            },
            getInstructions: function () {
                return checkoutConfig[this.item.method].description;
            },

            paymentReady: function () {
                return true;
            },

            /**
             * @return {exports}
             */
            initObservable: function () {
                this._super().observe('paymentReady');
                return this;
            },

            /**
             * @return {*}
             */
            isPaymentReady: function () {
                return this.paymentReady();
            },

            getiFrameWidth: function () {
                return checkoutConfig.pay360hpf.width;
            },

            getiFrameHeight: function () {
                return checkoutConfig.pay360hpf.height;
            },

            /**
             * Get action url for payment method iframe.
             * @returns {String}
             */
            getActionUrl: function () {
                return this.isInAction() ? checkoutConfig.pay360hpf.actionUrl : '';
            },

            /**
             * After place order callback
             */
            afterPlaceOrder: function () {
                this.isInAction(true);
                if (this.iframeIsLoaded) {
                    document.getElementById('hpf-iframe').contentWindow.location.reload();
                }

                this.paymentReady(true);
                this.iframeIsLoaded = true;
                this.isPlaceOrderActionAllowed(false); // disable place order button after finished loading iframe
                fullScreenLoader.stopLoader();
            },

            /**
             * Hide loader when iframe is fully loaded.
             */
            iframeLoaded: function () {
                fullScreenLoader.stopLoader();
            }
        }
    );
});
