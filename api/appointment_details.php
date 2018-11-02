<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 11/1/2018
 * Time: 7:08 PM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."/class_appointment.php");

$REQUEST = $_REQUEST;
switch ($REQUEST['api_name']) {
    case "search_pet_sitter":
            $appt_det = new class_appointment();
            $response = $appt_det->get_available_pet_sitter($REQUEST);
            echo json_encode($response);
        break;
}