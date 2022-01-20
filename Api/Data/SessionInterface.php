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

namespace Pay360\Payments\Api\Data;

interface SessionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ID = 'id';
    const SESSION_ID = 'session_id';
    const SESSION_DATE = 'session_date';
    const ORDER_ID = 'order_id';
    const LAST_MODIFIED = 'last_modified';
    const STATUS = 'status';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setId($id);

    /**
     * Get session_id
     * @return string|null
     */
    public function getSessionId();

    /**
     * Set session_id
     * @param string $session_id
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setSessionId($sessionId);

    /**
     * Get session_date
     * @return datetime|null
     */
    public function getSessionDate();

    /**
     * Set session_date
     * @param string $session_date
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setSessionDate($session_date);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $order_id
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setOrderId($order_id);

    /**
     * Get last_modified
     * @return datetime|null
     */
    public function getLastModified();

    /**
     * Set last_modified
     * @param string $last_modified
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setLastModified($last_modified);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Pay360\Payments\Api\Data\SessionInterface
     */
    public function setStatus($status);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Pay360\Payments\Api\Data\SessionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Pay360\Payments\Api\Data\SessionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Pay360\Payments\Api\Data\SessionExtensionInterface $extensionAttributes
    );
}
