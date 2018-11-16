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
            echo json_encode($save_appointment_details);
        break;
    case "purchase_product":
        $payment_details = new class_payment();
        $purchase_product = $payment_details->purchase_product($REQUEST);

        break;
    case "send_email":
            $payment_details = new class_payment();
        $receipt['userid'] = 3 ;
        $receipt['product_details_id'] = 3 ;
        $receipt['payment_for'] = 'product';
        $receipt['type_of_payment'] = 'credit_card';
        $receipt['card_details'] ='{"card_number":"43234567890","card_owner_name":"qwert","card_exp_month":"07","card_exp_year":"2023","card_zipcode":"07306","is_card_saved":"false"}';
        $receipt['service_id'] = 5;
        $receipt['payment_amt'] = 16.31;
            $save_appointment_details = $payment_details->send_product_receipt($receipt);
        break;
}