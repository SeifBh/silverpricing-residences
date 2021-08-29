<?php
define("ENVIRONMENT", isset($_SERVER['WINDIR'])?'DEV':"PROD");
// GLOBAL
define('DRUPAL_RESIDENCE_DATA', __DIR__ . "/residences");
define('DRUPAL_RESIDENCE_DATA_OUTPUT', __DIR__ . "/output");
define("RESIDENCE_MGMT_CNSA_API", "https://www.pour-les-personnes-agees.gouv.fr/api/v1");

// DISTANCE INDEXATION
// DATA PROCESSING

// DATA COLLECTION
define('DRUPAL_RESIDENCE_DATA_SCRAPPING', 200);
define('DRUPAL_RESIDENCE_DATA_QUEUE', __DIR__ . "/cron_config");

// ALERT PROCESSING
define('DRUPAL_RESIDENCE_ALERT_PRICE_UPDATED', 1);

if( ENVIRONMENT == "PROD" ) {

    $_SERVER['REMOTE_ADDR'] = "http://residences.silverpricing.fr";
    define('DRUPAL_ROOT', "/home/ubuntu/SilverPricing/public_html/app2.silverpricing.fr");

} else if( ENVIRONMENT == "DEV" ) {
    $_SERVER['REMOTE_ADDR'] = "https://ehpad.home";
    define('DRUPAL_ROOT', 'C:/Users/ben/home/ehpad/app/');
}
if(!isset($_SERVER['DOCUMENT_ROOT']))$_SERVER['DOCUMENT_ROOT']=DRUPAL_ROOT;

