<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 9/21/2018
 * Time: 2:42 PM
 */



class class_payment
{
    private $db_conn;
    function class_payment(){
        $this->db_conn =
            new db_connection(DB_SERVER_NAME,DB_USER_NAME,DB_PASSWORD,DB_NAME);
    }

    public function get_subscription_details(){
        $select_subscription = "SELECT subscription_id,subscription_name,details,package_amt 
                                    FROM subscription_details";
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

        $insert_sub_purchase = "INSERT INTO subscription_purchase_details
                                    SET 
                                        subscription_start_date             = ".$subscription_start_date.",
                                        subscription_end_date               = ".$subscription_end_date.",
                                        dog_owner_details_do_details_id     = '".$subscription_details['dog_owner_id']."',
                                        subscription_details_subscription_details_id = '".$subscription_details['subscription_type']."'";

        $this->db_conn->query($insert_sub_purchase);

        $subscription_details_purchase_id = $this->db_conn->get_last_insert_id();

        $select_top_id = "SELECT top_id FROM type_of_payment WHERE top_name = '".$subscription_details['type_of_payment']."'";
        $res_top_id = $this->db_conn->query($select_top_id);
        $top_id = $this->db_conn->fetch_data($res_top_id);

        $insert_payment_details = "INSERT INTO payment_details 
                                    SET 
                                      payment_type      = '".$subscription_details['type_of_payment']."',
                                      payment_date      = ".date("Y-m-d").",
                                      payment_amount    = '".$subscription_details['payment_amt']."',
                                      is_approved       = 0";
        $this->db_conn->query($insert_payment_details);

        $payment_details_insert_id = $this->db_conn->get_last_insert_id();

        $insert_payment_apportioning = "INSERT INTO  payment_apportioning 
                                          SET 
                                            payment_apportioning_service_id     = '".$subscription_details_purchase_id."',
                                            payment_details_payment_details_id  = ".$payment_details_insert_id.",
                                            type_of_payment_top_id              = '".$top_id."',
                                            payment_source                      = '".$subscription_details['paymnent_for']."',
                                            user_details_userid                = '".$subscription_details['userid']."'";

        $this->db_conn->query($insert_payment_apportioning);

        switch ($subscription_details['type_of_payment']){
            case "cash" :
                    $insert_cash = "INSERT INTO cash_details
                                      SET 
                                        cash_amt = '".$subscription_details['payment_amt']."',
                                        payment_details_payment_details_id = ".$payment_details_insert_id;
                    $this->db_conn->query($insert_cash);
                break;
            case "credit_card" :
                    $card_details = $subscription_details['card_details'];
                    $save_card_id = null;
                    if($card_details['is_card_saved'] == false){
                        $save_card_details = "INSERT INTO saved_card_details 
                                                SET 
                                                  scd_card_number       = ".$card_details['card_number'].",
                                                  scd_card_owner_name   = '".$card_details['card_owner_name']."',
                                                  scd_card_exp_month    = ".$card_details['card_exp_month'].",
                                                  scd_card_exp_year     = ".$card_details['card_exp_year'].",
                                                  scd_zipcode           = '".$card_details['card_zipcode']."',
                                                  scd_delete_flag       = 0,
                                                  user_details_userid   = '".$subscription_details['userid']."'";
                        $this->db_conn->query($save_card_details);
                        $save_card_id = $this->db_conn->get_last_insert_id();
                    }
                    else{
                        $get_saved_card_id = "SELECT scd_id FROM saved_card_details 
                                                WHERE scd_card_number=".$card_details['card_number'].", 
                                                user_details_userid = '".$subscription_details['userid']."'";
                        $res_scd_id = $this->db_conn->query($get_saved_card_id);
                        $row_scd_id = $this->db_conn->fetch_data($res_scd_id);
                        $save_card_id = $row_scd_id['scd_id'];
                    }
                    $insert_credit = "INSERT INTO card_details 
                                        SET 
                                          card_amt = '".$subscription_details['payment_amt']."',
                                          saved_card_details_scd_id = ".$save_card_id.",
                                          payment_details_payment_details_id = ".$payment_details_insert_id;
                    $this->db_conn->query($insert_credit);
                break;
            case "cheque" :
                    $cheque_details = $subscription_details['cheque_details'];
                    $insert_cheque = "INSERT INTO cheque_details 
                                        SET 
                                          cheque_amt    = ".$subscription_details['payment_amt'].",
                                          payment_details_payment_details_id = ".$payment_details_insert_id.",
                                          bank_name     = '".$cheque_details['bank_name']."',
                                          cheque_date   = '".date("Y-m-d",time($cheque_details['cheque_date']))."',
                                          cheque_routing_number = '".$cheque_details['cheque_routing_number']."',
                                          cheque_account_number = '".$cheque_details['cheque_account_number']."',
                                          cheque_number         = '".$cheque_details['cheque_number']."',
                                          cheque_holder_name  = '".$cheque_details['cheque_holder_name']."'";
                    $this->db_conn->query($insert_cheque);
                break;
        }

        function update_subscription_details($subscription_details){

        }

    }

}