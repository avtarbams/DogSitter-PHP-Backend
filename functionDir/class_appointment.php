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

        $filter_date = date("Y-m-d 00:00:00",time($search_details['filter_date']));
        $query_booked = "SELECT abd.pet_sitter_details_ps_details_id FROM ".DB_NAME.".appointment_booking_details abd WHERE 
                                abd.appointment_date = '".$filter_date."'";
        $res_booked = $this->db_connection->query($query_booked);
        if ($this->db_connection->num_of_rows($res_booked)>0){
            $result = $this->db_connection->fetch_data($res_booked);
            $where_query = "WHERE psd.ps_details_id NOT IN('".implode("','",$result[0])."')";
        }
        else{
            $where_query = "";
        }
        $select_pet_sitter = "SELECT psd.user_details_userid AS userid FROM ".DB_NAME.".pet_sitter_details psd ".$where_query;

        $res = $this->db_connection->query($select_pet_sitter);
        $pet_sitter_details = [];

        if($this->db_connection->num_of_rows($res)){
            $rows = $this->db_connection->fetch_data($res);
            $login_det = new class_login();
            $common_det = new class_common();

            foreach($rows as $key=>$userid){
                $userid_det['user_id'] = $userid['userid'];
                $pet_details = $login_det->get_user_details($userid_det);

                $sitter_user_details = $pet_details['data'][0];

                $sitter_user_details['latest_feedback'] = $common_det->get_user_feedback($userid_det,1);
                $pet_sitter_details[] = $sitter_user_details;
            }
            $response['status'] = SUCCESS;
            $response['msg'] = SUCCESS_MSG;
            $response['data'] = $pet_sitter_details;
            return $response;
        }
        else{
            $response['status'] = FAILED;
            $response['msg'] = ERROR_MSG;
            return $response;
        }
    }
}