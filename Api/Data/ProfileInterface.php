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

interface ProfileInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const REGISTERED = 'registered';
    const PROFILE_ID = 'profile_id';
    const CARD_TOKEN = 'card_token';
    const CARD_TYPE = 'card_type';
    const CARD_USAGE_TYPE = 'card_usage_type';
    const CARD_SCHEME = 'card_scheme';
    const CARD_CATEGORY = 'card_category';
    const CARD_HOLDER_NAME = 'card_holder_name';
    const CARD_NICK_NAME = 'card_nick_name';
    const MASKED_PAN = 'masked_pan';
    const EXPIRY_DATE = 'expiry_date';
    const ISSUER = 'issuer';
    const ISSUER_COUNTRY = 'issuer_country';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setId($id);

    /**
     * Get profile_id
     * @return string|null
     */
    public function getProfileId();

    /**
     * Set profile_id
     * @param string $profile_id
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setProfileId($profileId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customer_id
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get registered
     * @return boolean|null
     */
    public function getRegistered();

    /**
     * Set registered
     * @param boolean $registered
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setRegistered($registered);

    /**
     * Get card_token
     * @return string|null
     */
    public function getCardToken();

    /**
     * Set card_token
     * @param string $card_token
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCardToken($card_token);

    /**
     * Get card_type
     * @return string|null
     */
    public function getCardType();

    /**
     * Set card_type
     * @param string $card_type
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCardType($card_type);

    /**
     * Get card_usage_type
     * @return string|null
     */
    public function getCardUsageType();

    /**
     * Set card_usage_type
     * @param string $card_usage_type
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCardUsageType($card_usage_type);

    /**
     * Get card_scheme
     * @return string|null
     */
    public function getCardScheme();

    /**
     * Set card_scheme
     * @param string $card_scheme
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCardScheme($card_scheme);

    /**
     * Get card_category
     * @return string|null
     */
    public function getCardCategory();

    /**
     * Set card_category
     * @param string $card_category
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCardCategory($card_category);

    /**
     * Get card_holder_name
     * @return string|null
     */
    public function getCardHolderName();

    /**
     * Set card_holder_name
     * @param string $card_holder_name
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCardHolderName($card_holder_name);

    /**
     * Get card_nick_name
     * @return string|null
     */
    public function getCardNickName();

    /**
     * Set card_nick_name
     * @param string $card_nick_name
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setCardNickName($card_nick_name);

    /**
     * Get masked_pan
     * @return string|null
     */
    public function getMaskedPan();

    /**
     * Set masked_pan
     * @param string $masked_pan
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setMaskedPan($masked_pan);

    /**
     * Get expiry_date
     * @return string|null
     */
    public function getExpiryDate();

    /**
     * Set expiry_date
     * @param string $expiry_date
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setExpiryDate($expiry_date);

    /**
     * Get issuer
     * @return string|null
     */
    public function getIssuer();

    /**
     * Set issuer
     * @param string $issuer
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setIssuer($issuer);

    /**
     * Get issuer_country
     * @return string|null
     */
    public function getIssuerCountry();

    /**
     * Set issuer_country
     * @param string $issuer_country
     * @return \Pay360\Payments\Api\Data\ProfileInterface
     */
    public function setIssuerCountry($issuer_country);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Pay360\Payments\Api\Data\ProfilerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Pay360\Payments\Api\Data\ProfileExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Pay360\Payments\Api\Data\ProfileExtensionInterface $extensionAttributes
    );
}
