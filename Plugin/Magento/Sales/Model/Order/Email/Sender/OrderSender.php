<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Pay360\Payments\Plugin\Magento\Sales\Model\Order\Email\Sender;

use Pay360\Payments\Helper\Data as Pay360Helper;
use Pay360\Payments\Helper\Logger as Pay360Logger;

class OrderSender
{
    CONST MODULE_NAME='pay360';
    CONST CONTROLLER_NAME='gateway';
    CONST ACTION_NAME='postauthcallback';
    /**
     * @var Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var Pay360Helper
     */
    protected $helper;

    /**
     * @var Pay360Logger
     */
    protected $logger;

    /**
     * @param Pay360Helper $helper
     * @param Pay360Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        Pay360Helper $helper,
        Pay360Logger $logger
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * only send notification email once by webhook callback
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function aroundSend(
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Order $order,
        $forceSyncMode = false
    ) {
        $this->logger->write($order->getPayment()->getMethod());
        if ($order->getPayment() && $order->getPayment()->getMethod() == \Pay360\Payments\Model\Standard::CODE) {
            $this->logger->write("pay360 order");
            // handle pay360 orders
            $result = false;
            if ($this->isPay360CallbackRequest()
                && !$order->getEmailSent()) {
                $this->logger->write("pay360 send email once");
                $result = $proceed($order, $forceSyncMode);
            }
        }
        else {
            $this->logger->write("default M2 email");
            $result = $proceed($order, $forceSyncMode);
        }
        $this->logger->write("aroundSend end");

        return $result;
    }

    /**
     * check if request url is pay360 callback or not
     *
     * @return boolean
     */
    public function isPay360CallbackRequest()
    {
        return $this->helper->isNotificationSurpressionOn()
            && $this->request->getModuleName() == self::MODULE_NAME
            && $this->request->getControllerName() == self::CONTROLLER_NAME
            && $this->request->getActionName() == self::ACTION_NAME;
    }
}
