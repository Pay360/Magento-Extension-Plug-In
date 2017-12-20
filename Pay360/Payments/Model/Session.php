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

use Pay360\Payments\Api\Data\SessionInterface;

class Session extends \Magento\Framework\Model\AbstractModel implements SessionInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Pay360\Payments\Model\ResourceModel\Session');
    }

    /**
     * Get session_id
     * @return string
     */
    public function getSessionId()
    {
        return $this->getData(self::SESSION_ID);
    }

    /**
     * Set session_id
     * @param string $sessionId
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setSessionId($sessionId)
    {
        return $this->setData(self::SESSION_ID, $sessionId);
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
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
}
