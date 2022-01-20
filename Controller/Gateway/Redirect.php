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

class Redirect extends GatewayAbstract
{
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->_checkoutSession->getLastRealOrderId()) {
            $response = $this->_nvp->callDoPayment();
            $sessionData = $this->_sessionRepoistory->loadBySessionId($response['sessionId']);
            if (is_null($sessionData)) {
                $sessionData = $this->_sessionData;
            }
            $sessionData->setOrderId($this->_checkoutSession->getLastOrderId()) // last_order_id is entity_id 
                ->setSessionId($response['sessionId'])
                ->setSessionDate(date("Y-m-d H:i:s"))
                ->setStatus($response['status']);
            $this->_sessionRepoistory->save($sessionData);

            if ($response['status'] === \Pay360\Payments\Model\Config::STATUS_SUCCESS) {
                return $this->_resultRedirect->setUrl($response['redirectUrl']);
            } else {
                $this->messageManager->addNoticeMessage(__('Pay360 payment failed. please contact for support'));
                // reinit cart when redirect failed
                $this->_pay360Helper->reinitCart($this->_checkoutSession->getLastRealOrderId());

                return $this->_resultRedirect->setUrl('/checkout/cart/');
            }
        }

        return $this->_resultRedirect->setUrl('/');
    }
}
