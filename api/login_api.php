<?php
/**
 * Created by PhpStorm.
 * User: Richie
 * Date: 9/14/2018
 * Time: 7:21 PM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."/class_login.php");
session_start();

$REQUEST = $_REQUEST;

switch ($REQUEST['api_name']){
    case "login_check":
            $login_obj = new class_login();
            $login_check = $login_obj->login_check($REQUEST['email_id'],$REQUEST['password']);
            echo  json_encode($login_check);
        break;
    case "create_user_signup":
            $login_obj = new class_login();
            $login_resp = $login_obj->create_user_sign_up($REQUEST);
            echo json_encode($login_resp);
        break;
    case "update_user_details":
            $login_obj = new class_login();
            $login_resp = $login_obj->update_user_details($REQUEST);
            echo json_encode($login_resp);
        break;
    case "update_password":
            $login_obj = new class_login();
            $login_resp = $login_obj->update_password($REQUEST);
            echo json_encode($login_resp);
        break;
    case "delete_user":
        $login_obj = new class_login();
        $login_resp = $login_obj->delete_user($REQUEST);
        echo  json_encode($login_resp);
}

function print_array($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}