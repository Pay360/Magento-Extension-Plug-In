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

namespace Pay360\Payments\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProfileRepositoryInterface
{


    /**
     * Save Profile
     * @param \Pay360\Payments\Api\Data\ProfileInterface $profile
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Pay360\Payments\Api\Data\ProfileInterface $profile
    );

    /**
     * Retrieve Profile
     * @param string $profileId
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($profileId);

    /**
     * Retrieve Profile matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Pay360\Payments\Api\Data\ProfileSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Profile
     * @param \Pay360\Payments\Api\Data\ProfileInterface $profile
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Pay360\Payments\Api\Data\ProfileInterface $profile
    );

    /**
     * Delete Profile by ID
     * @param string $profileId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($profileId);
}
