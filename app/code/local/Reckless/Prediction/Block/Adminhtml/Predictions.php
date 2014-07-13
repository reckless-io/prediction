<?php
class Reckless_Prediction_Block_Adminhtml_Predictions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'reckless_prediction';
        $this->_controller = 'adminhtml_predictions';
        $this->_headerText = 'Reckless Promotions';
        parent::__construct();
        $this->_removeButton('add');
    }
}
