<?php

namespace Pay360\Payments\Model\Api;

use Pay360\Payments\Model\Api\AbstractApi;
use Pay360\Payments\Model\Config;
use Pay360\Payments\Helper\Data as Pay360Helper;

class Nvp extends AbstractApi
{
    protected $_orderIncrementId;

    protected $_lastOrder;

    /**
     * @param \Pay360\Payments\Model\Transaction
     */
    protected $_transaction;

    /**
     * @param Pay360Helper
     */
    protected $_pay360Helper;

    /**
     * @param \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Nvp constructor
     *
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Pay360\Payments\Helper\Logger $logger
     * @param Pay360Helper $pay360Helper
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Pay360\Payments\Model\Config $config
     * @param \Pay360\Payments\Model\Session $pay360session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Pay360\Payments\Model\Transaction $transaction
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\Framework\HTTP\Client\Curl $curlClient
     * @param array
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Pay360\Payments\Helper\Logger $logger,
        Pay360Helper $pay360Helper,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Pay360\Payments\Model\Config $config,
        \Pay360\Payments\Model\Session $pay360session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Pay360\Payments\Model\Transaction $transaction,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\HTTP\Client\Curl $curlClient,
        array $data = []
    ) {
        parent::__construct(
            $customerAddress,
            $logger,
            $localeResolver,
            $regionFactory,
            $config,
            $pay360session,
            $storeManager,
            $httpClientFactory,
            $jsonEncoder,
            $jsonDecoder,
            $curlClient,
            $data
        );
        $this->_countryFactory = $countryFactory;
        $this->_pay360Helper = $pay360Helper;
        $this->_lastOrder = $order;
        $this->_transaction = $transaction;
    }

    public function getPageStyle()
    {
        return $this->getConfigData('page_style');
    }

    public function getOrder()
    {
        if (!isset($this->_orderIncrementId)) {
            $this->_orderIncrementId = $this->_pay360Helper->getOrderId();
        }
        if (!$this->_lastOrder->getId()) {
            $this->_lastOrder->loadByIncrementId($this->_orderIncrementId);
        }

        return $this->_lastOrder;
    }

    public function getAddressDetails($address)
    {
        $countryCode = $address->getCountryId();
        $country = $this->_countryFactory->create()->loadByCode($countryCode);

        return [
            'line1' => $address->getStreetLine(1),
            'line2' => $address->getStreetLine(2),
            'city' => $address->getCity(),
            'region' => $address->getRegionCode(),
            'postcode' => $address->getPostcode(),
            'countryCode' => $country->getData('iso3_code')
        ];
    }

    /**
     * send payment authorization request to pay360
     *
     * @param string
     * @param string
     * @param string
     *
     * @return array
     */
    public function callDoPayment($orderId, $returnUrl = null, $cancelUrl = null)
    {
        $nvpArr = [
            'transaction' => [
                'merchantReference' => $orderId,
                'money' => [
                    'amount' => [
                        'fixed' => $this->getOrder()->getGrandTotal()
                    ],
                    'currency' => $this->getOrder()->getOrderCurrencyCode()
                ],
                'description' => 'Hosted Payment Transaction',
                'commerceType' => $this->getCommerceType(), //  Possible Values: ECOM, MOTO, CNP
                'channel' => 'WEB', //  Possible Values: WEB, MOBILE, SMS, RETAIL, MOTO, IVR, OTHER
                'deferred' => $this->_config->getValue('payment/pay360/payment_action') == \Pay360\Payments\Model\Standard::PAYMENT_TYPE_AUTH ? 1 : 0, // Indicates if you want the Payment to be Authorised and Captured separately. Capture immediately
            ],
            'customer' => [
                'registered' => false
            ],
            'session' => [
                'preAuthCallback' => [
                    'url' => $this->getPreAuthCallBackUrl(),
                    'format' => 'REST_JSON'
                ],
                'postAuthCallback' => [
                    'url' => $this->getPostAuthCallBackUrl(),
                    'format' => 'REST_JSON'
                ],
                'transactionNotification' => [
                    'url' => $this->getTransactionNotificationUrl(),
                    'format' => 'REST_JSON'
                ],
                'returnUrl' => [
                    'url' => empty($returnUrl) ? $this->getReturnUrl() : $returnUrl
                ],
                'cancelUrl' => [
                    'url' => empty($cancelUrl) ? $this->getCancelUrl() : $cancelUrl
                ]
            ]
        ];

        // setup 3DS
        if ($this->_config->getValue('payment/pay360/3ds')) {
            //$nvpArr['customer']['email'] = $this->getOrder()->getCustomerEmail();
        }

        // check if customer logged in -> create new profile
        if ($this->getOrder()->getCustomerId()) {
            $nvpArr['customer']['registered'] = true; // register customer to Pay360 by default
            $nvpArr['customer']['identity'] = [
                'merchantCustomerId' => $this->getOrder()->getCustomerId()
            ];
        }
        // customer information for both guest and logged in customer
        $nvpArr['customer']['details'] = $this->getCustomerDetails($this->getOrder());

        // look and feel for payment
        if ($this->_config->getValue('payment/pay360/skin_code') || $this->_config->getValue('payment/pay360/custom_skin_code')) {
            if ($this->_config->getValue('payment/pay360/custom_skin_code')) {
                $nvpArr['session']['skin'] = $this->_config->getValue('payment/pay360/custom_skin_code');
            } else {
                $nvpArr['session']['skin'] = $this->_config->getValue('payment/pay360/skin_code');
            }
        }

        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360/installation_id'), Config::HPP_MAKE_PAYMENT);
        $this->setResourceEndpoint($url);

        return $this->call($nvpArr);
    }

    public function getCustomerDetails($order)
    {
        $billingAddress = $order->getBillingAddress();
        return [
            'name' => $billingAddress->getFirstName().' '.$billingAddress->getLastName(),
            'address' => $this->getAddressDetails($billingAddress),
            'telephone' => $billingAddress->getTelephone(),
            'emailAddress' => $order->getCustomerEmail(), // is this enough for 3DS ?
            'ipAddress' => $order->getRemoteIp(),
            'defaultCurrency' => $order->getOrderCurrencyCode()
        ];
    }

    public function callDoCapture($transaction, $order)
    {
        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360/installation_id'), Config::API_MAKE_CAPTURE);
        $url = str_replace('{transaction_id}', $transaction->getTransactionId(), $url);
        $this->setResourceEndpoint($url);

        $nvpArr = [
            'transaction' => [
                'commerceType' => $this->getCommerceType(),
                'channel' => 'WEB',
                'merchantRef' => $order->getIncrementId(),
                'description' => ''
            ]
        ];
        return $this->call($nvpArr);
    }

    public function callGetTransactionDetails($transaction)
    {
        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360/installation_id'), Config::API_GET_DETAIL);
        $url = str_replace('{transaction_id}', $transaction->getTransactionId(), $url);
        $this->setResourceEndpoint($url);

        return $this->call();
    }

    public function callRefundTransaction($order, $amount)
    {
        $transaction = $this->_transaction->load($order->getIncrementId(), 'merchant_ref');
        $nvpArr = [
            'transaction' => [
                'amount' => $amount,
                'currency' => $order->getOrderCurrencyCode(),
                'merchantRef' => $transaction->getMerchantRef(),
                'commerceType' => $this->getCommerceType(),
                'channel' => 'WEB'
            ]
        ];

        $url = str_replace('{installation_id}', $this->_config->getValue('payment/pay360/installation_id'), Config::API_MAKE_REFUND);
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
    public function call($nvpArr = [])
    {
        return $this->request($nvpArr);
    }
}
