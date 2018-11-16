<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 9/20/2018
 * Time: 5:02 PM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."/class_common.php");

$REQUEST = $_REQUEST;
switch ($REQUEST['api_name']){
    case "get_roles":
            $class_common = new class_common();
            $response = $class_common->getRoles();
            echo  json_encode($response);
        break;
    case "get_user_upload":
            $class_common = new class_common();
            $response = $class_common->getPhotoUploads($REQUEST);
            echo  json_encode($response);
        break;
    case "save_feedback_comment":
            $class_common = new class_common();
            $response = $class_common->save_feedback_comment($REQUEST);
        break;
    case "get_user_feedback":
            $class_common = new class_common();
            $response = $class_common->get_user_feedback($REQUEST);
            echo  json_encode($response);
        break;
    case "fetch_products_details":
            $class_common = new class_common();
            $response = $class_common->fetch_products_details();
            echo json_encode($response);
        break;

}

function print_array($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}