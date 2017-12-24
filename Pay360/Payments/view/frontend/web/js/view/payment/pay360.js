define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'pay360',
                component: 'Pay360_Payments/js/view/payment/method-renderer/pay360-method'
            }
        );
        return Component.extend({});
    }
);
