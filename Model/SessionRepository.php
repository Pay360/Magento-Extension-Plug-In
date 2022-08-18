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

use Pay360\Payments\Api\SessionRepositoryInterface;
use Pay360\Payments\Api\Data\SessionSearchResultsInterfaceFactory;
use Pay360\Payments\Api\Data\SessionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Pay360\Payments\Model\ResourceModel\Session as ResourceSession;
use Pay360\Payments\Model\ResourceModel\Session\CollectionFactory as SessionCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class SessionRepository implements sessionRepositoryInterface
{

    protected $resource;

    protected $sessionFactory;

    protected $sessionCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataSessionFactory;

    private $storeManager;

    protected $extensibleDataObjectConverter;

    /**
     * @var searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;


    /**
     * @param ResourceSession $resource
     * @param SessionFactory $sessionFactory
     * @param SessionInterfaceFactory $dataSessionFactory
     * @param SessionCollectionFactory $sessionCollectionFactory
     * @param SessionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceSession $resource,
        SessionFactory $sessionFactory,
        SessionInterfaceFactory $dataSessionFactory,
        SessionCollectionFactory $sessionCollectionFactory,
        SessionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->sessionFactory = $sessionFactory;
        $this->sessionCollectionFactory = $sessionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSessionFactory = $dataSessionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Pay360\Payments\Api\Data\SessionInterface $session
    ) {
        /* if (empty($session->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $session->setStoreId($storeId);
        } */

        $sessionData = $this->extensibleDataObjectConverter->toNestedArray(
            $session,
            [],
            \Pay360\Payments\Api\Data\SessionInterface::class
        );
        
        $sessionModel = $this->sessionFactory->create()->setData($sessionData);

        try {
            $this->resource->save($sessionModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the session: %1',
                $exception->getMessage()
            ));
        }
        return $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($sessionId)
    {
        $session = $this->sessionFactory->create();
        $session->getResource()->load($session, $sessionId);
        if (!$session->getId()) {
            throw new NoSuchEntityException(__('Session with id "%1" does not exist.', $sessionId));
        }
        return $session;
    }

    /**
     * load transaction by session_id
     * @param $session_id
     *
     * @return $this
     */
    public function loadBySessionId($session_id)
    {
        $transactionSearchCriteria = $this->searchCriteriaBuilder->addFilter('session_id', $session_id, 'eq')->create();
        $transactionSearchResults = $this->getList($transactionSearchCriteria);

        if ($transactionSearchResults->getTotalCount() > 0) {
            list($item) = array_values($transactionSearchResults->getItems());
            return $item->getDataModel();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->sessionCollectionFactory->create();
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
        \Pay360\Payments\Api\Data\SessionInterface $session
    ) {
        try {
            $session->getResource()->delete($session);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Session: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sessionId)
    {
        return $this->delete($this->getById($sessionId));
    }
}
