<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 9/21/2018
 * Time: 2:29 PM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."class_payment.php");

$REQUEST = $_REQUEST;

switch ($REQUEST['api_name']){
    case "get_subscription_details" :
            $payment_details = new class_payment();
            $subscription_details = $payment_details->get_subscription_details();
            echo json_encode($subscription_details);
        break;
    case "save_subscription_details" :
            $payment_details = new class_payment();
            $save_subscription = $payment_details->save_subscription_details($REQUEST);
            echo json_encode($save_subscription);
        break;
    case "update_subscription_details":
            $payment_details = new class_payment();
            $upgrade_subscription = $payment_details->update_subscription_details($REQUEST);
        break;
    case "save_appointment_details":
            $payment_details = new class_payment();
            $save_appointment_details = $payment_details->save_appointment_details($REQUEST);
            echo json_encode($save_subscription);
        break;
}