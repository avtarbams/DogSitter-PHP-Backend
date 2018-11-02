<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 9/21/2018
 * Time: 2:42 PM
 */

require_once (__DIR__."/../config.php");
require_once (DB_CONNECTION_FILE_PATH."/db_connection.php");

class class_payment
{
    private $db_conn;
    function class_payment(){
        $this->db_conn =
            new db_connection(DB_SERVER_NAME,DB_USER_NAME,DB_PASSWORD,DB_NAME);
    }

    public function get_subscription_details(){
        $select_subscription = "SELECT subscription_detials_id,subscription_name,details,package_amt 
                                    FROM " . DB_NAME . ".subscription_details";
        $res_select_subscription = $this->db_conn->query($select_subscription);

        if($this->db_conn->num_of_rows($res_select_subscription)>0){
            $return_subscription = [];
            $rows_subscription = $this->db_conn->fetch_data($res_select_subscription);
            foreach ($rows_subscription as $row_key=>$value_row){
                $subscription_details = [];
                foreach ($value_row as $key=>$value){
                    $subscription_details[$key] = $value;
                }
                $return_subscription[$row_key] = $subscription_details;
            }
            $return_data['status'] = SUCCESS;
            $return_data["msg"] = SUCCESS_MSG;
            $return_data["data"] = $return_subscription;
        }
        else{
            $return_data['status'] = FAILED;
            $return_data["msg"] = ERROR_MSG;
        }
        return $return_data;
    }

