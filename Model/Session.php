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
declare(strict_types=1);

namespace Pay360\Payments\Model;

use Pay360\Payments\Api\Data\SessionInterface;
use Pay360\Payments\Api\Data\SessionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Session extends \Magento\Framework\Model\AbstractModel
{

    protected $sessionDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'pay360_session';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param SessionInterfaceFactory $sessionDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Pay360\Payments\Model\ResourceModel\Session $resource
     * @param \Pay360\Payments\Model\ResourceModel\Session\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        SessionInterfaceFactory $sessionDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Pay360\Payments\Model\ResourceModel\Session $resource,
        \Pay360\Payments\Model\ResourceModel\Session\Collection $resourceCollection,
        array $data = []
    ) {
        $this->sessionDataFactory = $sessionDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve session model with session data
     * @return SessionInterface
     */
    public function getDataModel()
    {
        $sessionData = $this->getData();
        
        $sessionDataObject = $this->sessionDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $sessionDataObject,
            $sessionData,
            SessionInterface::class
        );
        
        return $sessionDataObject;
    }
}
