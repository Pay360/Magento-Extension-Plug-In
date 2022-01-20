<?php
namespace Pay360\Payments\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

class HpfProvider implements ConfigProviderInterface
{
    const CODE = 'hpf';
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder
    ) {
        $this->paymentHelper = $paymentHelper;
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
                'pay360hpf' => [
                    'method' => 'Hosted Payment Form',
                    'width' => $this->scopeConfig->getValue('payment/pay360/iframe_width'),
                    'height' => $this->scopeConfig->getValue('payment/pay360/iframe_height'),
                    'description' => $this->scopeConfig->getValue('payment/pay360/description'),
                    'actionUrl' => $this->getFrameActionUrl(),
                    'isActive' => $this->scopeConfig->getValue('payment/pay360/payment_type') == self::CODE
                ]
            ],
        ];

        return $config;
    }

    /**
     * Get frame action URL
     *
     * @return string
     */
    protected function getFrameActionUrl()
    {
        return $this->urlBuilder->getUrl('pay360/gateway/redirect/', ['_secure' => true]);
    }
}
