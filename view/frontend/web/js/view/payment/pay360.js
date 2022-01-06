define([ 'uiComponent', 'Magento_Checkout/js/model/payment/renderer-list' ], function ( Component, rendererList) {
    'use strict';
    var config = window.checkoutConfig.payment;

    if (config['pay360'].isActive) {
        rendererList.push(
            {
                type: 'pay360',
                component: 'Pay360_Payments/js/view/payment/method-renderer/pay360-method'
            }
        );
    }

    if (config['pay360hpf'].isActive) {
        rendererList.push(
            {
                type: 'pay360',
                component: 'Pay360_Payments/js/view/payment/method-renderer/pay360-method-hpf'
            }
        );
    }
    return Component.extend({});
});
