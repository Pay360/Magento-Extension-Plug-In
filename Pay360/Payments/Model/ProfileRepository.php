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

use Pay360\Payments\Api\ProfileRepositoryInterface;
use Pay360\Payments\Api\Data\ProfileSearchResultsInterfaceFactory;
use Pay360\Payments\Api\Data\ProfileInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Pay360\Payments\Model\ResourceModel\Profile as ResourceProfile;
use Pay360\Payments\Model\ResourceModel\Profile\CollectionFactory as ProfileCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ProfileRepository implements profileRepositoryInterface
{

    protected $resource;

    protected $profileFactory;

    protected $profileCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataProfileFactory;

    private $storeManager;


    /**
     * @param ResourceProfile $resource
     * @param ProfileFactory $profileFactory
     * @param ProfileInterfaceFactory $dataProfileFactory
     * @param ProfileCollectionFactory $profileCollectionFactory
     * @param ProfileSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceProfile $resource,
        ProfileFactory $profileFactory,
        ProfileInterfaceFactory $dataProfileFactory,
        ProfileCollectionFactory $profileCollectionFactory,
        ProfileSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->profileFactory = $profileFactory;
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataProfileFactory = $dataProfileFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Pay360\Payments\Api\Data\ProfileInterface $profile
    ) {
        /* if (empty($profile->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $profile->setStoreId($storeId);
        } */
        try {
            $profile->getResource()->save($profile);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the profile: %1',
                $exception->getMessage()
            ));
        }
        return $profile;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($profileId)
    {
        $profile = $this->profileFactory->create();
        $profile->getResource()->load($profile, $profileId);
        if (!$profile->getId()) {
            throw new NoSuchEntityException(__('Profile with id "%1" does not exist.', $profileId));
        }
        return $profile;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->profileCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Pay360\Payments\Api\Data\ProfileInterface $profile
    ) {
        try {
            $profile->getResource()->delete($profile);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Profile: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($profileId)
    {
        return $this->delete($this->getById($profileId));
    }
}
