<?php
/**
 * Pay360 API Wrapper Abstract
 */
namespace Pay360\Payments\Model\Api;

use Magento\Payment\Helper\Formatter;
use Magento\Payment\Model\Method\Logger;
use Pay360\Payments\Model\Config;

/**
 * Abstract class for Paypal API wrappers
 */
abstract class AbstractApi extends \Magento\Framework\DataObject
{
    use Formatter;

    /**
     * Config instance
     *
     * @var \Pay360\Payments\Model\Config
     */
    protected $_config;

    /**
     * Customer address
     *
     * @var \Magento\Customer\Helper\Address
     */
    protected $_customerAddress;

    /**
     * @var \Pay360\Payments\Helper\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $_httpClientFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curlClient;

    /**
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Pay360\Payments\Helper\Logger $logger
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Pay360\Payments\Helper\Logger $logger,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Pay360\Payments\Model\Config $config,
        \Pay360\Payments\Model\Session $pay360session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\HTTP\Client\Curl $curlClient,
        array $data = []
    ) {
        $this->_customerAddress = $customerAddress;
        $this->_logger = $logger;
        $this->_localeResolver = $localeResolver;
        $this->_regionFactory = $regionFactory;
        $this->_config = $config;
        $this->_pay360Session = $pay360session;
        $this->_storeManager = $storeManager;
        $this->_httpClientFactory = $httpClientFactory;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;
        $this->_curlClient = $curlClient;
        parent::__construct($data);
    }

    /**
     * hardcoded function for Pay360 Communication URL. this is not configurable
     * $env : test/production
     * $type : api/hosted
     */
    public function getComUrl() {
        $type = strpos($this->getResourceEndpoint(), '/acceptor/rest') !== false ? 'api' : 'hosted';
        $resource = [
            'test' => [
                'api' => 'https://api.mite.pay360.com',
                'hosted' => 'https://api.mite.pay360.com'
            ],
            'production' => [
                'api' => 'https://api.pay360.com',
                'hosted' => 'https://api.pay360.com'
            ]
        ];

        $env = $this->_config->getValue('payment/pay360/test') ? 'test' : 'production';
        if (in_array($env, ['test', 'production']) && in_array($type, ['api', 'hosted'])) {
            return $resource[$env][$type];
        }
        return false;
    }

    /**
     * get current server name
     */
    public function getServerName() {
        if (!$this->hasServerName()) {
            $this->setServerName($_SERVER['SERVER_NAME']);
        }
        return $this->getData('server_name');
    }

