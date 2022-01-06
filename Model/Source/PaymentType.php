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

namespace Pay360\Payments\Model\Source;

class PaymentType implements \Magento\Framework\Option\ArrayInterface
{

    const TYPE_HPP = 'hpp';
    const TYPE_HPF = 'hpf';

    public function toOptionArray()
    {
        return [['value' => 'hpp', 'label' => __('Hosted Payment Page (Redirect)')],
            ['value' => 'hpf', 'label' => __('Hosted Payment Frame')]];
    }

    public function toArray()
    {
        return [
            'hpp' => __('Hosted Payment Page (Redirect)'),
            'hpf' => __('Hosted Payment Frame'),
        ];
    }
}
