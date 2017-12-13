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

namespace Pay360\Payments\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_pay360_payments_profile = $setup->getConnection()->newTable($setup->getTable('pay360_payments_profile'));
        $table_pay360_payments_profile->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Profile ID'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'unsigned' => true,),
            'Customer ID'
        )
        ->addColumn(
            'profile_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            15,
            [],
            'Profile Id'
        )
        ->addColumn(
            'card_token',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            15,
            [],
            'Card Token'
        )
        ->addColumn(
            'registered',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            1,
            [],
            'Registered Customer'
        )
        ->addColumn(
            'card_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Card Type'
        )
        ->addColumn(
            'card_usage_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Card Usage Type'
        )
        ->addColumn(
            'card_scheme',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Card Scheme'
        )
        ->addColumn(
            'card_category',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Card Category'
        )
        ->addColumn(
            'masked_pan',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Masked PAN'
        )
        ->addColumn(
            'expiry_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            6,
            [],
            'Expiry Date'
        )
        ->addColumn(
            'issuer',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            30,
            [],
            'Issuer'
        )
        ->addColumn(
            'issuer_country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            30,
            [],
            'Issuer Country'
        )
        ->addColumn(
            'card_holder_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            250,
            [],
            'Card Holder Name'
        )
        ->addColumn(
            'card_nick_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            [],
            'Card Nick Name'
        )
        ->addIndex(
            $installer->getIdxName('pay360_profile_id', ['id']),
            ['id']
        )
        ->addIndex(
            $installer->getIdxName('pay360_card_token', ['card_token']),
            ['card_token']
        )
        ->addForeignKey(
            $installer->getFkName('pay360_payments_profile', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $table_pay360_payments_session = $setup->getConnection()->newTable($setup->getTable('pay360_payments_session'));
        $table_pay360_payments_session->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        )
        ->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'unsigned' => true,),
            'Order ID'
        )
        ->addColumn(
            'session_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            30,
            [],
            'Session ID'
        )
        ->addColumn(
            'session_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Session Date'
        )
        ->addColumn(
            'last_modified',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Last Modified'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            15,
            [],
            'Status'
        )
        ->addIndex(
            $installer->getIdxName('pay360_session_session_id', ['session_id']),
            ['session_id']
        )
        ->addForeignKey(
            $installer->getFkName('pay360_payments_session', 'order_id', 'sales_order', 'entity_id'),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );


        $table_pay360_payments_transaction = $setup->getConnection()->newTable($setup->getTable('pay360_payments_transaction'));
        $table_pay360_payments_transaction->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'ID'
        )
        ->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            30,
            [],
            'Transaction Id'
        )
        ->addColumn(
            'deferred',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            1,
            [],
            'Deferred'
        )
        ->addColumn(
            'merchant_ref',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            15,
            [],
            'Merchant Reference'
        )
        ->addColumn(
            'merchant_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Merchant Description'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Transaction Type'
        )
        ->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Transaction Amount'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            15,
            [],
            'Transaction Status'
        )
        ->addColumn(
            'currency',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            [],
            'Transaction Currency'
        )
        ->addColumn(
            'transaction_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Transaction Time'
        )
        ->addColumn(
            'received_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Received Time'
        )
        ->addColumn(
            'channel',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Transaction Channel'
        )
        ->addIndex(
            $installer->getIdxName('pay360_profile_id', ['id']),
            ['id']
        )
        ->addIndex(
            $installer->getIdxName('pay360_profile_transaction_id', ['transaction_id']),
            ['transaction_id']
        );

        $setup->getConnection()->createTable($table_pay360_payments_transaction);
        $setup->getConnection()->createTable($table_pay360_payments_session);
        $setup->getConnection()->createTable($table_pay360_payments_profile);

        $setup->endSetup();
    }
}
