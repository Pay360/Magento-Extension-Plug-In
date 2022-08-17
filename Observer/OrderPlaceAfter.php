<?php

namespace Pay360\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;
use Pay360\Payments\Helper\Data as Pay360Helper;
use Pay360\Payments\Helper\Logger as Pay360Logger;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Pay360Logger
     */
    protected $pay360Logger;

    /**
     * @var Pay360Helper
     */
    protected $pay360Helper;

    /**
     * @param Pay360Logger $pay360Logger
     * @param Pay360Helper $pay360Helper
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Pay360Logger $pay360Logger,
        Pay360Helper $pay360Helper
    ) {
        $this->orderRepository = $orderRepository;
        $this->pay360Logger = $pay360Logger;
        $this->pay360Helper = $pay360Helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        if($order->getEntityType() == 'order'
            && $order->getPayment()->getMethod() == \Pay360\Payments\Model\Standard::CODE) {

            $order_state = $this->pay360Helper->getOrderState($order);
            $order->setState($order_state)->setStatus($order_state);
            $this->orderRepository->save($order);
        }
    }
}
