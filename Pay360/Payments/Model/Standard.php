<?php
/**
 * Magento 2 Payment module from Pay360
 * Copyright (C) 2017  Pay360 by Capita
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

class Standard extends \Magento\Payment\Model\Method\AbstractMethod
{

    const PAYMENT_TYPE_AUTH = 'AUTHONLY';
    const PAYMENT_TYPE_AUCAP = 'AUTHNCAPTURE';

    const STATE_PENDING_PAY360_PAYMENT = 'pending_pay360_payment';

    const DATA_CHARSET = 'utf-8';
    const CODE = 'pay360_standard';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapturePartial = false;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * @var string
     */
    protected $_code = 'pay360_standard';


    /**
     * @var string
     */
    protected $_formBlockType = 'pay360/standard_form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'pay360/info';

    /**
     * @var array
     */
    protected $_allowCurrencyCode = array('AUD', 'CAD', 'EUR', 'HKD', 'JPY', 'NZD', 'GBP', 'SGD', 'USD');

    /**
     * get HPP order placed redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('pay360/gateway/redirect', array('_secure' => true));
    }

}
