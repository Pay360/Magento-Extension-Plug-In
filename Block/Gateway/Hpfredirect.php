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

namespace Pay360\Payments\Block\Gateway;

class Hpfredirect extends \Magento\Framework\View\Element\Template
{
    protected $_config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Pay360\Payments\Model\Config $config,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_config = $config;
    }

    public function getRedirectUrl()
    {
        return $this->_config->getUrlBuilder()->getUrl('checkout/onepage/success', array('_secure' => true));
    }

    public function getCancelRedirectUrl()
    {
        return $this->_config->getUrlBuilder()->getUrl('checkout/cart/index', array('_secure' => true));
    }
}
