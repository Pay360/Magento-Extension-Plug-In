<?php
/**
 * Magento 2 Payment module from Pay360
 * Copyright (C) 2017  Pay360 by Capita
 *
 * This file is part of Pay360/Payments.
 *
 * Pay360/Payments is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Pay360\Payments\Model;

class Standard extends \Magento\Payment\Model\Method\AbstractMethod
{

    const PAYMENT_TYPE_AUTH = 'AUTHONLY';
    const PAYMENT_TYPE_AUCAP = 'AUTHNCAPTURE';

    const STATE_PENDING_PAY360_PAYMENT = 'pending_pay360_payment';

    const DATA_CHARSET = 'utf-8';
    const CODE = 'pay360_standard';

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * @var string
     */
    protected $_formBlockType = \Pay360\Payments\Block\Express\Form::class;

    /**
     * @var string
     */
    protected $_infoBlockType = \Pay360\Payments\Block\Payment\Info::class;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isGateway = false;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canOrder = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canFetchTransactionInfo = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canReviewPayment = true;

    /**
     * Payment additional information key for payment action
     *
     * @var string
     */
    protected $_isOrderPaymentActionKey = 'is_order_action';

    /**
     * Payment additional information key for number of used authorizations
     *
     * @var string
     */
    protected $_authorizationCountKey = 'authorization_count';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Exception\LocalizedExceptionFactory
     */
    protected $_exception;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var Transaction\BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * @var \Pay360\Payments\Helper\Logger
     */
    protected $_pay360Logger;

    /**
     * NOTE: dont change last 3 params, or error will be thrown
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Exception\LocalizedExceptionFactory $exception
     * @param \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
     * @param Transaction\BuilderInterface $transactionBuilder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Pay360\Payments\Helper\Logger $pay360Logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Exception\LocalizedExceptionFactory $exception,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Pay360\Payments\Helper\Logger $pay360Logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_checkoutSession = $checkoutSession;
        $this->_exception = $exception;
        $this->transactionRepository = $transactionRepository;
        $this->transactionBuilder = $transactionBuilder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;
        $this->_pay360Logger = $pay360Logger;
    }

    /**
     * Store setter
     * Also updates store ID in config object
     *
     * @param \Magento\Store\Model\Store|int $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        if (null === $store) {
            $store = $this->_storeManager->getStore()->getId();
        }
        $this->_pro->getConfig()->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        if ($this->_scopeConfig->isSetFlag(
            'payment/hosted_pro/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) && !$this->_scopeConfig->isSetFlag(
            'payment/hosted_pro/display_ec',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return false;
        }
        return parent::canUseCheckout();
    }

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->_pro->getConfig()->isCurrencyCodeSupported($currencyCode);
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see \Magento\Sales\Model\Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->_pro->getConfig()->getPaymentAction();
    }

    /**
     * Check whether payment method can be used
     * @param \Magento\Quote\Api\Data\CartInterface|Quote|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return parent::isAvailable($quote) && $this->_pro->getConfig()->isMethodAvailable();
    }

    /**
     * Custom getter for payment configuration
     *
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getConfigData($field, $storeId = null)
    {
        if ('order_place_redirect_url' === $field) {
            return $this->getOrderPlaceRedirectUrl();
        }
        return $this->_pro->getConfig()->getValue($field);
    }

    /**
     * Order payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $paypalTransactionData = $this->_checkoutSession->getPaypalTransactionData();
        if (!is_array($paypalTransactionData)) {
            $this->_placeOrder($payment, $amount);
        } else {
            $this->_importToPayment($this->_pro->getApi()->setData($paypalTransactionData), $payment);
        }

        $payment->setAdditionalInformation($this->_isOrderPaymentActionKey, true);

        if ($payment->getIsFraudDetected()) {
            return $this;
        }

        $order = $payment->getOrder();
        $orderTransactionId = $payment->getTransactionId();

        $api = $this->_callDoAuthorize($amount, $payment, $orderTransactionId);

        $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
        $status = true;

        $formattedPrice = $order->getBaseCurrency()->formatTxt($amount);
        if ($payment->getIsTransactionPending()) {
            $message = __('The ordering amount of %1 is pending approval on the payment gateway.', $formattedPrice);
            $state = \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW;
        } else {
            $message = __('Ordered amount of %1', $formattedPrice);
        }

        $transaction = $this->transactionBuilder->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($payment->getTransactionId())
            ->build(Transaction::TYPE_ORDER);
        $payment->addTransactionCommentsToOrder($transaction, $message);

        $this->_pro->importPaymentInfo($api, $payment);

        if ($payment->getIsTransactionPending()) {
            $message = __(
                'We\'ll authorize the amount of %1 as soon as the payment gateway approves it.',
                $formattedPrice
            );
            $state = \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW;
            if ($payment->getIsFraudDetected()) {
                $status = \Magento\Sales\Model\Order::STATUS_FRAUD;
            }
        } else {
            $message = __('The authorized amount is %1.', $formattedPrice);
        }

        $payment->resetTransactionAdditionalInfo();

        $payment->setTransactionId($api->getTransactionId());
        $payment->setParentTransactionId($orderTransactionId);

        $transaction = $this->transactionBuilder->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($payment->getTransactionId())
            ->build(Transaction::TYPE_AUTH);
        $payment->addTransactionCommentsToOrder($transaction, $message);

        $order->setState($state)
            ->setStatus($status);

        $payment->setSkipOrderProcessing(true);
        return $this;
    }

    /**
     * Authorize payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Void payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        //Switching to order transaction if needed
        if ($payment->getAdditionalInformation(
            $this->_isOrderPaymentActionKey
        ) && !$payment->getVoidOnlyAuthorization()
        ) {
            $orderTransaction = $this->getOrderTransaction($payment);
            if ($orderTransaction) {
                $payment->setParentTransactionId($orderTransaction->getTxnId());
                $payment->setTransactionId($orderTransaction->getTxnId() . '-void');
            }
        }
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @param float $amount
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $authorizationTransaction = $payment->getAuthorizationTransaction();
        $authorizationPeriod = abs(intval($this->getConfigData('authorization_honor_period')));
        $maxAuthorizationNumber = abs(intval($this->getConfigData('child_authorization_number')));
        $order = $payment->getOrder();
        $isAuthorizationCreated = false;

        if ($payment->getAdditionalInformation($this->_isOrderPaymentActionKey)) {
            $voided = false;
            if (!$authorizationTransaction->getIsClosed() && $this->_isTransactionExpired(
                $authorizationTransaction,
                $authorizationPeriod
            )
            ) {
                //Save payment state and configure payment object for voiding
                $isCaptureFinal = $payment->getShouldCloseParentTransaction();
                $payment->setShouldCloseParentTransaction(false);
                $payment->setParentTransactionId($authorizationTransaction->getTxnId());
                $payment->unsTransactionId();
                $payment->setVoidOnlyAuthorization(true);
                $payment->void(new \Magento\Framework\DataObject());

                //Revert payment state after voiding
                $payment->unsAuthorizationTransaction();
                $payment->unsTransactionId();
                $payment->setShouldCloseParentTransaction($isCaptureFinal);
                $voided = true;
            }

            if ($authorizationTransaction->getIsClosed() || $voided) {
                if ($payment->getAdditionalInformation($this->_authorizationCountKey) > $maxAuthorizationNumber - 1) {
                    $this->_exception->create(
                        ['phrase' => __('The maximum number of child authorizations is reached.')]
                    );
                }
                $api = $this->_callDoAuthorize($amount, $payment, $authorizationTransaction->getParentTxnId());

                //Adding authorization transaction
                $this->_pro->importPaymentInfo($api, $payment);
                $payment->setTransactionId($api->getTransactionId());
                $payment->setParentTransactionId($authorizationTransaction->getParentTxnId());
                $payment->setIsTransactionClosed(false);

                $formatedPrice = $order->getBaseCurrency()->formatTxt($amount);

                if ($payment->getIsTransactionPending()) {
                    $message = __(
                        'We\'ll authorize the amount of %1 as soon as the payment gateway approves it.',
                        $formatedPrice
                    );
                } else {
                    $message = __('The authorized amount is %1.', $formatedPrice);
                }

                $transaction = $this->transactionBuilder->setPayment($payment)
                    ->setOrder($order)
                    ->setTransactionId($payment->getTransactionId())
                    ->setFailSafe(true)
                    ->build(Transaction::TYPE_AUTH);
                $payment->addTransactionCommentsToOrder($transaction, $message);

                $payment->setParentTransactionId($api->getTransactionId());
                $isAuthorizationCreated = true;
            }
            //close order transaction if needed
            if ($payment->getShouldCloseParentTransaction()) {
                $orderTransaction = $this->getOrderTransaction($payment);

                if ($orderTransaction) {
                    $orderTransaction->setIsClosed(true);
                    $order->addRelatedObject($orderTransaction);
                }
            }
        }

        if (false === $this->_pro->capture($payment, $amount)) {
            $this->_placeOrder($payment, $amount);
        }

        if ($isAuthorizationCreated && isset($transaction)) {
            $transaction->setIsClosed(true);
        }

        return $this;
    }

    /**
     * Refund capture
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @return $this
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        $this->void($payment);

        return $this;
    }

    /**
     * Whether payment can be reviewed
     * @return bool
     */
    public function canReviewPayment()
    {
        return parent::canReviewPayment() && $this->_pro->canReviewPayment($this->getInfoInstance());
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see \Magento\Checkout\Controller\Onepage::savePaymentAction()
     * @see Quote\Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('paypal/express/start');
    }

    /**
     * Fetch transaction details info
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(\Magento\Payment\Model\InfoInterface $payment, $transactionId)
    {
        return $this->_pro->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * @return Api\Nvp
     */
    public function getApi()
    {
        return $this->_pro->getApi();
    }

    /**
     * Assign data to info model instance
     *
     * @param array|\Magento\Framework\DataObject $data
     * @return \Magento\Payment\Model\Info
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return $this;
        }

        foreach ($additionalData as $key => $value) {
            $this->getInfoInstance()->setAdditionalInformation($key, $value);
        }
        return $this;
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Payment $payment
     * @param float $amount
     * @return $this
     */
    protected function _placeOrder(Payment $payment, $amount)
    {
        $order = $payment->getOrder();

        // prepare api call
        $token = $payment->getAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_TOKEN);

        $cart = $this->_cartFactory->create(['salesModel' => $order]);

        $api = $this->getApi()->setToken(
            $token
        )->setPayerId(
            $payment->getAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_PAYER_ID)
        )->setAmount(
            $amount
        )->setPaymentAction(
            $this->_pro->getConfig()->getValue('paymentAction')
        )->setNotifyUrl(
            $this->_urlBuilder->getUrl('paypal/ipn/')
        )->setInvNum(
            $order->getIncrementId()
        )->setCurrencyCode(
            $order->getBaseCurrencyCode()
        )->setPaypalCart(
            $cart
        )->setIsLineItemsEnabled(
            $this->_pro->getConfig()->getValue('lineItemsEnabled')
        );
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
            $api->setBillingAddress($order->getBillingAddress());
        }

        // call api and get details from it
        $api->callDoExpressCheckoutPayment();

        $this->_importToPayment($api, $payment);
        return $this;
    }

    /**
     * Import payment info to payment
     *
     * @param Nvp $api
     * @param Payment $payment
     * @return void
     */
    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId(
            $api->getTransactionId()
        )->setIsTransactionClosed(
            0
        )->setAdditionalInformation(
            ExpressCheckout::PAYMENT_INFO_TRANSPORT_REDIRECT,
            $api->getRedirectRequired()
        );

        if ($api->getBillingAgreementId()) {
            $payment->setBillingAgreementData(
                [
                    'billing_agreement_id' => $api->getBillingAgreementId(),
                    'method_code' => \Pay360\Payments\Model\Config::METHOD_BILLING_AGREEMENT,
                ]
            );
        }

        $this->_pro->importPaymentInfo($api, $payment);
    }

    /**
     * Check void availability
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @internal param \Magento\Framework\DataObject $payment
     */
    public function canVoid()
    {
        $info = $this->getInfoInstance();
        if ($info->getAdditionalInformation($this->_isOrderPaymentActionKey)) {
            $orderTransaction = $this->getOrderTransaction($info);
            if ($orderTransaction) {
                $info->setParentTransactionId($orderTransaction->getTxnId());
            }
        }

        return $this->_canVoid;
    }

    /**
     * Check capture availability
     *
     * @return bool
     */
    public function canCapture()
    {
        $payment = $this->getInfoInstance();
        $this->_pro->getConfig()->setStoreId($payment->getOrder()->getStore()->getId());

        if ($payment->getAdditionalInformation($this->_isOrderPaymentActionKey)) {
            $orderTransaction = $this->getOrderTransaction($payment);
            if ($orderTransaction->getIsClosed()) {
                return false;
            }

            $orderValidPeriod = abs(intval($this->getConfigData('order_valid_period')));

            $dateCompass = new \DateTime($orderTransaction->getCreatedAt());
            $dateCompass->modify('+' . $orderValidPeriod . ' days');
            $currentDate = new \DateTime();

            if ($currentDate > $dateCompass || $orderValidPeriod == 0) {
                return false;
            }
        }

        return $this->_canCapture;
    }

    /**
     * Call DoAuthorize
     *
     * @param int $amount
     * @param \Magento\Framework\DataObject $payment
     * @param string $parentTransactionId
     * @return \Pay360\Payments\Model\Api\AbstractApi
     */
    protected function _callDoAuthorize($amount, $payment, $parentTransactionId)
    {
        $apiData = $this->_pro->getApi()->getData();
        foreach ($apiData as $k => $v) {
            if (is_object($v)) {
                unset($apiData[$k]);
            }
        }
        $this->_checkoutSession->setPaypalTransactionData($apiData);
        $this->_pro->resetApi();
        $api = $this->setAmount($amount)
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setTransactionId($parentTransactionId)
            ->callDoAuthorization();

        $payment->setAdditionalInformation(
            $this->_authorizationCountKey,
            $payment->getAdditionalInformation($this->_authorizationCountKey) + 1
        );

        return $api;
    }

    /**
     * Check transaction for expiration in PST
     *
     * @param Transaction $transaction
     * @param int $period
     * @return bool
     */
    protected function _isTransactionExpired(Transaction $transaction, $period)
    {
        $period = intval($period);
        if (0 == $period) {
            return true;
        }

        $transactionClosingDate = new \DateTime($transaction->getCreatedAt(), new \DateTimeZone('GMT'));
        $transactionClosingDate->setTimezone(new \DateTimeZone('US/Pacific'));
        /**
         * 11:49:00 PayPal transactions closing time
         */
        $transactionClosingDate->setTime(11, 49, 00);
        $transactionClosingDate->modify('+' . $period . ' days');

        $currentTime = new \DateTime(null, new \DateTimeZone('US/Pacific'));

        if ($currentTime > $transactionClosingDate) {
            return true;
        }

        return false;
    }

    /**
     * Is active
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return parent::isActive($storeId) || (bool)(int)$this->_scopeConfig->getValue('payment/pay360/active', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get transaction with type order
     *
     * @param OrderPaymentInterface $payment
     * @return false|\Magento\Sales\Api\Data\TransactionInterface
     */
    protected function getOrderTransaction($payment)
    {
        return $this->transactionRepository->getByTransactionType(
            Transaction::TYPE_ORDER,
            $payment->getId(),
            $payment->getOrder()->getId()
        );
    }

    /**
     * custom logic to check before authorization. we can verify and control payment flow here. 
     * $body_json -> paymentMethod -> registered, card, billingAddress, paymentClass
     */
    public function preAuthCallback($body_json) {
        $response = array(
            'callbackResponse' => array(
                'preAuthCallbackResponse' => array(
                    'action' => \Pay360\Payments\Model\Config::RESPOND_PROCEED,
                    'redirect' => array(
                        'url' => $this->_urlBuilder->getUrl('pay360/gateway/paymentsuspend', array('sessionId' => $body_json['sessionId'])),
                        'frame' => 'CONTAINER' // Possible Values: CONTAINER, TOP
                    )
                    // 'return' => array() // not necessary since we allready have call back url
                )
            )
        );

        $this->_pay360Logger->write(['body_json' => $body_json]);
        return $this->_jsonEncoder->encode($response);
    }

    /**
     * custom logic to check after authorization
     */
    public function postAuthCallback($body_json) {
        // Default response is cancel
        $response = array(
            'callbackResponse' => array(
                'postAuthCallbackResponse' => array(
                    'action' => Pay360_Payments_Model_Api_Abstract::RESPOND_CANCEL, // available actions proceed, cancel
                    'return' => array(
                        'url' => $this->_nvp->getFailedPaymentUrl()
                    )
                )
            )
        );
        try {
            $transaction = $body_json['transaction'];
            $model = $this->_pay360transaction->load($transaction['transactionId'], 'transaction_id');
            $model->setTransactionId($transaction['transactionId'])
                ->setDeferred(empty($transaction['deferred']) ? false : true)
                ->setMerchantRef($transaction['merchantRef'])
                ->setMerchantDescription($transaction['merchantDescription'])
                ->setType($transaction['type'])
                ->setAmount($transaction['amount'])
                ->setStatus($transaction['status'])
                ->setCurrency($transaction['currency'])
                ->setTransactionTime($transaction['transactionTime'])
                ->setReceivedTime($transaction['receivedTime'])
                ->setChannel($transaction['channel'])
                ->save();

            // Create/Upate profile if transaction success
            if ($transaction['status'] == Pay360_Payments_Model_Api_Abstract::PAYMENT_STATUS_SUCCESS) {
                $this->saveProfile($body_json);

                // set order state with post auth call back. will not append to order status history
                $order = Mage::getModel('sales/order')->load($transaction['merchantRef']);
                $newOrderStatus = $this->getConfigData('order_status', Mage_Sales_Model_Order::STATE_NEW, $order->getStoreId());
                if (empty($newOrderStatus)) {
                    $newOrderStatus = $order->getStatus();
                }

                $order->setState(
                    $newOrderStatus, false,
                    Mage::helper('pay360')->__('Order #%s updated.', $order->getIncrementId()),
                    $notified = true
                )->save();
                $response['callbackResponse']['postAuthCallbackResponse']['action'] = Pay360_Payments_Model_Api_Abstract::RESPOND_PROCEED;
                unset($response['callbackResponse']['postAuthCallbackResponse']['return']);
            }
        }
        catch (Exception $e) {
            Mage::helper('pay360/logger')->write($e->getMessage());
        }

        Mage::helper('pay360/logger')->write(['body_json' => $body_json]);
        return Zend_Json::encode($response);
    }
}
