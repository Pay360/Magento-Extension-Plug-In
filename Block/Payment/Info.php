<?php
namespace Pay360\Payments\Block\Payment;

/**
 * Pay360 common payment info block
 * Uses default templates
 */
class Info extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var \Pay360\Payments\Model\InfoFactory
     */
    protected $_infoFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Pay360\Payments\Model\InfoFactory $infoFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Pay360\Payments\Model\InfoFactory $infoFactory,
        array $data = []
    ) {
        $this->_infoFactory = $infoFactory;
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
        $info = $this->_infoFactory->create();
        if ($this->getIsSecureMode()) {
            $info = $info->getPublicPaymentInfo($payment, true);
        } else {
            $info = $info->getPaymentInfo($payment, true);
        }
        return $transport->addData($info);
    }
}
