<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 12/13/2018
 * Time: 12:18 AM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."/class_login.php");
require_once (DB_CONNECTION_FILE_PATH."/db_connection.php");

class class_customer_support
{
    private $db_conn;
    function class_customer_support(){
        $this->db_conn =
            new db_connection(DB_SERVER_NAME,DB_USER_NAME,DB_PASSWORD,DB_NAME);
    }

    function save_compliant($data){

        $insert = "INSERT INTO  ".DB_NAME.".customer_report_table SET
                        crt_subject = '".$data['subject']."',
                        crt_description = '".$data['description']."',
                        user_details_userid = '".$data['userid']."'";

        $this->db_conn->query($insert);

        $ticket_id = $this->db_conn->get_last_insert_id();


        $user_details = new class_login();

        $user_info = $user_details->get_user_details($data['userid']);
        $response = $user_info['data']['0'];
        $response['ticket_id'] = $ticket_id;
        $response['address1'] = $response['street'].",".$response['apt_num'];
        $response['address2'] = $response['city'].",".$response['state']."-".$response['zipcode'];
        return $response;
    }
    function get_all_complaint(){
        $login = new class_login();;

        $select_complaint = "SELECT * FROM ".DB_NAME.".customer_report_table";

        $res_complaint = $this->db_conn->query($select_complaint);
        if($this->db_conn->num_of_rows($res_complaint)>0){
            $result_data = $this->db_conn->fetch_data($res_complaint);
            foreach ($result_data as $key=>$value){
                $result_data[$key]['user_data'] = $login->get_user_details($value['user_details_userid'])['data']['0'];
            }
            $response['status'] = SUCCESS;
            $response['msg'] = SUCCESS_MSG;
            $response['data'] = $result_data;
        }
        else{
            $response['status'] = FAILED;
            $response['msg'] = ERROR_MSG;
        }
        return $response;
    }

    function fetch_products_details(){
        $get_product_details = "SELECT * FROM " . DB_NAME . ".product_details ";
        $res_prod = $this->db_conn->query($get_product_details);
        $return_data = [];
        if ($this->db_conn->num_of_rows($res_prod)>0){
            $result = $this->db_conn->fetch_data($res_prod);
            $return_data['status'] = SUCCESS;
            $return_data['msg'] = "Success";
            $return_data['data'] = $result;
        }
        else{
            $return_data['status'] = FAILED;
            $return_data['msg'] = "No records Found";
            $return_data['data'] = '';
        }


        return $return_data;
    }

    function update_products_details($data){
        $get_product_details = "UPDATE " . DB_NAME . ".product_details SET  
                                    product_delete_flag = 1
                                    WHERE product_details_id=".$data['product_details_id'];
        $res_prod = $this->db_conn->query($get_product_details);
        return $this->fetch_products_details();
    }
}