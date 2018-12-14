<?php
/**
 * Created by PhpStorm.
 * User: Richie
 * Date: 9/14/2018
 * Time: 7:44 PM
 */
header("Access-Control-Allow-Origin: *");
error_reporting(0);

//server constant defination
    define("DB_SERVER_NAME", "localhost");
    define("DB_USER_NAME", "root");
    define("DB_PASSWORD","");


//DB Names

    define("DB_NAME", "welovepets");

##################################################################


    define("SUCCESS_MSG", "Data Found.");
    define("ERROR_MSG", "Data Not Found.");
    define("SUCCESS", 1);
    define("FAILED", 0);

#################################################################


    define("CLASS_FILE_PATH",__DIR__."/functionDir/");
    define("DB_CONNECTION_FILE_PATH", __DIR__."/db/");
    define("CONFIG_FILE_PATH",__DIR__);
    define("INVOICE_FILE_PATH",__DIR__."/invoice/");

##################################################################

    define("WE_LOVE_PETS_EMAIL","vlovepets.vars@gmail.com");
    define("WE_LOVE_PETS_PASS","Welovepets@123");


