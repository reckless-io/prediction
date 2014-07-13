<?php
class Reckless_Prediction_Block_Adminhtml_Predictions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reckless_promotionGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('prediction/promotions')
            ->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $currencyCode = Mage::app()->getBaseCurrencyCode();

        $this->addColumn(
            'entity_id',
            array(
                'header' => 'ID',
                'align' =>'right',
                'width' => '50px',
                'index' => 'entity_id',
                'type' => 'number',
            )
        );
        $this->addColumn(
            'customer_id',
            array(
                'header' => 'CustomerID',
                'align' =>'right',
                'index' => 'customer_id',
                'type' => 'number',
            )
        );
        $this->addColumn(
            'quote_id',
            array(
                'header' => 'QuoteID',
                'align' =>'right',
                'index' => 'quote_id',
                'type' => 'number',
            )
        );

        $this->addColumn(
            'checkout_intent',
            array(
                'header' => 'Checkout',
                'align' =>'right',
                'index' => 'checkout_intent',
            )
        );
        $this->addColumn(
            'coupon_code',
            array(
                'header' => 'Coupon Code',
                'align' =>'left',
                'index' => 'coupon_code',
            )
        );
        $this->addColumn(
            'discount_percent',
            array(
                'header' => 'Discount',
                'align' =>'left',
                'index' => 'discount_percent',
            )
        );
        $this->addColumn(
            'aov_global',
            array(
                'header' => 'Average Order Value',
                'align' =>'left',
                'type'  => 'price',
                'currency_code' => $currencyCode,
                'index' => 'aov_global',
            )
        );
        $this->addColumn(
            'ltv_global',
            array(
                'header' => 'LifeTime Value',
                'align' =>'left',
                'type'  => 'price',
                'currency_code' => $currencyCode,
                'index' => 'ltv_global',
            )
        );
        $this->addColumn(
            'total_orders',
            array(
                'header' => 'Total Orders',
                'align' =>'left',
                'index' => 'total_orders',
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => 'Created At',
                'align' =>'left',
                'index' => 'created_at',
                'type' => 'datetime',

            )
        );

        $this->addExportType('*/*/exportPredictionsCsv', 'CSV');
        $this->addExportType('*/*/exportPredictionsExcel', 'Excel');

        return parent::_prepareColumns();
    }
}
