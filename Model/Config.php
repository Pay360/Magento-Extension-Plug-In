<?php
/**
 * Magento 2 Payment module from Pay360
 * Copyright (C) 2022  Pay360 by Capita
 *
 * This file is part of Pay360/Payments.
 *
 * Pay360/Payments is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


namespace Pay360\Payments\Model;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Helper\Context;

class Config
{
    const PAYMENT_TYPE_PREAUTH = 'PREAUTH';
    const PAYMENT_TYPE_REFUND = 'REFUND';
    const PAYMENT_TYPE_PAYMENT = 'PAYMENT';
    const PAYMENT_TYPE_CAPTURE = 'CAPTURE';

    const PAYMENT_STATUS_SUCCESS = 'SUCCESS';
    const PAYMENT_STATUS_FAILED = 'FAILED';

    const AUTH_STATUS_AUTHORISED = 'AUTHORISED';
    const AUTH_STATUS_DECLINED = 'DECLINED';

    const RESPOND_PROCEED = 'PROCEED';
    const RESPOND_CANCEL = 'CANCEL';
    const RESPOND_SUSPEND = 'SUSPEND';
    const RESPOND_SUSPEND_REPLAY = 'SUSPEND_REPLAY';

    const HPP_MAKE_PAYMENT = '/hosted/rest/sessions/{installation_id}/payments';
    const API_MAKE_AUTHORISE = '/acceptor/rest/transactions/{installation_id}/payment';
    const API_MAKE_CAPTURE = '/acceptor/rest/transactions/{installation_id}/{transaction_id}/capture';
    const API_MAKE_CANCEL = '/acceptor/rest/transactions/{installation_id}/{transaction_id}/cancel';
    const API_MAKE_REFUND = '/acceptor/rest/transactions/{installation_id}/{transaction_id}/refund';
    const API_GET_DETAIL = '/acceptor/rest/transactions/{installation_id}/{transaction_id}';
    
    const STATUS_SUCCESS = 'SUCCESS';

    const DEFAULT_TIMEOUT = 60;
    const DEFAULT_CHANNEL = 'WEB';
    const DEFAULT_COMMERCE_TYPE = 'ECOM';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context
    ) {
        $this->_context = $context;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeId = null;
    }

    /**
     *  $context->getModuleManager()
     *  $context->getUrlBuilder()
     *  $context->getRequest()
     *  $context->getCacheConfig()
     *  $context->getEventManager()
     *  $context->getLogger()
     *  $context->getHttpHeader()
     *  $context->getRemoteAddress()
     *  $context->getUrlEncoder()
     *  $context->getUrlDecoder()
     *  $context->getScopeConfig()
     */

    /**
     * Store ID setter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * @return \Magento\Framework\Module\Manager
     */
    public function getModuleManager()
    {
        return $this->_context->getModuleManager();
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_context->getUrlBuilder();
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_context->getRequest();
    }

    /**
     * @return \Magento\Framework\Cache\ConfigInterface
     */
    public function getCacheConfig()
    {
        return $this->_context->getCacheConfig();
    }

    /**
     * @return \Magento\Framework\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_context->getEventManager();
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->_context->getLogger();
    }

    /**
     * @return \Magento\Framework\HTTP\Header
     */
    public function getHttpHeader()
    {
        return $this->_context->getHttpHeader();
    }

    /**
     * @return \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public function getRemoteAddress()
    {
        return $this->_context->getRemoteAddress();
    }

    /**
     * @return \Magento\Framework\Url\EncoderInterface
     */
    public function getUrlEncoder()
    {
        return $this->_context->getUrlEncoder();
    }

    /**
     * @return \Magento\Framework\Url\DecoderInterface
     */
    public function getUrlDecoder()
    {
        return $this->_context->getUrlDecoder();
    }

    /**
     * Returns payment configuration value of Pay360
     *
     * @param string $key
     * @param null $storeId
     * @return null|string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getValue($key, $storeId = null)
    {
        $this->_storeId = !empty($storeId) ? $storeId : $this->_storeId;
        switch ($key) {
            case 'getDebugReplacePrivateDataKeys':
                return $this->methodInstance->getDebugReplacePrivateDataKeys();
            default:
                if ($key !== null) {
                    $value = $this->_scopeConfig->getValue(
                        $key,
                        ScopeInterface::SCOPE_STORE,
                        $this->_storeId
                    );
                    return $value;
                }
        }
        return null;
    }

    /**
     * Check module is in test mode
     *
     * @return boolean
     */
    public function isTest()
    {
        return $this->getValue('payment/pay360/test');
    }

    /**
     * @param string $method Method code
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isMethodActive($method)
    {
    }
}
