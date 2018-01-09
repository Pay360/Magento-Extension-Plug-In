<?php

// @codingStandardsIgnoreFile

namespace Pay360\Payments\Model;

/**
 * Pay360 payment information model
 *
 * Aware of all Pay360 payment methods
 * Collects and provides access to Pay360-specific payment data
 * Provides business logic information about payment flow
 */
class Info
{
    /**
     * @var \Pay360\Payments\Model\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Pay360\Payments\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Pay360\Payments\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_transactionFactory = $transactionFactory;
        $this->_scopeConfig = $scopeConfig;
    }
    /**
     * All available payment info getter
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPaymentInfo(\Magento\Payment\Model\InfoInterface $payment, $labelValuesOnly = false)
    {
        // collect pay360-specific info
        $result = $this->_getFullInfo($payment, $labelValuesOnly);

        // TODO: add other transaction informaion like : last_trans_id

        return $result;
    }

    /**
     * Public payment info getter
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPublicPaymentInfo(\Magento\Payment\Model\InfoInterface $payment, $labelValuesOnly = false)
    {
        return $this->_getFullInfo($payment, $labelValuesOnly);
    }

    /**
     * Render info item
     *
     * @param array $keys
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param bool $labelValuesOnly
     * @return array
     */
    protected function _getFullInfo(\Magento\Payment\Model\InfoInterface $payment, $labelValuesOnly)
    {
        $order = $payment->getOrder();
        $transaction = $this->_transactionFactory->create()->load($order->getId(), 'merchant_ref');

        return $transaction->getData();
    }
}
