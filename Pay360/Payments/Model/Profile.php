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

use Pay360\Payments\Api\Data\ProfileInterface;

class Profile extends \Magento\Framework\Model\AbstractModel implements ProfileInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Pay360\Payments\Model\ResourceModel\Profile');
    }

    /**
     * Get profile_id
     * @return string
     */
    public function getProfileId()
    {
        return $this->getData(self::PROFILE_ID);
    }

    /**
     * Set profile_id
     * @param string $profileId
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setProfileId($profileId)
    {
        return $this->setData(self::PROFILE_ID, $profileId);
    }

    /**
     * Get id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param string $id
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
}
