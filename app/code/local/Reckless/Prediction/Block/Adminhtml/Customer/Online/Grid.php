<?php
/**
 * Reckless_Prediction_Block_Adminhtml_Customer_Online_Grid
 *
 * @uses Mage_Adminhtml_Block_Customer_Online_Grid
 * @package
 * @version $id$
 * @copyright 2014, Reckless.io
 * @author Javier Carrascal <hello@reckless.io>
 */
class Reckless_Prediction_Block_Adminhtml_Customer_Online_Grid extends Mage_Adminhtml_Block_Customer_Online_Grid
{
    /**
     * Initialize Grid block
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add custom column to the grid
     *
     */
    public function setCollection($collection)
    {
        $collection->getSelect()->joinLeft(
            array('reckless_promo'=> 'reckless_promotions'),
            'reckless_promo.visitor_id = main_table.visitor_id',
            array('reckless_promo.checkout_intent')
        );

        // Prevent collection exception when same visitor_id has more than one quote
        $collection->getSelect()->group('main_table.visitor_id');
        parent::setCollection($collection);
    }

    /**
     * Prepare columns
     *
     * @return Reckless_Prediction_Block_Adminhtml_Customer_Online_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn(
            'checkout_intent',
            array(
                'header'    => Mage::helper('customer')->__('Checkout Intent'),
                'default'   => Mage::helper('customer')->__('n/a'),
                'index'     => 'checkout_intent',
            )
        );
    }
}
