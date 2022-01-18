<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Pay360\Magento\Model\Data;

use Pay360\Magento\Api\Data\TransactionInterface;

class Transaction extends \Magento\Framework\Api\AbstractExtensibleObject implements TransactionInterface
{

    /**
     * Get transaction_id
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->_get(self::TRANSACTION_ID);
    }

    /**
     * Set transaction_id
     * @param string $transactionId
     * @return \Pay360\Magento\Api\Data\TransactionInterface
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeferred()
    {
        return $this->_get(self::DEFERRED);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeferred($deferred)
    {
        return $this->setData(self::DEFERRED, $deferred);
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantRef()
    {
        return $this->_get(self::MERCHANT_REF);
    }

    /**
     * {@inheritdoc}
     */
    public function setMerchantRef($merchant_ref)
    {
        return $this->setData(self::MERCHANT_REF, $merchant_ref);
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantDescription()
    {
        return $this->_get(self::MERCHANT_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMerchantDescription($merchant_description)
    {
        return $this->setData(self::MERCHANT_DESCRIPTION, $merchant_description);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType()
    {
        return $this->_get(self::TRANSACTION_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionType($transaction_type)
    {
        return $this->setData(self::TRANSACTION_TYPE, $transaction_type);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->_get(self::CURRENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionTime()
    {
        return $this->_get(self::TRANSACTION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionTime($transaction_time)
    {
        return $this->setData(self::TRANSACTION_TIME, $transaction_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getReceivedTime()
    {
        return $this->_get(self::RECEIVED_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setReceivedTime($received_time)
    {
        return $this->setData(self::RECEIVED_TIME, $received_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        return $this->_get(self::CHANNEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setChannel($channel)
    {
        return $this->setData(self::CHANNEL, $channel);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Pay360\Magento\Api\Data\TransactionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