    public function save_subscription_details($subscription_details){
        $subscription_start_date  = date("Y-m-d 00:00:00", time($subscription_details['start_date']));
        $subscription_end_date = date("Y-m-d 23:59:59", time($subscription_details['end_date']));

        $insert_sub_purchase = "INSERT INTO " . DB_NAME . ".subscription_purchase_details
                                    SET 
                                        subscription_start_date             = ".$subscription_start_date.",
                                        subscription_end_date               = ".$subscription_end_date.",
                                        dog_owner_details_do_details_id     = '".$subscription_details['dog_owner_id']."',
                                        subscription_details_subscription_details_id = '".$subscription_details['subscription_type']."'";

        $this->db_conn->query($insert_sub_purchase);

        $subscription_details['service_id'] = $this->db_conn->get_last_insert_id();
        $this->save_payment_details($subscription_details);


    }
    private function save_payment_details($payment_details){

        $select_top_id = "SELECT top_id FROM " . DB_NAME . ".type_of_payment WHERE top_name = '".$payment_details['type_of_payment']."'";
        $res_top_id = $this->db_conn->query($select_top_id);
        $top_id = $this->db_conn->fetch_data($res_top_id);

        $insert_payment_details = "INSERT INTO " . DB_NAME . ".payment_details 
                                SET 
                                  payment_type      = '".$payment_details['type_of_payment']."',
                                  payment_date      = ".date("Y-m-d").",
                                  payment_amount    = '".$payment_details['payment_amt']."',
                                  is_approved       = 0";
        $this->db_conn->query($insert_payment_details);

        $payment_details_insert_id = $this->db_conn->get_last_insert_id();

        $insert_payment_apportioning = "INSERT INTO  " . DB_NAME . ".payment_apportioning 
                                      SET 
                                        payment_apportioning_service_id     = '".$payment_details['service_id']."',
                                        payment_details_payment_details_id  = ".$payment_details_insert_id.",
                                        type_of_payment_top_id              = '".$top_id."',
                                        payment_source                      = '".$payment_details['payment_for']."',
                                        user_details_userid                = '".$payment_details['userid']."'";

        $this->db_conn->query($insert_payment_apportioning);

        switch ($payment_details['type_of_payment']) {
            case "cash" :
                $insert_cash = "INSERT INTO " . DB_NAME . ".cash_details
                                  SET 
                                    cash_amt = '" . $payment_details['payment_amt'] . "',
                                    payment_details_payment_details_id = " . $payment_details_insert_id;
                $this->db_conn->query($insert_cash);
                break;
            case "credit_card" :
                $card_details = json_decode($payment_details['card_details'],true);
                $save_card_id = null;
                if ($card_details['is_card_saved'] == false) {
                    $save_card_details = "INSERT INTO " . DB_NAME . ".saved_card_details 
                                            SET 
                                              scd_card_number       = " . $card_details['card_number'] . ",
                                              scd_card_owner_name   = '" . $card_details['card_owner_name'] . "',
                                              scd_card_exp_month    = " . $card_details['card_exp_month'] . ",
                                              scd_card_exp_year     = " . $card_details['card_exp_year'] . ",
                                              scd_zipcode           = '" . $card_details['card_zipcode'] . "',
                                              scd_delete_flag       = 0,
                                              user_details_userid   = '" . $payment_details['userid'] . "'";
                    $this->db_conn->query($save_card_details);
                    $save_card_id = $this->db_conn->get_last_insert_id();
                } else {
                    $get_saved_card_id = "SELECT scd_id FROM " . DB_NAME . ".saved_card_details 
                                            WHERE scd_card_number=" . $card_details['card_number'] . " AND  
                                            user_details_userid = '" . $payment_details['userid'] . "'";
                    $res_scd_id = $this->db_conn->query($get_saved_card_id);
                    $row_scd_id = $this->db_conn->fetch_data($res_scd_id);
                    $save_card_id = $row_scd_id['scd_id'];
                }
                $insert_credit = "INSERT INTO " . DB_NAME . ".card_details 
                                    SET 
                                      card_amt = '" . $payment_details['payment_amt'] . "',
                                      saved_card_details_scd_id = " . $save_card_id . ",
                                      payment_details_payment_details_id = " . $payment_details_insert_id;
                $this->db_conn->query($insert_credit);
                break;
            case "cheque" :
                $cheque_details = json_decode($payment_details['cheque_details'],true);
                $insert_cheque = "INSERT INTO " . DB_NAME . ".cheque_details 
                                    SET 
                                      cheque_amt    = " . $payment_details['payment_amt'] . ",
                                      payment_details_payment_details_id = " . $payment_details_insert_id . ",
                                      bank_name     = '" . $cheque_details['bank_name'] . "',
                                      cheque_date   = '" . date("Y-m-d", time($cheque_details['cheque_date'])) . "',
                                      cheque_routing_number = '" . $cheque_details['cheque_routing_number'] . "',
                                      cheque_account_number = '" . $cheque_details['cheque_account_number'] . "',
                                      cheque_number         = '" . $cheque_details['cheque_number'] . "',
                                      cheque_holder_name  = '" . $cheque_details['cheque_holder_name'] . "'";
                $this->db_conn->query($insert_cheque);
                break;
        }
    }
    public function save_appointment_details($appointment_details){

        $get_dog_owner_id = "SELECT do_details_id FROM " . DB_NAME . ".dog_owner_details WHERE user_details_userid = ".$appointment_details['dog_owner_userid'];
        $res_dog_owner = $this->db_conn->query($get_dog_owner_id);

        $dog_owner_id = $this->db_conn->fetch_data($res_dog_owner);

        $get_pet_sitter_id = "SELECT ps_details_id FROM " . DB_NAME . ".pet_sitter_details WHERE user_details_userid = ".$appointment_details['pet_sitter_userid'];
        $res_pet_sitter = $this->db_conn->query($get_pet_sitter_id);

        $pet_sitter_id = $this->db_conn->fetch_data($res_pet_sitter);

        $appointment_date = date("Y-m-d 00:00:00", time($appointment_details['appointment_date']));

        $insert_appointment = "INSERT INTO " . DB_NAME . ".appointment_booking_details 
                                SET
                                    appointment_date = '".$appointment_date."',
                                    dog_owner_details_do_details_id = ".$dog_owner_id.",
                                    pet_sitter_details_ps_details_id = ".$pet_sitter_id;
        $this->db_conn->query($insert_appointment);

        $appointment_details['service_id'] = $this->db_conn->get_last_insert_id();

        $this->save_payment_details($appointment_details);
    }
}