<?php
namespace Magento\Paypal\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Paypal\Model\Billing\AgreementFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Paypal\Model\Payment\Method\Billing\AbstractAgreement;

/**
 * Class BillingAgreementConfigProvider
 */
class HppProvider implements ConfigProviderInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var AgreementFactory
     */
    protected $agreementFactory;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param AgreementFactory $agreementFactory
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        AgreementFactory $agreementFactory
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->agreementFactory = $agreementFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'paypalBillingAgreement' => [
                    'agreements' => $this->getBillingAgreements(),
                    'transportName' => AbstractAgreement::TRANSPORT_BILLING_AGREEMENT_ID
                ]
            ]
        ];

        return $config;
    }
}
