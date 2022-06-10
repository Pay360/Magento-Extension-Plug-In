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

namespace Pay360\Payments\Controller\Gateway;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Redirect extends GatewayAbstract implements HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $order_id = $this->getOrderId();

        if ($order_id) {
            $response = $this->_nvp->callDoPayment($order_id);
            try {
                $sessionData = $this->_sessionRepoistory->loadBySessionId($response['sessionId']);
                if (is_null($sessionData)) {
                    $sessionData = $this->_sessionData;
                }
                $sessionData->setOrderId($order_id)
                            ->setSessionId($response['sessionId'])
                            ->setSessionDate(date("Y-m-d H:i:s"))
                            ->setStatus($response['status']);
                $this->_sessionRepoistory->save($sessionData);

                if ($response['status'] === \Pay360\Payments\Model\Config::STATUS_SUCCESS) {
                    return $this->_resultRedirect->setUrl($response['redirectUrl']);
                } else {
                    return $this->failedPay360(__('Pay360 payment failed. Please contact us for support'));
                }
            }
            catch (\Exception $e) {
                return $this->failedPay360(__('Pay360 Payment Method is not available. Please try again or contact us for support.'));
            }
        }

        return $this->_resultRedirect->setUrl('/');
    }

    public function failedPay360($msg)
    {
        $this->messageManager->addNoticeMessage($msg);
        $this->_pay360Helper->reinitCart($this->getOrderId());

        return $this->_resultRedirect->setUrl('/checkout/cart/');
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
