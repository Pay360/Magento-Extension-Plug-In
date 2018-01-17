<?php
namespace Pay360\Payments\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\UrlInterface;

/**
 * Class BillingAgreementConfigProvider
 */
class HppProvider implements ConfigProviderInterface
{
    const CODE = 'hpp';
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'pay360' => [
                    'method' => 'Hosted Payment Page',
                    'actionUrl' => $this->getFrameActionUrl(),
                    'isActive' => $this->scopeConfig->getValue('payment/pay360/payment_type') == self::CODE
                ]
            ]
        ];

        return $config;
    }

    /**
     * Get frame action URL
     *
     * @param string $code
     * @return string
     */
    protected function getFrameActionUrl()
    {
        return $this->urlBuilder->getUrl('pay360/gateway/redirect/', ['_secure' => true]);
    }
}
