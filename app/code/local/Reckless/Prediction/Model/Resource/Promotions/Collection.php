<?php
/**
 *
 *
 * @version     $Id$
 * @copyright   Reckless Data, 11th June 2014
 * @package     Reckless_Prediction
 * @author      Dhruv Boruah <hello@reckless.io>
 */
class Reckless_Prediction_Model_Resource_Promotions_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('prediction/promotions');
    }
}
