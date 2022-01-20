<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Pay360\Payments\Model\Data;

use Pay360\Payments\Api\Data\ProfileInterface;

class Profile extends \Magento\Framework\Api\AbstractExtensibleObject implements ProfileInterface
{

    /**
     * {@inheritdoc}
     */
    public function getProfileId()
    {
        return $this->_get(self::TRANSACTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProfileId($transactionId)
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
    public function getRegistered()
    {
        return $this->_get(self::REGISTERED);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegistered($registered)
    {
        return $this->setData(self::REGISTERED, $registered);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardToken()
    {
        return $this->_get(self::CARD_TOKEN);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardToken($card_token)
    {
        return $this->setData(self::CARD_TOKEN, $card_token);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardType()
    {
        return $this->_get(self::CARD_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardType($card_type)
    {
        return $this->setData(self::CARD_TYPE, $card_type);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardUsageType()
    {
        return $this->_get(self::CARD_USAGE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardUsageType($card_usage_type)
    {
        return $this->setData(self::CARD_USAGE_TYPE, $card_usage_type);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardScheme()
    {
        return $this->_get(self::CARD_SCHEME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardScheme($card_scheme)
    {
        return $this->setData(self::CARD_SCHEME, $card_scheme);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardCategory()
    {
        return $this->_get(self::CARD_CATEGORY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardCategory($card_category)
    {
        return $this->setData(self::CARD_CATEGORY, $card_category);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardHolderName()
    {
        return $this->_get(self::CARD_HOLDER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardHolderName($card_holder_name)
    {
        return $this->setData(self::CARD_HOLDER_NAME, $card_holder_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardNickName()
    {
        return $this->_get(self::CARD_NICK_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardNickName($card_nick_name)
    {
        return $this->setData(self::CARD_NICK_NAME, $card_nick_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaskedPan()
    {
        return $this->_get(self::MASKED_PAN);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaskedPan($masked_pan)
    {
        return $this->setData(self::MASKED_PAN, $masked_pan);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryDate()
    {
        return $this->_get(self::EXPIRY_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryDate($expiry_date)
    {
        return $this->setData(self::EXPIRY_DATE, $expiry_date);
    }

    /**
     * {@inheritdoc}
     */
    public function getIssuer()
    {
        return $this->_get(self::ISSUER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIssuer($issuer)
    {
        return $this->setData(self::ISSUER, $issuer);
    }

    /**
     * {@inheritdoc}
     */
    public function getIssuerCountry()
    {
        return $this->_get(self::ISSUER_COUNTRY);
    }

    /**
     * {@inheritdoc}
     */
    public function setIssuerCountry($issuer_country)
    {
        return $this->setData(self::ISSUER_COUNTRY, $issuer_country);
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
        \Pay360\Payments\Api\Data\ProfileExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
