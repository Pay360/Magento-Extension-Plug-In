<?php

namespace Pay360\Payments\Helper;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Pay360\Payments\Model\Standard as PaymentModel;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PAY360_DEBUG = 'payment/pay360/debug';
    const PAY360_PAYMENT_TYPE = 'payment/pay360/payment_type';
    const PAY360_ORDER_STATUS = 'payment/pay360/order_status';
    const PAY360_ORDER_STATUS_AUTHONLY = 'payment/pay360/order_status_auth';

    protected $_customerSession;
    protected $_checkoutSession;
    protected $_orderConfig;
    protected $_orderFactory;
    protected $_orderManagement;
    protected $cart;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param CustomerCart $cart
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        CustomerCart $cart,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceHelper
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_orderFactory = $orderFactory;
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->_orderManagement = $orderManagement;
        $this->_priceHelper = $priceHelper;
    }

    public function getOrderId()
    {
        return $this->_checkoutSession->getLastRealOrderId();
    }

    /**
     * Check order view availability
     *
     * @param \Magento\Sales\Model\Order $order
     */
    protected function _canViewOrder($order)
    {
        $customerId = $this->_customerSession->getCustomerId();
        $availableStates = $this->_orderConfig->getVisibleOnFrontStatuses();
        if ($order->getId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
            ) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param Integer $increment_id
     */
    protected function _loadValidOrder($increment_id = null)
    {
        if (!$increment_id) {
            return false;
        }

        $order = $this->_orderFactory->create()->loadByIncrementId($increment_id);
        if ($this->_canViewOrder($order)) {
            return $order;
        }

        return false;
    }

    /**
     * retrieve order state/status for new order per configuration. values depends on Payment Type
     *
     * @return string
     */
    public function getOrderState()
    {
        if ($this->scopeConfig->getValue(self::PAY360_PAYMENT_TYPE, ScopeInterface::SCOPE_STORE) == PaymentModel::AUTHONLY) {
            return $this->scopeConfig->getValue(self::PAY360_ORDER_STATUS_AUTHONLY, ScopeInterface::SCOPE_STORE, $order->getStoreId());
        }

        return $this->scopeConfig->getValue(self::PAY360_ORDER_STATUS, ScopeInterface::SCOPE_STORE, $order->getStoreId());
    }

    /**
     * rebuild cart content if payment failed
     *
     * @param Integer $last_order_id
     */
    public function reinitCart($last_order_id)
    {
        $order = $this->_loadValidOrder($last_order_id);
        if (!$order) {
            return;
        }
        // cancel before reinit - NOTE: status update required before cancelling
        $status = $this->scopeConfig->getValue(self::PAY360_ORDER_STATUS, ScopeInterface::SCOPE_STORE, $order->getStoreId());
        $order->addStatusHistoryComment(__("Order #%1 cancelled.", $order->getIncrementId()));
        $order->setState($status)->setStatus($status)->save();

        $this->_orderManagement->cancel($order->getId());

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $this->cart->addOrderItem($item);
            } catch (\Exception $e) {
                $this->messageManager->addNoticeMessage(__("Cannot add item '{$item->getName()}' to the shopping cart."));
                return false;
            }
        }

        $this->cart->save();
    }

    /**
     * get template file for review section of onepage page
     */
    public function reviewhpf()
    {
        if ($this->_checkoutSession->getQuote()->getPayment()->getMethodInstance()->getCode() == PaymentModel::CODE
            && $this->scopeConfig->getValue(self::PAY360_PAYMENT_TYPE, ScopeInterface::SCOPE_STORE)  == \Pay360\Payments\Model\Source\Paymenttype::TYPE_HPF) {
            return 'pay360/review/info.phtml';
        } else {
            return 'checkout/onepage/review/info.phtml';
        }
    }

    /**
     * check payment debug enabled
     *
     * @return bool
     */
    public function isDebugOn()
    {
        return (bool) $this->scopeConfig->getValue(self::PAY360_DEBUG, ScopeInterface::SCOPE_STORE);
    }

    /**
     * format currency with symbol and no container
     *
     * @param string | number
     * @return string
     */
    public function formatCurrency($amount)
    {
        return $this->_priceHelper->convertAndFormat($amount, false);
    }
}
