<?php
$this->startSetup();

$baseurl = Mage::getBaseUrl();
$domain = parse_url($baseurl, PHP_URL_HOST);
$apikey = md5($domain);

//Mage::log("\n BaseURL: " . $baseurl . ". Domain: " .  $domain . " Hash: " . md5($domain));
$curl_url = "http://app.reckless.io/machinelearning/create/"
    . $domain
    . "/"
    . $apikey
    . "/";

Mage::log("calling " . $curl_url);

// Register the client app with Reckless Data Servers & Setup the default Values
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $curl_url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
$response = curl_exec($curl);
//$status = curl_getinfo($curl);
curl_close($curl);

Mage::helper('reckless_prediction')->setRecklessBaseUrl($domain);
Mage::helper('reckless_prediction')->setRecklessAPIKey($response);

// Reinit the config to flush the config cache with the new settings saved
Mage::getConfig()->reinit();
$this->endSetup();
