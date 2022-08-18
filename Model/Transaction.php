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

use Pay360\Payments\Api\Data\TransactionInterface;
use Pay360\Payments\Api\Data\TransactionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Transaction extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_FAILED = 'FAILED';

    protected $transactionDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'pay360_transaction';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param TransactionInterfaceFactory $transactionDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Pay360\Payments\Model\ResourceModel\Transaction $resource
     * @param \Pay360\Payments\Model\ResourceModel\Transaction\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        TransactionInterfaceFactory $transactionDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Pay360\Payments\Model\ResourceModel\Transaction $resource,
        \Pay360\Payments\Model\ResourceModel\Transaction\Collection $resourceCollection,
        array $data = []
    ) {
        $this->transactionDataFactory = $transactionDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve transaction model with transaction data
     * @return TransactionInterface
     */
    public function getDataModel()
    {
        $transactionData = $this->getData();
        
        $transactionDataObject = $this->transactionDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $transactionDataObject,
            $transactionData,
            TransactionInterface::class
        );
        
        return $transactionDataObject;
    }
}
