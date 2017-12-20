<?php

namespace Pay360\Payments\Model\Api;

use Pay360\Payments\Model\Api\AbstractApi;

class Nvp extends AbstractApi {

    protected $_checkoutSession;

    protected $_order;

    protected $_transaction;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Psr\Log\LoggerInterface $logger,
        Logger $customLogger,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Pay360\Payments\Model\Config $config,
        \Pay360\Payments\Model\Session $pay360session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Pay360\Payments\Model\Transaction $transaction,
        array $data = []
    ) {
        parent::__construct($customerAddress, $logger, $customLogger, $localeResolver, $regionFactory, $config, $pay360session, $storeManager, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_order = $order;
        $this->_transaction = $transaction;
    }

    public function getPageStyle() {
        return $this->getConfigData('page_style');
    }

    public function getOrder() { 
        if(!isset($this->_orderIncrementId)) {
            $this->_orderIncrementId = $this->_checkoutSession->getLastRealOrderId();
        }
        if(!is_object($this->_order)) {
            $this->_order->loadByIncrementId($this->_orderIncrementId);
        }  
        return $this->_order;
    }

    public function getAddressDetails($address)
    {
        return array(
            'line1' => $address->getStreet(1),
            'line2' => $address->getStreet(2),
            'city' => $address->getCity(),
            'region' => $address->getRegionCode(),
            'postcode' => $address->getPostcode(),
            'countryCode' => $address->getCountry()
        );
    }

    public function callDoPayment($returnUrl = null, $cancelUrl = null) {
        $nvpArr = array(
            'transaction' => array(
                'merchantReference' => $this->_checkoutSession->getLastOrderId(),
                'money' => array(
                    'amount' => array(
                        'fixed' => $this->getOrder()->getGrandTotal()
                    ),
                    'currency' => $this->getOrder()->getOrderCurrencyCode()
                ),
                'description' => 'Hosted Payment Transaction',
                'commerceType' => 'ECOM', //  Possible Values: ECOM, MOTO, CNP
                'channel' => 'WEB', //  Possible Values: WEB, MOBILE, SMS, RETAIL, MOTO, IVR, OTHER
                'deferred' => $this->_config->getValue('payment/pay360_standard/payment_action') == Pay360_Payments_Model_Standard::PAYMENT_TYPE_AUTH ? 1 : 0, // Indicates if you want the Payment to be Authorised and Captured separately. Capture immediately
            ),
            'customer' => array(
                'registered' => false
            ),
            'session' => array(
                'preAuthCallback' => array(
                    'url' => $this->getPreAuthCallBackUrl(),
                    'format' => 'REST_JSON'
                ),
                'postAuthCallback' => array(
                    'url' => $this->getPostAuthCallBackUrl(),
                    'format' => 'REST_JSON'
                ),
                'transactionNotification' => array(
                    'url' => $this->getTransactionNotificationUrl(),
                    'format' => 'REST_JSON'
                ),
                'returnUrl' => array(
                    'url' => empty($returnUrl) ? $this->getReturnUrl() : $returnUrl
                ),
                'cancelUrl' => array(
                    'url' => empty($cancelUrl) ? $this->getCancelUrl() : $cancelUrl
                )
            )
        );

        // check if customer logged in -> create new profile
        if ($this->getOrder()->getCustomerId()) {
            $nvpArr['customer']['registered'] = true; // register customer to Pay360 by default
            $nvpArr['customer']['identity'] = array(
                'merchantCustomerId' => $this->getOrder()->getCustomerId()
            );
        }
        // customer information for both guest and logged in customer
        $nvpArr['customer']['details'] = array(
            'name' => $this->_order->getCustomerName(),
            'address' => $this->getAddressDetails($this->getOrder()->getBillingAddress()),
            'telephone' => $this->getOrder()->getBillingAddress()->getTelephone(),
            'emailAddress' => $this->getOrder()->getCustomerEmail(),
            'ipAddress' => $this->getOrder()->getRemoteIp(),
            'defaultCurrency' => $this->getOrder()->getOrderCurrencyCode()
        );

        // look and feel for payment
        if ($this->_config->getValue('payment/pay360_standard/skin_code') || $this->_config->getValue('payment/pay360_standard/custom_skin_code')) {
            if ($this->_config->getValue('payment/pay360_standard/custom_skin_code')) {
                $nvpArr['session']['skin'] = $this->_config->getValue('payment/pay360_standard/custom_skin_code');
            }
            else {
                $nvpArr['session']['skin'] = $this->_config->getValue('payment/pay360_standard/skin_code');
            }
        }

        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360_standard/installation_id'), self::HPP_MAKE_PAYMENT);
        $this->setResourceEndpoint($url);

        return $this->call($nvpArr);
    }

    public function callDoCapture($transaction, $order) {
        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360_standard/installation_id'), self::API_MAKE_CAPTURE);
        $url = str_replace('{transaction_id}', $transaction->getTransactionId(), $url);
        $this->setResourceEndpoint($url);

        $nvpArr = array(
            'transaction' => array(
                'commerceType' => 'ECOM',
                'channel' => 'WEB',
                'merchantRef' => $order->getId(),
                'description' => ''
            )
        );
        return $this->call($nvpArr);
    }

    public function callGetTransactionDetails($transaction) {
        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360_standard/installation_id'), self::API_GET_DETAIL);
        $url = str_replace('{transaction_id}', $transaction->getTransactionId(), $url);
        $this->setResourceEndpoint($url);

        return $this->call();
    }

    public function callRefundTransaction($order, $amount) {
        $transaction = $this->_transaction->load($order->getId(), 'merchant_ref');
        $nvpArr = array(
            'transaction' => array(
                'amount' => $amount,
                'currency' => $order->getOrderCurrencyCode(),
                'merchantRef' => $transaction->getMerchantRef(),
                'commerceType' => 'ECOM',
                'channel' => 'WEB'
            )
        );

        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360_standard/installation_id'), self::API_MAKE_REFUND);
        $url = str_replace('{transaction_id}', $transaction->getTransactionId(), $url);
        $this->setResourceEndpoint($url);

        return $this->call($nvpArr);
    }

    /**
     * Function to perform the API call to Pay360 using Basic Authorization for HPP
     *
     * @param $nvpArr array NVP params array
     * @return array|boolean an associtive array containing the response from the server or false in case of error.
     */
    public function call($nvpArr = array()) {
        $headers = [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Basic ".base64_encode($this->_config->getValue('payment/pay360_standard/api_user') . ':'. $this->_config->getValue('payment/pay360_standard/api_password'))
        ];

        return $this->request($headers, $nvpArr);
    }

}
