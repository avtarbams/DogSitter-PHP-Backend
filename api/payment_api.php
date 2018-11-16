<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 9/21/2018
 * Time: 2:29 PM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."class_payment.php");
require_once (__DIR__."/../send_mail.php");

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
        /*$receipt['userid'] = 3 ;
        $receipt['product_details_id'] = 3 ;
        $receipt['payment_for'] = 'product';
        $receipt['type_of_payment'] = 'credit_card';
        $receipt['card_details'] ='{"card_number":"43234567890","card_owner_name":"qwert","card_exp_month":"07","card_exp_year":"2023","card_zipcode":"07306","is_card_saved":"false"}';
        $receipt['service_id'] = 5;
        $receipt['payment_amt'] = 16.31;*/

       echo $html = '<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>A simple, clean, and responsive HTML invoice template</title>
    
    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="https://webpage.pace.edu/rc87337n/RichieCarvalho_U01442486/project/welovepetslogo1.png" style="width:100%; max-width:300px;">
                            </td>
                            
                            <td>
                                Invoice #: PRODUCT-123<br>
                                Created: Nov 16, 2015<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Rutwik Deokar.<br>
                                322 St. Pauls Ave<br>
                                Jersey City,07306
                            </td>
                            
                            <td>
                                Richie Carvalho.<br>
                                Support<br>
                                vlovepets.vars@gmail.com
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Payment Method
                </td>
                
                <td>
                    Credit Card #
                </td>
            </tr>
            
            <tr class="details">
                <td>
                    Credit Card
                </td>
                
                <td>
                    $'.($REQUEST['amount']+10).'
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Item
                </td>
                
                <td>
                    Price
                </td>
            </tr>
            
            <tr class="item">
                <td>
                    Product
                </td>
                
                <td>
                    $'.$REQUEST['amount'].'.00
                </td>
            </tr>
            
            <tr class="item last">
                <td>
                    Domain name (1 year)
                </td>
                
                <td>
                    $10.00
                </td>
            </tr>
            
            <tr class="total">
                <td></td>
                
                <td>
                   Total: $'.($REQUEST['amount']+10).'
                </td>
            </tr>
        </table>
    </div>
</body>
</html>';
       $send_mail = new send_mail($REQUEST['email_id'],"Product Purchase Receipt",$html);
        break;
}