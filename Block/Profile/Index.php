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

namespace Pay360\Payments\Block\Profile;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;
    protected $_customerSession;
    protected $_profileFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Pay360\Payments\Model\ProfileFactory $profileFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_profileFactory = $profileFactory;
    }

    public function getCollection()
    {
        return $this->_profileFactory->create()->getCollection()->addCustomerFilter($this->_customerSession->getCustomer()->getId());
    }
}
