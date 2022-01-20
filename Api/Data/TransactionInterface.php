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

interface TransactionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const TRANSACTION_ID = 'transaction_id';
    const DEFERRED = 'deferred';
    const MERCHANT_REF = 'merchant_ref';
    const MERCHANT_DESCRIPTION = 'merchant_description';
    const TRANSACTION_TYPE = 'transaction_type';
    const AMOUNT = 'amount';
    const STATUS = 'status';
    const CURRENCY = 'currency';
    const TRANSACTION_TIME = 'transaction_time';
    const RECEIVED_TIME = 'received_time';
    const CHANNEL = 'channel';

    /**
     * Get transaction_id
     * @return string|null
     */
    public function getTransactionId();

    /**
     * Set transaction_id
     * @param string $transactionId
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setTransactionId($transactionId);

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setId($id);

    /**
     * Get deferred
     * @return boolean
     */
    public function getDeferred();

    /**
     * Set deferred
     * @param boolean $deferred
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setDeferred($deferred);

    /**
     * Get merchant_ref
     * @return string|null
     */
    public function getMerchantRef();

    /**
     * Set merchant_ref
     * @param string $buyer_id
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setMerchantRef($merchant_ref);

    /**
     * Get merchant_description
     * @return string|null
     */
    public function getMerchantDescription();

    /**
     * Set merchant_description
     * @param string $merchant_description
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setMerchantDescription($merchant_description);

    /**
     * Get transaction_type
     * @return string|null
     */
    public function getTransactionType();

    /**
     * Set transaction_type
     * @param string $transaction_type
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setTransactionType($type);

    /**
     * Get amount
     * @return integer|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param integer $amount
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setAmount($amount);

    /**
     * Get status
     * @return integer|null
     */
    public function getStatus();

    /**
     * Set status
     * @param integer $status
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setStatus($status);

    /**
     * Get currency
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set currency
     * @param string $currency
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setCurrency($currency);

    /**
     * Get transaction_time
     * @return datetime|null
     */
    public function getTransactionTime();

    /**
     * Set transaction_time
     * @param string $transaction_time
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setTransactionTime($transaction_time);

    /**
     * Get received_time
     * @return datetime|null
     */
    public function getReceivedTime();

    /**
     * Set received_time
     * @param string $received_time
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setReceivedTime($received_time);

    /**
     * Get channel
     * @return string|null
     */
    public function getChannel();

    /**
     * Set channel
     * @param string $channel
     * @return \Pay360\Payments\Api\Data\TransactionInterface
     */
    public function setChannel($channel);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Pay360\Payments\Api\Data\TransactionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Pay360\Payments\Api\Data\TransactionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Pay360\Payments\Api\Data\TransactionExtensionInterface $extensionAttributes
    );
}