    /**
     * get config data . if not available. set config data
     */
    public function getConfigData($key, $default=false, $storeId = null) {
        if (!$this->hasData($key))
		{
            if ($storeId === null && $this->getPayment() instanceof Varien_Object) {
                $storeId = $this->getPayment()->getOrder()->getStoreId();
            }
            $value = $this->_config->getValue('payment/pay360/'.$key, $storeId);
            if (is_null($value) || false===$value) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    public function getUseSession() {
        if (!$this->hasData('use_session')) {
            $this->setUseSession(true);
        }
        return $this->getData('use_session');
    }

    public function getSessionData($key, $default=false) {
        if (!$this->hasData($key)) {
            $value = $this->_pay360Session->getData($key);
            if ($this->_pay360Session->hasData($key)) {
                $value = $this->_pay360Session->getData($key);
            }
			else {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    public function setSessionData($key, $value) {
        if ($this->getUseSession()) {
            $this->_pay360Session->setData($key, $value);
        }
        $this->setData($key, $value);
        return $this;
    }

    /**
     * the page where buyers will go if there are API error
     *
     * @return string
     */
    public function getApiErrorUrl() {
        return $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_error_url', 'pay360/gateway/error'), array('_secure' => true));
    }

    /**
     * Details of the callback made before the transaction is sent for authorisation.
     *
     * @return string
     */
    public function getPreAuthCallBackUrl() {
        return $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_preauth_callback_url', 'pay360/gateway/preauthcallback'), array('_secure' => true));
    }

    /**
     * Details of the callback made after the transaction is sent for authorisation.
     *
     * @return string
     */
    public function getPostAuthCallBackUrl() {
        return $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_postluth_callback_url', 'pay360/gateway/postauthcallback'), array('_secure' => true));
    }

    /**
     * Details of the notification sent after transaction completion.
     *
     * @return string
     */
    public function getTransactionNotificationUrl() {
        return $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_transaction_notification_url', 'pay360/gateway/transactionnotification'), array('_secure' => true));
    }

    /**
     * the page where buyers return to after they are done with the payment review on pay360
     *
     * @return string
     */
    public function getReturnUrl() {
        if ($this->_config->getValue('payment/pay360/payment_type') == \Pay360\Payments\Model\HppProvider::CODE) {
            $url = $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_return_url', 'pay360/gateway/gatewayreturn'), array('_secure' => true));
        }
        else {
            $url = $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_return_url', 'pay360/gateway/gatewayreturnhpf'), array('_secure' => true));
        }
        return $url;
    }

    /**
     * The page where buyers return to when they cancel the payment review on pay360
     *
     * @return string
     */
    public function getCancelUrl() {
        if ($this->_config->getValue('payment/pay360/payment_type') == \Pay360\Payments\Model\HppProvider::CODE) {
            $url = $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_cancel_url', 'pay360/gateway/cancel'), array('_secure' => true));
        }
        else {
            $url = $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_cancel_url', 'pay360/gateway/cancelhpf'), array('_secure' => true));
        }
        return $url;
    }

    /**
     * action for payment gateway, to redirect customer when transaction failed
     *
     * @return string
     */
    public function getFailedPaymentUrl() {
        return $this->_config->getUrlBuilder()->getUrl($this->getConfigData('api_cancel_url', 'pay360/gateway/failedpayment'), array('_secure' => true));
    }

    /**
     * transaction Id
     */
    public function getTransactionId() {
        return $this->getSessionData('transaction_id');
    }

    public function setTransactionId($data) {
        return $this->setSessionData('transaction_id', $data);
    }

    /**
     * Authorization Id
     */
    public function getAuthorizationId() {
        return $this->getSessionData('authorization_id');
    }

    public function setAuthorizationId($data) {
        return $this->setSessionData('authorization_id', $data);
    }

    /**
     * Payer Id
     */
    public function getPayerId() {
        return $this->getSessionData('payer_id');
    }

    public function setPayerId($data) {
        return $this->setSessionData('payer_id', $data);
    }

    /**
     * Complete type code (Complete, NotComplete)
     */
    public function getCompleteType() {
        return $this->getSessionData('complete_type');
    }

    public function setCompleteType($data) {
        return $this->setSessionData('complete_type', $data);
    }

    /**
     * Has to be one of the following values: Sale or Order or Authorization
     */
    public function getPaymentType() {
        return $this->getSessionData('payment_type');
    }

    public function setPaymentType($data) {
        return $this->setSessionData('payment_type', $data);
    }

    /**
     * Total value of the shopping cart
     *
     * Includes taxes, shipping costs, discount, etc.
     *
     * @return float
     */
    public function getAmount() {
        return $this->getSessionData('amount');
    }

    public function setAmount($data) {
	    $data = sprintf('%.2f', $data);
        return $this->setSessionData('amount', $data);
    }

    /**
     * currency Code
     */
    public function getCurrencyCode() {
        return $this->getSessionData('currency_code', $this->_storeManager->getStore()->getCurrentCurrency()->getCode());
    }

    public function setCurrencyCode($data) {
        return $this->setSessionData('currency_code', $data);
    }

    /**
     * Refund type ('Full', 'Partial')
     */
    public function getRefundType() {
        return $this->getSessionData('refund_type');
    }

    public function setRefundType($data) {
        return $this->setSessionData('refund_type', $data);
    }

    /**
     * Error
     */
    public function getError() {
        return $this->getSessionData('error');
    }

    public function setError($data) {
        return $this->setSessionData('error', $data);
    }

    public function unsError() {
        return $this->setSessionData('error', null);
    }

    public function getCcTypeName($ccType) {
        $types = array(
            'AE'=> __('Amex'),
            'VI'=> __('Visa'),
            'MC'=> __('MasterCard'),
            'DI'=> __('Discover')
        );

        return isset($types[$ccType]) ? $types[$ccType] : false;
    }

    /**
     * setResourceEndpoint need to done from child class
     */
    public function getApiEndpoint() {
        if (!$this->getData('api_endpoint')) {
            $url = $this->getComUrl() . $this->getResourceEndpoint();
            $this->setData('api_endpoint', $url);
        }
        return $this->getData('api_endpoint');
    }

    /**
     * perform request to Pay360 payment gateway
     */
    protected function request($nvpArr) {
        $this->_curlClient->setTimeout(Config::DEFAULT_TIMEOUT);
        $username = $this->_config->getValue('payment/pay360/api_user');
        $password = $this->_config->getValue('payment/pay360/api_password');
        $this->_curlClient->setCredentials($username, $password);
        $this->_curlClient->addHeader('Content-Type', 'application/json');

        try {
            $this->_curlClient->post($this->getApiEndpoint(), $this->_jsonEncoder->encode($nvpArr));
            $response = $this->_curlClient->getBody();
            $this->_logger->write(['nvpArr'=> $nvpArr, 'response' => $response, 'endpoint' => $this->getApiEndpoint()]);

            if ($response && $data = $this->_jsonDecoder->decode($response)) {
                return $data;
            }
        }
        catch (\Exception $e) {
            $this->setError(array( 'type'=>'CURL', 'message'=>$e->getMessage()));
            $this->_logger->write(array( 'type'=>'CURL', 'message'=>$e->getMessage()));
            $this->setRedirectUrl($this->getApiErrorUrl());
        }

        $this->setRedirectUrl($this->getApiErrorUrl());
        return false;
    }
}
