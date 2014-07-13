<?php
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('prediction/promotions'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Entity Id')
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Visitor Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            'default'   => '0',
        ), 'Customer Id')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Quote Id')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Session Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            'default'   => '0',
        ), 'Group Id')
    ->addColumn('checkout_intent', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        ), 'Checkout Intent')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Coupon code')
    ->addColumn('discount_percent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            'default'   => '0',
        ), 'Discount Percent')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
        ), 'Updated At')
    ->addIndex($installer->getIdxName('prediction/promotions', array('visitor_id')),
        array('visitor_id'))
    ->addIndex($installer->getIdxName('prediction/promotions', array('quote_id')),
        array('quote_id'))
    ->addIndex($installer->getIdxName('prediction/promotions', array('coupon_code')),
        array('coupon_code'))
    ->addForeignKey($installer->getFkName('prediction/promotions', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Reckless Promotions Entity');
$installer->getConnection()->createTable($table);
$installer->endSetup();
