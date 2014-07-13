<?php
/**
 * Helper Class for Reckless Data Module
 *
 * @version     $Id$
 * @copyright   Reckless Data, 11th June 2014
 * @package     Reckless_Prediction
 * @author      Dhruv Boruah <hello@reckless.io>
 */
class Reckless_Prediction_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Constants for different configurations in Admin Panel
     */
    const RECKLESS_CORE_ENABLED     = 'reckless_prediction/reckless_prediction_section/reckless_prediction_enable';
    const RECKLESS_CORE_API_KEY	    = 'reckless_prediction/reckless_prediction_section/reckless_prediction_api_key';
    const RECKLESS_CORE_BASE_URL    = 'reckless_prediction/reckless_prediction_section/reckless_prediction_base_url';
    const RECKLESS_CORE_SYNC_CUST_EMAIL     = 'reckless_prediction/reckless_prediction_section/reckless_prediction_sync_customer_email';
    const RECKLESS_CORE_NOTIFY_EMAIL    = 'reckless_prediction/reckless_prediction_section/reckless_prediction_notificationemail';
    const RECKLESS_CORE_ENABLE_LOG     = 'reckless_prediction/reckless_prediction_section/reckless_prediction_enablelog';
    const RECKLESS_CORE_LOG_FILE    = 'reckless_prediction/reckless_prediction_section/reckless_prediction_logfilename';

    const RECKLESS_CORE_LAST_ORDER = 'reckless_prediction/reckless_prediction_section/reckless_prediction_lastorder';
    const RECKLESS_CORE_LAST_QUOTE = 'reckless_prediction/reckless_prediction_section/reckless_prediction_lastquote';

    const RECKLESS_PROMOTIONS_ENABLED    = 'reckless_prediction/reckless_promotions_section/reckless_dynamic_promotion_enable';
    const RECKLESS_PRMOTIONS_THRESHOLD     = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promotion_threshould';
    const RECKLESS_PROMOTIONS_VAILDITY     = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocodevalid_time';
    const RECKLESS_PROMOTIONS_VAILDITY_STARTSFROM     = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocodevalid_starts_from';
    const RECKLESS_PROMOTIONS_STOPRULESPROCESSING     = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocode_stoprulesprocessing';
    const RECKLESS_PROMOTIONS_COUPONTYPE    = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocode_type';
    const RECKLESS_PROMOTIONS_USAGEPERCUSTOMER   = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocode_usagepercustomer';
    const RECKLESS_PROMOTIONS_COUPONUSAGE    = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocode_usage';
    const RECKLESS_PROMOTIONS_COUPONPREFIX    = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocode_prefix';
    const RECKLESS_PROMOTIONS_COUPONDESC    = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocode_desc';
    const RECKLESS_PROMOTIONS_COUPONDESC_TYPE    = 'reckless_prediction/reckless_promotions_section/reckless_dynamic_promotion_discount_type';
    const RECKLESS_PROMOTIONS_COUPONDISCPERCENT    = 'reckless_prediction/reckless_promotions_section/reckless_prediction_promocode_percent';
    const RECKLESS_PROMOTIONS_CUSTOMER_PROMO_MESSAGE_YES = 'reckless_prediction/reckless_promotions_section/reckless_prediction_customer_promo_message_yes';
    const RECKLESS_PROMOTIONS_CUSTOMER_PROMO_MESSAGE_NO = 'reckless_prediction/reckless_promotions_section/reckless_prediction_customer_promo_message_no';

    /**
     * Check if Reckless Module is enabled
     *
     * @return  bool
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function isRecklessModuleEnabled()
    {
        return (bool) Mage::getStoreConfig(self::RECKLESS_CORE_ENABLED);
    }

    public function isPromoCodeValueAPercentOfCurrentCart()
    {
        return (bool) Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONDESC_TYPE);
    }

    /**
     * Check if Log is Enabled
     *
     * @return  bool
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function isLogEnabled($store = null)
    {
        return (bool) Mage::getStoreConfig(self::RECKLESS_CORE_ENABLE_LOG);
    }

    /**
     * Check if Reckless Customer Sync is enabled
     *
     * @return  bool
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function isCustomerSyncEnabled()
    {
        return (bool) Mage::getStoreConfig(self::RECKLESS_CORE_SYNC_CUST_EMAIL);
    }

    /**
     * Get the Reckless API Key
     *
     * @return  string - returns "SIGNUP FOR API KEY" if this is not set
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function getRecklessAPIKey()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_CORE_API_KEY)) == 0) {
            throw new Exception("ERROR : API Key Missing. Please SIGNUP FOR API KEY");
        } else {
            return Mage::getStoreConfig(self::RECKLESS_CORE_API_KEY);
        }
    }

    /**
     * Set the Reckless API Key
     *
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function setRecklessAPIKey($apikey)
    {
        if (strlen($apikey) == 0) {
            throw new Exception("ERROR : API Key Missing. Please SIGNUP FOR API KEY");
        } else {
            Mage::getModel('core/config')->saveConfig(self::RECKLESS_CORE_API_KEY, $apikey);
        }
    }

    /**
     * Get the Reckless Base Url
     *
     * @return  string - returns "ENTER BASE URL" if this is not set
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function getRecklessBaseUrl()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_CORE_BASE_URL)) == 0) {
            throw new Exception("ERROR : Missing Base Url for Reckless.io Verification");
        } else {
            return Mage::getStoreConfig(self::RECKLESS_CORE_BASE_URL);
        }
    }

    /**
     * Set the Reckless Base Url
     *
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function setRecklessBaseUrl($domain)
    {
        if (strlen($domain) == 0) {
            throw new Exception("ERROR : Missing Base Url for Reckless.io Verification");
        } else {
            Mage::getModel('core/config')->saveConfig(self::RECKLESS_CORE_BASE_URL, $domain);
        }
    }

    /**
     * Get the Reckless Nofify Email
     *
     * @return  string
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function getMonitorNotifyEmail()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_CORE_NOTIFY_EMAIL)) == 0) {
            throw new Exception("ERROR : Configured Notification Email is Empty");
        } else {
            return Mage::getStoreConfig(self::RECKLESS_CORE_NOTIFY_EMAIL);
        }
    }

    /**
     * Get the LogFileName
     *
     * @return  string
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function getLogFileName()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_CORE_LOG_FILE)) == 0) {
            return false;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_CORE_LOG_FILE);
        }
    }

    /**
     * Check if Reckless Promotion is enabled
     *
     * @return  bool
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function isRecklessPromotionEnabled()
    {
        return (bool) Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_ENABLED);
    }

    /**
     * Get Promotion Threshold
     * Defaut: 10 GBP , Prediction for multiple stores and for the default value for different websites
     *
     * @return  string
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function getPromotionThreshold($store = null)
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PRMOTIONS_THRESHOLD)) == 0) {
            return 10;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PRMOTIONS_THRESHOLD);
        }
    }

    /**
     * Get Promotion Validity
     * Defaut: 86400 Minutes ( 60 days)
     * @return  string
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function getPromotionValidity($store = null)
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_VAILDITY)) == 0) {
            return 60;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_VAILDITY);
        }
    }

    /**
     * Get Promotion Valifity Start From
     * Defaut: 0 Days (Immediately which is Today)
     * @return  string
     * @author  Dhruv Boruah <hello@reckless.io>
     */
    public function getPromotionValidityStartFrom()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_VAILDITY_STARTSFROM)) == 0) {
            return 0;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_VAILDITY_STARTSFROM);
        }
    }

    /**
     * Method to send notification emails
     * @param unknown_type $sendToName
     * @param unknown_type $sendToEmail
     * @param unknown_type $subject
     * @param unknown_type $msg
     */
    public function notify($sendToName, $sendToEmail, $subject, $msg)
    {
        $mail = Mage::getModel('core/email');
        $mail->setToName($sendToName);
        $mail->setToEmail($sendToEmail);
        $mail->setBody($msg);
        $mail->setSubject('=?utf-8?B?' . base64_encode($subject) . '?=');
        $mail->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $mail->setFromName(Mage::getStoreConfig('general/store_information/name'));
        $mail->setType('html');

        try {
            $mail->send();
        } catch (Exception $e) {
            Mage::logException($e);

            return false;
        }

        return true;
    }

    public function runCurl($curl_url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curl_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $response = curl_exec($curl);
        //$status = curl_getinfo($curl);
        curl_close($curl);

        return $response;
    }

    public function checkVersion()
    {
        $val = 1;

        if (Mage::getVersion() < 1.3) {
            $val = 0;
        } elseif (Mage::getVersion() > 1.3) {
            if (Mage::getVersion() == '1.4.0.1') {
                $val = 1;
            } else {
                $val = 2;
            }
        }

        return $val;
    }

    public function getRuleModel()
    {
        if ($this->checkVersion() == 2) {
            return Mage::getModel('salesrule/coupon');
        } else {
            return Mage::getModel('salesrule/rule');
        }
    }

    public function getCouponPostfix($customer)
    {
        if ($customer->getCustomerId() == NULL) {
            return "G";
        }
        return $customer->getCustomerId();
    }

    public function getCouponAction()
    {
        if($this->isPromoCodeValueAPercentOfCurrentCart()) {
            return 'cart_fixed';
        } else {
            return 'by_percent';
        }
    }

    public function getCouponValue($quoteValue)
    {
        Mage::log("Getting Coupon Value Total " . $quoteValue);
        if ($this->isPromoCodeValueAPercentOfCurrentCart()) {
            return ($quoteValue * $this->getCouponDiscountPercent()) / 100;
        } else

            return min(100, $this->getCouponDiscountPercent());
    }
    /*
     * Function to create
     *		a) Promotion Rule and
     * 		b) coupon codes for Old and New Magento Versions
     */

    public function createCouponCode($discount_percent, $store, $customer, $quoteValue)
    {
        //$discount_percent : Not used in current prediction model

        $coupon= $this->getCouponPrefix() . Mage::helper('core')->getRandomString(4) . "-" . $this->getCouponPostfix($customer);
        Mage::log("Creating Coupon " . $coupon);
        $model = $this->getRuleModel();

        $now = new DateTime('NOW');
        $couponValidFromDate = new DateTime('NOW');
        $couponValidFromDate->add(new DateInterval('P' . $this->getPromotionValidityStartFrom() . 'D'));
        $couponValidFromDate = $couponValidFromDate->format(DateTime::ISO8601);

        $intervalToAdd = "P" . $this->getPromotionValidity()  . "D";// Days
        $couponValidToDate = $now->add(new DateInterval($intervalToAdd));

           $discount_amount = 0;

        try {

            if ($this->checkVersion() == 2) {

                $rule = Mage::getModel('salesrule/rule')
                    ->setName($coupon)
                    ->setDescription($this->getCouponDescription() . $this->getCouponAction() . $customer->getCustomerId())
                    ->setFromDate(date($couponValidFromDate))
                    ->setToDate($couponValidToDate->format('Y-m-d H:i:s'))
                    ->setCustomerGroupIds($this->getCustomerGroups())
                    ->setIsActive(1)
                    ->setSimpleAction($this->getCouponAction())
                    ->setDiscountAmount($this->getCouponValue($quoteValue))
                    ->setStopRulesProcessing($this->getStopRulesProcessing())
                    ->setUseAutoGeneration(0)
                    ->setIsRss(1)
                    ->setUsesPerCoupon($this->getMaxCouponUsage())
                    ->setUsesPerCustomer($this->getUsagePerCustomer())
                    ->setExpirationDate($couponValidToDate)
                    ->setWebsiteIds($this->getAllWebsites())
                    ->setCouponType($this->getCouponType())
                    ->save();

                $model->setRuleId($rule->getId())
                    ->setCode($coupon)
                    ->setIsPrimary(1)
                    ->save();

            } else {
                $model
                    ->setName($coupon)
                    ->setDescription($this->getCouponDescription() . $this->getCouponAction() . $customer->getCustomerId())
                    ->setFromDate(date($couponValidFromDate))
                    ->setCouponCode($coupon)
                    ->setToDate($couponValidToDate->format('Y-m-d H:i:s'))
                    ->setCustomerGroupIds($this->getCustomerGroups())
                    ->setIsActive(1)
                    ->setSimpleAction($this->getCouponAction())
                    ->setUseAutoGeneration(0)
                    ->setWebsiteIds($this->getAllWebsites())
                    ->setDiscountAmount($this->getCouponValue($quoteValue))
                    ->setStopRulesProcessing($this->getStopRulesProcessing())
                    ->setIsRss(1)
                    ->setUsesPerCoupon($this->getMaxCouponUsage())
                    ->setUsesPerCustomer($this->getUsagePerCustomer())
                    ->setExpirationDate($couponValidToDate)
                    ->setCouponType($this->getCouponType())
                    ->save();

            }
        } catch (exception $e) {
            return $e->getMessage();
        }

        return $coupon;

    }

    /*
     * Function to delete a Coupon Code and the Associated Rule
     */
    public function deleteCouponCode($couponCode)
    {
        $couponModel = Mage::getModel('salesrule/coupon')
            ->getCollection()
            ->addFieldToFilter('code', $couponCode)
            ->getFirstItem();
        $ruleId = $couponModel->getRuleId();

        $ruleModel = Mage::getModel('salesrule/rule')
            ->getCollection()
            ->addFieldToFilter('rule_id', $ruleId)
            ->getFirstItem();

        $couponModel->delete();
        $ruleModel->delete();
    }

    /*
     * Function to return the website ids
     */
    protected function getAllWebsites()
    {
        $website_ids = array();
        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website) {
            $website_ids[] = $website->getId();
        }

        return $website_ids;
    }

    /*
     * Function to return the customer groups
     */
    protected function getCustomerGroups()
    {
        $groupIds = array();
        $collection = Mage::getModel('customer/group')->getCollection();
        foreach ($collection as $customer) {
            $groupIds[] = $customer->getId();
        }

        return $groupIds;
    }

    public function getStopRulesProcessing()
    {
        return (bool) Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_STOPRULESPROCESSING);
    }

    public function getCouponType()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONTYPE)) == 0) {
            return 2;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONTYPE);
        }
    }

    public function getUsagePerCustomer()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_USAGEPERCUSTOMER)) == 0) {
            return 1;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_USAGEPERCUSTOMER);
        }
    }

    public function getMaxCouponUsage()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONUSAGE)) == 0) {
            return 1;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONUSAGE);
        }
    }

    public function getCouponPrefix()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONPREFIX)) == 0) {
            return "PRSL";
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONPREFIX);
        }
    }

    public function getCouponDescription()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONDESC)) == 0) {
            return "Personalised Promo Code for customer ";
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONDESC);
        }
    }

    public function getCouponDiscountPercent()
    {
        if (strlen(Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONDISCPERCENT)) == 0) {
            return 5;
        } else {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_COUPONDISCPERCENT);
        }
    }

    public function getLastProcessedOrderId()
    {
        return Mage::getStoreConfig(self::RECKLESS_CORE_LAST_ORDER) ?: 0;
    }

    public function setLastProcessedOrderId($id)
    {
        Mage::getModel('core/config')->saveConfig(self::RECKLESS_CORE_LAST_ORDER, $id);
    }

    public function getLastProcessedQuoteId()
    {
        return Mage::getStoreConfig(self::RECKLESS_CORE_LAST_QUOTE) ?: 0;
    }

    public function setLastProcessedQuoteId($id)
    {
        Mage::getModel('core/config')->saveConfig(self::RECKLESS_CORE_LAST_QUOTE, $id);
    }

    public function getCustomerPromoMessageYES()
    {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_CUSTOMER_PROMO_MESSAGE_YES);
    }

    public function getCustomerPromoMessageNO()
    {
            return Mage::getStoreConfig(self::RECKLESS_PROMOTIONS_CUSTOMER_PROMO_MESSAGE_NO);
    }
}
