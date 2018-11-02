<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 11/1/2018
 * Time: 7:18 PM
 */

require_once (__DIR__."/../config.php");
require_once (DB_CONNECTION_FILE_PATH."/db_connection.php");
require_once (CLASS_FILE_PATH."/class_login.php");
require_once (CLASS_FILE_PATH."/class_common.php");

class class_appointment
{
    private $db_connection;

    function class_appointment()
    {
        $this->db_connection =
            new db_connection(DB_SERVER_NAME, DB_USER_NAME, DB_PASSWORD, DB_NAME);
    }

    public function get_available_pet_sitter($search_details){

        $select_pet_sitter = "SELECT psd.user_details_userid AS userid FROM pet_sitter__details WHERE ps_details_id NOT IN(
                                SELECT pet_sitter_details_ps_details_id FROM ".DB_NAME.".appointment_booking_details WHERE 
                                abd.appointment_date = '".$search_details['filter_date']."')";

        $res = $this->db_connection->query($select_pet_sitter,1);
        if($this->db_connection->num_of_rows($res)){
            $rows = $this->db_connection->fetch_data($res);
            $login_det = new class_login();
            $common_det = new class_common();
            foreach($rows as $key=>$userid){
                $userid_det['user_id'] = $userid;
                $sitter_user_details = $login_det->get_user_details($userid_det);

                $sitter_user_details['latest_feedback'] = $common_det->get_user_feedback($userid_det,1);

            }
        }
    }
}