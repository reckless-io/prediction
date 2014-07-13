<?php
class Reckless_Prediction_Block_Adminhtml_Redemptions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'reckless_prediction';
        $this->_controller = 'adminhtml_redemptions';
        $this->_headerText = 'Reckless Redemptions';
        parent::__construct();
        $this->_removeButton('add');
    }
}
