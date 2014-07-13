<?php

class Reckless_Prediction_Model_Promotion_Observer
{
    public static $errorLogFile = "";

    /**
     * Constructor
     * Initialize the log file and the model
     */
    public function _construct()
    {
        self::$errorLogFile = Mage::helper('reckless_prediction')->getLogFileName();
    }

    public function show_predicted_coupon_code($observer)
    {
        if (!Mage::getSingleton('core/session')->getPromotionShowed()) {
            $event = $observer->getEvent();
            $collection = Mage::getModel('prediction/promotions')
                ->getCollection()
                ->addFieldToFilter('session_id', session_id());
            foreach ($collection as $discount) {
                if (strcasecmp($discount->getCheckoutIntent(),'Y') == 0) {
                    Mage::getSingleton('core/session')->addNotice(
                        Mage::helper('reckless_prediction')->getCustomerPromoMessageYES() . ' ' . $discount->getCouponCode()
                    );
                } else {
                    Mage::getSingleton('core/session')->addNotice(
                        Mage::helper('reckless_prediction')->getCustomerPromoMessageNO() . ' ' . $discount->getCouponCode()
                    );
                }
                Mage::getSingleton('core/session')->setPromotionShowed(true);
            }
        }

        return $this;
    }

    /**
     * Generic function to log to a consistent log file
     *
     * @param unknown_type $message -  Message to log
     */
    public function log($message)
    {
        if (Mage::helper('reckless_prediction')->isLogEnabled()) {
            Mage::log($message, null, self::$errorLogFile);
        } else {
            return;
        }
    }
}
