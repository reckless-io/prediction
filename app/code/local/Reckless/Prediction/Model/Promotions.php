<?php
/**
 *
 *
 * @version     $Id$
 * @copyright   Reckless Data, 11th June 2014
 * @package     Reckless_Prediction
 * @author      Dhruv Boruah <hello@reckless.io>
 * @author      Javier Carrascal <hello@reckless.io>
 */
class Reckless_Prediction_Model_Promotions extends Mage_Core_Model_Abstract
{
    public static $errorLogFile = "";
    public static $emailMessage = "";

    const RECKLESS_SERVER_URL = 'http://app.reckless.io/machinelearning/%s/%s/%s/';
    const TEMP_TRAINING_CSV_FILENAME = "reckless-training-set-v1.csv";
    const MAX_NUM_ITEMS = 1000;

    /**
     * Constructor
     * Initialize the log file and the model
     */
    public function _construct()
    {
        $this->_init('prediction/promotions');
        self::$errorLogFile = Mage::helper('reckless_prediction')->getLogFileName();
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

    private function saveToCSV($fileName, $csvData)
    {
        $file = Mage::getBaseDir('export') . '/' . $fileName;
        $csv = new Varien_File_Csv();
        $csv->saveData($file, $csvData);
    }

    private function getUploadUrl($apiKey)
    {
        return sprintf(self::RECKLESS_SERVER_URL, 'get_train_url', $apiKey, md5($apiKey));
    }

    private function uploadTrainingData($filename)
    {
        $this->log("--> UploadTrainingData");
        //GET API Key and Domain
        $apiKey = Mage::helper('reckless_prediction')->getRecklessAPIKey();

        // Step 1: Get Upload Url
        $requestUploadUrl = $this->getUploadUrl($apiKey);
        $uploadUrl = Mage::helper('reckless_prediction')->runCurl($requestUploadUrl);
        $this->log("Upload Url Request Reply " . $uploadUrl);

        //Step 2: Upload File to Reckless Server and Train
        $trainingCSVFilePath = Mage::getBaseDir('export') . '/' . $filename;
        $post = array('file'=> '@' . $trainingCSVFilePath);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        $this->log("UploadTrainingData --> Curl Staus: " . $httpcode);

    }

    private function getUnsuccessfulCheckouts($id = null, $field = 'entity_id')
    {
        $data = array();

        $quote_collection = Mage::getModel('sales/quote')
            ->getCollection()
            ->addExpressionFieldToSelect('created', 'UNIX_TIMESTAMP(created_at)', null)
            ->setOrder('entity_id', 'ASC')
            // If quote is active, means that order hasn't been created (user didn't checkout)
            ->addFieldToFilter('is_active', array('eq' => '1'));
        if ($id) {
            $quote_collection->addFieldToFilter($field, array('eq' => $id));
        } else {
            // Delta to filter the collection
            $lastProcessed = Mage::helper('reckless_prediction')->getLastProcessedQuoteId();
            $quote_collection->addFieldToFilter('entity_id', array('gt' => $lastProcessed));
        }

        $quote_collection->getSelect()->limit(self::MAX_NUM_ITEMS);

        foreach ($quote_collection as $quote) {
            $data[] = array(
                'quote',
                $quote->getCreated(),
                $quote->getRemoteIp(),
                $quote->getBaseSubtotal() - $quote->getBaseSubtotalWithDiscount(),
                $quote->getBaseGrandTotal(),
                $quote->getItemsQty(),
                $quote->getCustomerIsGuest(),
                $quote->getBaseCurrencyCode(),
                $quote->getGlobalCurrencyCode()
            );
            if ($id) {
                $data[sizeof($data) - 1][] = $quote->getEntityId();
            }
        }
        // Update delta only if it's the cronjob training (not customer_id specific data)
        if (isset($lastProcessed) && isset($quote)) {
            Mage::helper('reckless_prediction')->setLastProcessedQuoteId($quote->getEntityId());
        }

        return $data;
    }

    private function getSuccessfulCheckouts()
    {
        $lastProcessed = Mage::helper('reckless_prediction')->getLastProcessedOrderId();
        $data = array();
        $order_collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addExpressionFieldToSelect('created', 'UNIX_TIMESTAMP(created_at)', null)
            ->addAttributeToFilter('status', array('nin' => array('canceled')))
            ->setOrder('entity_id', 'ASC');

        // Delta processing data
        $order_collection->addAttributeToFilter('entity_id', array('gt' => array($lastProcessed)));
        $order_collection->getSelect()->limit(self::MAX_NUM_ITEMS);

        foreach ($order_collection as $order) {
            $data[] = array(
                'order',
                $order->getCreated(),
                $order->getRemoteIp(),
                $order->getBaseDiscountInvoiced(),
                $order->getBaseGrandTotal(),
                $order->getTotalQtyOrdered(),
                $order->getCustomerIsGuest(),
                $order->getBaseCurrencyCode(),
                $order->getGlobalCurrencyCode()
            );
        }
        // Update delta
        if (isset($order)) {
            Mage::helper('reckless_prediction')->setLastProcessedOrderId($order->getEntityId());
        }

        return $data;
    }

    private function getAndUploadTrainingData()
    {
        $this->log("--> getAndUploadTrainingData");

        $unsuccessful = $this->getUnsuccessfulCheckouts();
        $successful = $this->getSuccessfulCheckouts();
        $trainData = array_merge($unsuccessful, $successful);

        if (sizeOf($trainData)) {
            // Re-init the config to flush the config cache with the delta values for the processed orders and quotes
            Mage::getConfig()->reinit();

            $this->saveToCSV(self::TEMP_TRAINING_CSV_FILENAME, $trainData);
            $this->uploadTrainingData(self::TEMP_TRAINING_CSV_FILENAME);
        } else {
            $this->log("Nothing to train");
        }

        $this->log("getAndUploadTrainingData --> Done");
    }

    /**
     * This method is triggered by the Cron job to train the prediction engine
     */
    public function train()
    {
        // Check if the Reckless Data Module is enabled
        if (Mage::helper('reckless_prediction')->isRecklessModuleEnabled()) {
            $this->log('Engine Training Started by Cron');
            try {
                // Start of Training Logic
                $this->getAndUploadTrainingData();
            } catch (Exception $e) {
                $this->sendAlertEmail("Error: Reckless Data Training", $e->getTraceAsString());
            }
        } else {
            $this->log("Reckless Data is DISABLED");
        }

        return $this;
    }

    /**
     * This method is triggered by the Cron job to update the predictions
     */
    public function predict()
    {
        // Check if the Reckless Data Module is enabled
        if (Mage::helper('reckless_prediction')->isRecklessModuleEnabled() &&
                Mage::helper('reckless_prediction')->isRecklessPromotionEnabled()) {
            $this->log('Prediction Started');
            try {
                $this->updatePredictions();
                //$this->generatePromoCoupons();

            } catch (Exception $e) {
                $this->sendAlertEmail("Error: Reckless Data Predictions", $e->getTraceAsString());
            }
        } else {
            $this->log("Reckless Data is Disabled");
        }

        return $this;
    }

    private function getSessionID($visitor_id)
    {
        $onlineCustomersCollection = Mage::getModel('log/visitor');
        $sessionData = $onlineCustomersCollection->load($visitor_id)->getData();

        return $sessionData['session_id'];
    }

    public function getOnlineCustomersCollection()
    {
        $onlineCustomersCollection = Mage::getModel('log/visitor_online')
            ->prepare()
            ->getCollection();
        $onlineCustomersCollection->getSelect()->joinLeft(array('log_quote'=> 'log_quote'), 'log_quote.visitor_id = main_table.visitor_id', array('log_quote.quote_id'));

        return $onlineCustomersCollection;
    }

    private function getPredictionUrl($apiKey)
    {
        return sprintf(self::RECKLESS_SERVER_URL, 'predict', $apiKey, md5($apiKey));
    }

    private function getCheckoutIntent($customerQuote)
    {
        $apiKey = Mage::helper('reckless_prediction')->getRecklessAPIKey();

        $checkoutIntent = "X";

        $checkOutIntentPredictionUrl = $this->getPredictionUrl($apiKey);
        $fields_string = http_build_query(array_slice($customerQuote, 1));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $checkOutIntentPredictionUrl);
        curl_setopt($ch, CURLOPT_POST, count($customerQuote) - 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (($httpcode) != '200') {
            $this->log("Error! Curl Url" . $checkOutIntentPredictionUrl . ", POST DATA " . print_r($customerQuote, true));
            $this->log("Response code: " . $httpcode);
            throw new Exception('Error while fetching prediction results.');
        }

        return $result;

    }

    private function createCouponCode($discount_percent, $store, $customer, $quoteValue)
    {
        $couponCode = Mage::helper('reckless_prediction')->createCouponCode($discount_percent, null, $customer, $quoteValue);

        return $couponCode;
    }

    private function updatePredictionModel($customer, $checkoutIntent, $quoteId, $predictedDiscountPercent, $quoteValue)
    {
        //Check if a record exist for the customer
        //If yes, Update the record
        //If No, Add a new record
        $collections = Mage::getModel('prediction/promotions')
            ->getCollection()
            ->addFieldToFilter('quote_id', $quoteId);

        //Get the AOV and LTV and TotalSales for the customer
        //Refactor to Magento Models later
        $aov_global_currency = 0;
        $ltv_global_currency = 0;
        $total_orders = 0;

        if ($customer->getCustomerId() != NULL) {
            $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $query = 'select customer_id, base_grand_total*base_to_global_rate as ltv_global , (base_grand_total*base_to_global_rate)/count(customer_id) as aov_global, count(customer_id) as total_orders from sales_flat_order where customer_id=' . $customer->getCustomerId();
            $results = $readConnection->fetchAll($query);
            $aov_global_currency=$results[0]['aov_global'];
            $ltv_global_currency=$results[0]['ltv_global'];
            $total_orders=$results[0]['total_orders'];
        }


        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());;

        //If no record
        if ($collections->count() < 1) {
            $this->log("NEW RECORD :  Visitor With ID: " . $customer->getVisitorId() . " will be created");
            $pmodel = Mage::getModel('prediction/promotions');
            $pmodel->setVisitorId($customer->getVisitorId())
                ->setCustomerId($customer->getCustomerId())
                ->setQuoteId($quoteId)
                ->setSessionId($this->getSessionID($customer->getVisitorId()))
                ->setCheckoutIntent($checkoutIntent)
                ->setDiscountPercent($predictedDiscountPercent)
                ->setCouponCode($this->createCouponCode($predictedDiscountPercent, null, $customer, $quoteValue))
                ->setAovGlobal($aov_global_currency)
                ->setLtvGlobal($ltv_global_currency)
                ->setTotalOrders($total_orders)
                ->setCreatedAt($currentTimestamp)
                ->setUpdatedAt($currentTimestamp)
                ->save();

        } else {
            $this->log("UPDATE RECORD : Visitor With ID: " . $customer->getVisitorId());
            //Loop
            foreach ($collections as $promo) {
                $promo->setVisitorId($customer->getVisitorId());
                $promo->setCheckoutIntent($checkoutIntent);
                $promo->setAovGlobal($aov_global_currency);
                $promo->setLtvGlobal($ltv_global_currency);
                $promo->setTotalOrders($total_orders);
                $promo->setUpdatedAt($currentTimestamp);
                if ($predictedDiscountPercent != $promo->getDiscountPercent()) {
                    //delete old code
                    //create a new code
                    Mage::helper('reckless_prediction')->deleteCouponCode($promo->getCouponCode());
                    $promo->setCouponCode($this->createCouponCode($predictedDiscountPercent, null, $customer, $quoteValue));
                }

                $promo->save();
            }
        }
    }
    private function predictAndProcess($customerCtr, $customer, $customerQuotes)
    {
        $sessionId = $this->getSessionID($customer->getVisitorId());

        //Process all Quotes and predict a checkout intent
        foreach ($customerQuotes as $customerQuote) {
            try {

                $quoteId = $customerQuote[sizeOf($customerQuote) -1];
                unset($customerQuote[sizeOf($customerQuote) -1]);

                $quoteValue = $customerQuote[sizeOf($customerQuote) - 5];

                //Step 2: Predict Y/N checkout
                $checkoutIntent = $this->getCheckoutIntent($customerQuote);
                //Step 3: Record prediction in DB
                $this->updatePredictionModel($customer, $checkoutIntent, $quoteId, Mage::helper('reckless_prediction')->getCouponDiscountPercent() , $quoteValue);
                $this->log("Customer: " . $customerCtr . ", Quote " . $quoteId . ", Visitor ID : " . $customer->getVisitorId() . ", CustomerId: " . $customer->getCustomerId() . ", Session ID: " . $sessionId . ", Checkout Intent: " . $checkoutIntent);

            } catch (Exception $e) {
                $this->sendAlertEmail("Error: Reckless Data Predicting", $e->getTraceAsString());
                Mage::logException($e);
            }
        }
    }

