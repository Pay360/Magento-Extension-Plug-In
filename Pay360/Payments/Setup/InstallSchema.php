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
            'profile_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_pay360_payments_profile->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'id'
        );
        

        $table_pay360_payments_session = $setup->getConnection()->newTable($setup->getTable('pay360_payments_session'));

        
        $table_pay360_payments_session->addColumn(
            'session_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_pay360_payments_session->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'id'
        );
        

        $table_pay360_payments_transaction = $setup->getConnection()->newTable($setup->getTable('pay360_payments_transaction'));

        
        $table_pay360_payments_transaction->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_pay360_payments_transaction->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'id'
        );
        

        $setup->getConnection()->createTable($table_pay360_payments_transaction);

        $setup->getConnection()->createTable($table_pay360_payments_session);

        $setup->getConnection()->createTable($table_pay360_payments_profile);

        $setup->endSetup();
    }
}
