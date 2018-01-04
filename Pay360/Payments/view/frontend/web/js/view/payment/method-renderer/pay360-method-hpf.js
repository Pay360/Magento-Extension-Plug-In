define( [ 'jquery', 'Magento_Checkout/js/view/payment/default', 'mage/url' ], function ($, Component, url) {
    var checkoutConfig = window.checkoutConfig.payment;
    'use strict';
    return Component.extend(
        {
           redirectAfterPlaceOrder: false,
           isInAction: iframe.isInAction,
           defaults: {
               template: 'Magento_Paypal/payment/iframe-methods',
           paymentReady: false
           },
           getMethodImage: function () {
               return checkoutConfig.image[this.item.method];
           },
           getInstructions: function () {
               return checkoutConfig.instructions[this.item.method];
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

           /**
            * Get action url for payment method iframe.
            * @returns {String}
            */
           getActionUrl: function () {
               return this.isInAction() ? window.checkoutConfig.payment.pay360hpf.actionUrl[this.getCode()] : '';
           },

           /**
            * Places order in pending payment status.
            */
           placePendingPaymentOrder: function () {
               if (this.placeOrder()) {
                   fullScreenLoader.startLoader();
                   this.isInAction(true);
                   // capture all click events
                   document.addEventListener('click', iframe.stopEventPropagation, true);
               }
           },

           /**
            * @return {*}
            */
           getPlaceOrderDeferredObject: function () {
               var self = this;

               return this._super().fail(function () {
                   fullScreenLoader.stopLoader();
                   self.isInAction(false);
                   document.removeEventListener('click', iframe.stopEventPropagation, true);
               });
           },
           /**
            * After place order callback
            */
           afterPlaceOrder: function () {
               if (this.iframeIsLoaded) {
                   document.getElementById(this.getCode() + '-iframe').contentWindow.location.reload();
               }

               this.paymentReady(true);
               this.iframeIsLoaded = true;
               this.isPlaceOrderActionAllowed(true);
               fullScreenLoader.stopLoader();
           },

           /**
            * Hide loader when iframe is fully loaded.
            */
           iframeLoaded: function () {
               fullScreenLoader.stopLoader();
           }
        });
});
