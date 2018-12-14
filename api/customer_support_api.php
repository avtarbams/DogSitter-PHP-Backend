<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 12/12/2018
 * Time: 9:21 PM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."class_customer_support.php");
require_once (__DIR__."/../send_mail.php");

$REQUEST = $_REQUEST;
switch ($REQUEST['api_name']){
    case "get_all_complaint" :

            $customer_support = new class_customer_support();
            $response = $customer_support->get_all_complaint();
            echo json_encode($response);
        break;
    case "fetch_products_details" :

        $customer_support = new class_customer_support();
        $response = $customer_support->fetch_products_details();
        echo json_encode($response);
        break;
    case "update_products_details" :

        $customer_support = new class_customer_support();
        $response = $customer_support->update_products_details($REQUEST);
        echo json_encode($response);
        break;
    case "save_complaint" :

        $customer_support = new class_customer_support();
        $response = $customer_support->save_compliant($REQUEST);
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
                                Report ID #: TICKET_00'.$response["ticket_id"].'<br>
                                Created: '.date("M d,Y").'<br>
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
                                
                                '.$response["address1"].'<br>
                                '.$response["address2"].'
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
            
            <tr>
                <td  class="heading" style="width: 20px">
                    Issue Id
                </td>
                
                <td>
                    '.$response["ticket_id"].'
                </td>
            </tr>
            
            <tr>
                <td class="heading">
                    Subject
                </td>
                
                <td>
                    '.($REQUEST['subject']).'
                </td>
            </tr>
            
            <tr>
                <td class="heading" colspan="2">
                    Description
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    '.($REQUEST['description']).'
                </td>
            </tr>
        </table>
    </div>
</body>
</html>';
        $send_mail = new send_mail('cricheic555@gmail.com',"Issue Tracked id ",$html);
        break;
}