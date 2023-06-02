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

namespace Pay360\Payments\Model\Source;

class CommerceType implements \Magento\Framework\Option\ArrayInterface
{

    const TYPE_ECOM = 'ECOM';
    const TYPE_MOTO = 'MOTO';
    //const TYPE_CNP = 'CNP';

    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_ECOM, 'label' => __('Ecommerce')],
            ['value' => self::TYPE_MOTO, 'label' => __('MOTO - Mail Order / Telephone Order')],
        ];
    }

    public function toArray()
    {
        return [
            self::TYPE_ECOM => __('Ecommerce'),
            self::TYPE_MOTO => __('MOTO - Mail Order / Telephone Order'),
        ];
    }
}
