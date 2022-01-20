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

use Pay360\Payments\Api\Data\ProfileInterface;
use Pay360\Payments\Api\Data\ProfileInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Profile extends \Magento\Framework\Model\AbstractModel
{

    protected $profileDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'pay360_profile';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ProfileInterfaceFactory $profileDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Pay360\Payments\Model\ResourceModel\Profile $resource
     * @param \Pay360\Payments\Model\ResourceModel\Profile\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ProfileInterfaceFactory $profileDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Pay360\Payments\Model\ResourceModel\Profile $resource,
        \Pay360\Payments\Model\ResourceModel\Profile\Collection $resourceCollection,
        array $data = []
    ) {
        $this->profileDataFactory = $profileDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve profile model with profile data
     * @return ProfileInterface
     */
    public function getDataModel()
    {
        $profileData = $this->getData();
        
        $profileDataObject = $this->profileDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $profileDataObject,
            $profileData,
            ProfileInterface::class
        );
        
        return $profileDataObject;
    }

    /**
     * init pay360 profile by masked pan and customer id. only 1 maskedPan for 1 customer is allowed
     * @param string $masked_pan
     * @param integer $customer_id
     * @return $this
     */
    public function initProfileByMaskedPan($masked_pan, $customer_id)
    {
        $profile = $this->getCollection()
            ->addFieldToFilter('masked_pan', array('eq' => $masked_pan))
            ->addFieldToFilter('customer_id', array('eq' => $customer_id))
            ->getFirstItem();

        $profile = $profile->getId() ? $profile : $this;
        $profile->setMaskedPan($masked_pan)->setCustomerId($customer_id);

        return $profile;
    }
}

