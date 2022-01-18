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

interface SessionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Session list.
     * @return \Pay360\Payments\Api\Data\SessionInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Pay360\Payments\Api\Data\SessionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
