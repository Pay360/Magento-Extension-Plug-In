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

use Magento\Store\Model\ScopeInterface;

class Standard extends \Magento\Payment\Model\Method\AbstractMethod
{

    const PAYMENT_TYPE_AUTH = 'AUTHONLY';
    const PAYMENT_TYPE_AUCAP = 'AUTHNCAPTURE';

    const STATE_PENDING_PAY360_PAYMENT = 'pending_pay360_payment';

    const DATA_CHARSET = 'utf-8';
    const CODE = 'pay360';

    /**
     * @var string
     */
    protected $_infoBlockType = \Pay360\Payments\Block\Payment\Info::class;

    /**
     * @var string
     */
    protected $_code = self::CODE;

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
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Pay360\Payments\Model\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Pay360\Payments\Model\ProfileFactory
     */
    protected $_profileFactory;

    /**
     * @var \Pay360\Payments\Model\Api\Nvp
     */
    protected $_nvp;

    /**
     * @var \Pay360\Payments\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $_orderSender;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

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
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Pay360\Payments\Model\TransactionFactory $transactionFactory,
     * @param \Pay360\Payments\Model\ProfileFactory $profileFactory,
     * @param \Pay360\Payments\Model\Api\Nvp $nvp,
     * @param \Pay360\Payments\Model\Config $config,
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender
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
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Pay360\Payments\Model\TransactionFactory $transactionFactory,
        \Pay360\Payments\Model\ProfileFactory $profileFactory,
        \Pay360\Payments\Model\Api\Nvp $nvp,
        \Pay360\Payments\Model\Config $config,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
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
        $this->_orderFactory = $orderFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_profileFactory = $profileFactory;
        $this->_nvp = $nvp;
        $this->_config = $config;
        $this->_orderSender = $orderSender;
        $this->_transaction = $transaction;
        $this->_invoiceService = $invoiceService;
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return parent::canUseCheckout();
    }

    /**
     * Custom getter for payment configuration
     *
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getConfigData($key, $default = false, $storeId = null)
    {
        if (!$this->hasData($key)) {
            if (null === $storeId) {
                $storeId = $this->_storeManager->getStore()->getId();
            }
            $value = $this->_scopeConfig->getValue('payment/pay360/'.$key, ScopeInterface::SCOPE_STORE, $storeId);
            if (is_null($value) || false===$value) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
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

        $payment->setAdditionalInformation($this->_isOrderPaymentActionKey, true);

        if ($payment->getIsFraudDetected()) {
            return $this;
        }

        $order = $payment->getOrder();
        $orderTransactionId = $payment->getTransactionId();

        $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
        $status = true;

        $formattedPrice = $order->getBaseCurrency()->formatTxt($amount);
        if ($payment->getIsTransactionPending()) {
            $message = __("The ordering amount of %1 is pending approval on the payment gateway.", $formattedPrice);
            $state = \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW;
        } else {
            $message = __("Ordered amount of %1", $formattedPrice);
        }

        $transaction = $this->transactionBuilder->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($payment->getTransactionId())
            ->build(Transaction::TYPE_ORDER);
        $payment->addTransactionCommentsToOrder($transaction, $message);

        if ($payment->getIsTransactionPending()) {
            $message = __(
                "We'll authorize the amount of %1 as soon as the payment gateway approves it.",
                $formattedPrice
            );
            $state = \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW;
            if ($payment->getIsFraudDetected()) {
                $status = \Magento\Sales\Model\Order::STATUS_FRAUD;
            }
        } else {
            $message = __("The authorized amount is %1.", $formattedPrice);
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
        //$this->_nvp->callDo($payment);
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
        $order = $payment->getOrder();
        $transaction = $this->_transactionFactory->create()->load($order->getId(), 'merchant_ref');
        // apply capture logic here. only take place if order was place with Authorize only
        $response = $this->_nvp->callDoCapture($transaction, $order);

        $transaction = $response['transaction'];
        $outcome = empty($response['outcome']) ? array() : $response['outcome'];
        $payment->setTransactionId($transaction['transactionId']);
        if ($transaction['status'] == \Pay360\Payments\Model\Config::PAYMENT_STATUS_SUCCESS) {
            return $this;
        } else {
            if (!empty($outcome) && !empty($outcome['reasonMessage'])) {
                throw new \Exception($outcome['reasonMessage']);
            } else {
                throw new \Exception(__("Payment capturing error."));
            }
        }
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
        $order = $payment->getOrder();

        // apply pay360 refund logic here.
        $response = $this->_nvp->callRefundTransaction($order, $amount);

        $transaction = $response['transaction'];
        $outcome = empty($response['outcome']) ? array() : $response['outcome'];
        $payment->setTransactionId($transaction['transactionId']);
        if ($transaction['type'] == \Pay360\Payments\Model\Config::PAYMENT_TYPE_REFUND && $transaction['status'] == \Pay360\Payments\Model\Config::PAYMENT_STATUS_SUCCESS) {
            return $this;
        } else {
            if (!empty($outcome) && !empty($outcome['reasonMessage'])) {
                throw new \Exception($outcome['reasonMessage']);
            } else {
                throw new \Exception(__("Payment refunding error."));
            }
        }
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
        $this->_config->setStoreId($payment->getOrder()->getStore()->getId());

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
     * Available resouces
     * $body_json['history']
     * $body_json['processing']
     * $body_json['paymentMethod']
     * $body_json['customer']
     *
     * @param array $body_json
     */
    public function transactionNotificationCallback($body_json)
    {
        $transaction = $body_json['transaction'];

        $order = $this->_orderFactory->create()->load($transaction['merchantRef']);
        if ($order->getId() && $order->getGrandTotal() == $transaction['amount'] && empty($transaction['deferred'])) {
            $this->afterCapture($order, $transaction);
        } else {
            $this->_pay360Logger->write("Amounts not equal or Payment deferred");
        }

        $this->_pay360Logger->write(['body_json' => $body_json]);
    }

