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

use Magento\Sales\Model\Order;

class OrderStatus implements \Magento\Framework\Option\ArrayInterface
{
    const STATE_PENDING = 'pending';

    public function toOptionArray()
    {
        return [
            ['value' => Order::STATE_NEW, 'label' => __('New')],
            ['value' => self::STATE_PENDING, 'label' => __('Pending')],
            ['value' => Order::STATE_PROCESSING, 'label' => __('Processing')],
            ['value' => Order::STATE_PENDING_PAYMENT, 'label' => __('Pending Payment')]
        ];
    }

    public function toArray()
    {
        return [
            Order::STATE_NEW => __('New'),
            self::STATE_PENDING => __('Pending'),
            Order::STATE_PROCESSING => __('Processing'),
            Order::STATE_PENDING_PAYMENT => __('Pending Payment')
        ];
    }
}
