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
        $result = false;

        if ($this->helper->isNotificationSurpressionOn()) {
            $this->logger->write("Pay360 Mailer Logic");
            // only send email when there is payment details
            if ($order->getPayment()
                && $order->getPayment()->getMethod() == \Pay360\Payments\Model\Standard::CODE
                && $order->getPayment()->getCcType()) {

                // email sender defined here Pay360\Payments\Model\Standard::afterCapture L736,L776
                // afterCapture triggered by either capture() or transactionNotificationCallback()
                if (!$order->getEmailSent()) {
                    $this->logger->write("Sending Order Notification Email..");
                    $result = $proceed($order, $forceSyncMode);
                }
            }
        }
        else {
            $this->logger->write("Default Magento Mailer Logic");
            $result = $proceed($order, $forceSyncMode);
        }

        return $result;
    }
}