    /**
     * custom logic to check before authorization. we can verify and control payment flow here.
     * @param $body_json -> paymentMethod -> registered, card, billingAddress, paymentClass
     */
    public function preAuthCallback($body_json)
    {
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

        $this->_pay360Logger->write($response);
        return $response;
    }

    /**
     * custom logic to check after authorization
     */
    public function postAuthCallback($body_json)
    {
        // Default response is cancel
        $response = array(
            'callbackResponse' => array(
                'postAuthCallbackResponse' => array(
                    'action' => \Pay360\Payments\Model\Config::RESPOND_CANCEL, // available actions proceed, cancel
                    'return' => array(
                        'url' => $this->_nvp->getFailedPaymentUrl()
                    )
                )
            )
        );
        try {
            $transaction = $body_json['transaction'];
            $model = $this->_transactionFactory->create()->load($transaction['transactionId'], 'transaction_id');
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
            if ($transaction['status'] == \Pay360\Payments\Model\Config::PAYMENT_STATUS_SUCCESS) {
                $this->saveProfile($body_json);

                // set order state with post auth call back. will not append to order status history
                $order = $this->_orderFactory->create()->load($transaction['merchantRef']);
                $newOrderStatus = $this->getConfigData('order_status', \Magento\Sales\Model\Order::STATE_NEW, $order->getStoreId());
                if (empty($newOrderStatus)) {
                    $newOrderStatus = $order->getStatus();
                }

                $order->addStatusHistoryComment(__("Order #%1 updated.", $order->getId()));
                $order->setState($newOrderStatus)->setStatus($newOrderStatus)->save();
                $response['callbackResponse']['postAuthCallbackResponse']['action'] = \Pay360\Payments\Model\Config::RESPOND_PROCEED;
                unset($response['callbackResponse']['postAuthCallbackResponse']['return']);
            }
        } catch (Exception $e) {
            $this->_pay360Logger->write($e->getMessage());
        }

        $this->_pay360Logger->write(['body_json' => $body_json]);
        return $response;
    }

    /**
     * create or update payment profile. dont save duplicate card
     *
     * @param array $body_json
     */
    protected function saveProfile($body_json)
    {
        $customer_id = null;
        if (!empty($body_json['customer']) && !empty($body_json['customer']['merchantRef'])) {
            $customer_id = $body_json['customer']['merchantRef'];
        }
        if (empty($customer_id)) {
            // dont save profile if not a loggedin customer
            return false;
        }
        try {
            $card = $body_json['paymentMethod']['card'];
            $registered = $body_json['paymentMethod']['registered'];
            $profile_id = $body_json['customer']['id'];

            $profile = $this->_profileFactory->create()->initProfileByMaskedPan($card['maskedPan'], $customer_id);
            $profile->setCardToken($card['cardToken'])
                ->setProfileId($profile_id)
                ->setRegistered((boolean)$registered)
                ->setNew($card['new'])
                ->setCardType($card['cardType'])
                ->setCardUsageType($card['cardUsageType'])
                ->setCardScheme($card['cardScheme'])
                ->setCategory($card['cardCategory'])
                ->setExpiryDate($card['expiryDate'])
                ->setIssuer($card['issuer'])
                ->setIssuerCountry($card['issuerCountry'])
                ->setCardHolderName($card['cardHolderName'])
                ->setCardNickName($card['cardNickname'])
                ->save();
        } catch (Exception $e) {
            $this->_pay360Logger->write($e->getMessage());
        }
    }

    /**
     * process order, invoice after payment capture is done
     */
    public function afterCapture($order, $transaction)
    {
        /* set the quote as inactive after back from pay360 */
        $this->_checkoutSession->getQuote()->setIsActive(false)->save();

        /* send confirmation email to customer */
        $this->_orderSender->send($order);

        /* verify status. make invoice and set order state to processing. add comments to Order */
        if ($transaction['status'] == \Pay360\Payments\Model\Config::PAYMENT_STATUS_SUCCESS) {
            if (!$order->canInvoice()) {
                $order->addStatusHistoryComment(__("Error in creating an invoice"));
            } else {
                try {
                    $order->getPayment()->setTransactionId($transaction['transactionId']);
                    $invoice = $this->_invoiceService->prepareInvoice($order);
                    ;
                    //set transaction id for invoice
                    $invoice->setTransactionId($transaction['transactionId']);
                    //set invoice state to paid
                    $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                    $invoice->register();
                    $this->_transaction->addObject($invoice)->addObject($order)->save();
                    /* set order status */
                    $order->addStatusHistoryComment(__("Captured amount of %1 online. Transaction ID: '%2'.", strip_tags($order->getBaseCurrency()->formatTxt($transaction['amount'])), strval($transaction['transactionId'])));

                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                } catch (Exception $e) {
                    $this->_pay360Logger->write($e->getMessage());
                }
            }
        } else {
            $order->addStatusHistoryComment(__("Received IPN verification but was not successful"));
        }
        $ipnCustomerNotified = true;
        if (!$order->getPay360IpnCustomerNotified()) {
            $ipnCustomerNotified = false;
            $order->setPay360IpnCustomerNotified(1);
        }

        $order->save();
        if (!$ipnCustomerNotified) {
            $this->_orderSender->send($order);
        }
    }
}