    private function updatePredictions()
    {
        $this->log("--> updatePredictions");
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $onlineCustomersCollection = $this->getOnlineCustomersCollection();
        $this->log("Online Customers Size: " . $onlineCustomersCollection->count());
        $customerCtr = 0;
        foreach ($onlineCustomersCollection as $customer) {
            $customerQuotes = null;
            $customerCtr++;

            //Step 1: Get Quote data if id is present
            if ($customer->getQuoteId()) {
                $this->log($customer->getQuoteId());
                $customerQuotes = $this->getUnsuccessfulCheckouts($customer->getQuoteId());
            } elseif ($customer->getCustomerId()) {
                // Attempt to fetch the quote for the logged customer
                $customerQuotes = $this->getUnsuccessfulCheckouts($customer->getCustomerId(), 'customer_id');
            }

            if (($customer->getQuoteId() || $customer->getCustomerId()) && sizeOf($customerQuotes)) {
                $this->log($customerQuotes);
                $this->predictAndProcess($customerCtr, $customer, $customerQuotes);
            } else {
                $this->log("Customer: " . $customerCtr . ", Visitor Id: " . $customer->getVisitorId() . ". Error: NO Quotes for Customer " . $customer->getCustomerId());
            }
        }
        $this->log("updatePredictions -->");
    }
    private function sendAlertEmail($subject, $body)
    {
        $helper = Mage::helper('reckless_prediction');
        $helper->notify($helper->getMonitorNotifyEmail(), $helper->getMonitorNotifyEmail(), $subject, $body);
    }
}
