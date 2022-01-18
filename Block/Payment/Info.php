<?php
namespace Pay360\Payments\Block\Payment;

/**
 * Pay360 common payment info block
 * Uses default templates
 */
class Info extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var \Pay360\Payments\Api\TransactionRepositoryInterface
     */
    protected $_transactionRepository;

    /**
     * @var \Pay360\Payments\Model\InfoFactory
     */
    protected $_infoFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Pay360\Payments\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Pay360\Payments\Helper\Data $gr4vyHelper
     * @param \Pay360\Payments\Model\InfoFactory $infoFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Pay360\Payments\Api\TransactionRepositoryInterface $transactionRepository,
        \Pay360\Payments\Helper\Data $gr4vyHelper,
        \Pay360\Payments\Model\InfoFactory $infoFactory,
        array $data = []
    ) {
        $this->_infoFactory = $infoFactory;
        $this->_transactionRepository = $transactionRepository;
        $this->_helper = $gr4vyHelper;
        parent::__construct($context, $paymentConfig, $data);
    }

    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
        return parent::getCcTypeName();
    }

    /**
     * Prepare Pay360-specific payment information
     *
     * @param \Magento\Framework\DataObject|array|null $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $pay360_transaction_id = $payment->getData('pay360_transaction_id');
        $transaction = $this->_transactionRepository->getByTransactionId($pay360_transaction_id);

        /*prepare labels*/
        $last_trans_id = (string)__('Last Transaction ID');
        $amount = (string)__('Amount');
        $captured_amount = (string)__('Captured Amount');
        $refunded_amount = (string)__('Refunded Amount');
        $currency = (string)__('Currency');
        $status = (string)__('Status');

        /*prepare data*/
        $captured = $transaction->getCapturedAmount() ? $this->_helper->formatCurrency($transaction->getCapturedAmount()/100) : 0;
        $refunded = $transaction->getRefundedAmount() ? $this->_helper->formatCurrency($transaction->getRefundedAmount()/100) : 0;
        $data = array(
            $last_trans_id => $transaction->getTransactionId(),
            $status => ucwords(str_replace('_', ' ',$transaction->getStatus())),
            $amount => $this->_helper->formatCurrency($transaction->getAmount()/100),
            $captured_amount => $captured ?: '0.00',
            $refunded_amount => $refunded ?: '0.00',
            $currency => $transaction->getCurrency()
        );

        return $transport->addData($data);
    }
}
