<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 9/15/2018
 * Time: 1:09 AM
 */

require_once (__DIR__."/../config.php");
require_once (CLASS_FILE_PATH."/class_common.php");
require_once (DB_CONNECTION_FILE_PATH."/db_connection.php");

class class_login{
    private $db_conn;
    function class_login(){
        $this->db_conn =
            new db_connection(DB_SERVER_NAME,DB_USER_NAME,DB_PASSWORD,DB_NAME);
    }
    function login_check($emailUserName,$password){
        $password_encode = md5($password);

        $select_login = "SELECT userid,user_name,is_active FROM ".DB_NAME.".user_details 
                            WHERE email_id = '".$emailUserName."' AND password = '".$password_encode."'";
        $select_login_res = $this->db_conn->query($select_login);

        if($this->db_conn->num_of_rows($select_login_res)> 0 ) {
            $response = $this->db_conn->fetch_data($select_login_res);

            if ($response['is_active'] == 1) {

                $return_data['status'] = SUCCESS_MSG;
                $return_data['msg'] = "";
                $return_data['data'] = $response;
            }
            else{
                $return_data['status'] = FAILED;
                $return_data['msg'] = "User is inactive.Try to update user.";
                $return_data['data'] = "";
            }
        }
        else{
            $return_data['status'] = FAILED;
            $return_data['msg'] = "Invalid Username/Password.";
            $return_data['data'] = "";
        }

        return json_encode($return_data);
    }

    function create_user_sign_up($sign_up_details){

        $user_details_insert = "INSERT INTO ".DB_NAME.".user_details SET 
                                    user_first_name             = '".$sign_up_details['user_first_name']."',
                                    user_last_name              = '".$sign_up_details['user_last_name']."',
                                    email_id                    = '".$sign_up_details['email_id']."',
                                    password                    = '".md5($sign_up_details['password'])."',
                                    is_active                   = 1,
                                    last_changed_password_date  = '".date('Y-m-d H:i:s')."'";

        $this->db_conn->query($user_details_insert);

        $user_id = $this->db_conn->get_last_insert_id();


        $user_type_insert = "INSERT INTO ".DB_NAME.".user_type SET 
                                user_details_userid = '".$user_id."',
                                    roles_role_id       = '".$sign_up_details['role_id']."'";
        $this->db_conn->query($user_type_insert);

        $address = $sign_up_details['street']." ".$sign_up_details['city']." ".$sign_up_details['state'];
        $google_api_fetch = class_common::getLatLong($address);

        $insert_general_details  = "INSERT INTO ".DB_NAME.".general_details SET
                                        landline_number         = '".$sign_up_details['phone_number']."',
                                        mobile_number           = '".$sign_up_details['mobile_number']."',
                                        street                  = '".addcslashes($sign_up_details['street'])."',
                                        apt_num                 = '".$sign_up_details['apt_num']."',
                                        city                    = '".$sign_up_details['city']."',
                                        state                   = '".$sign_up_details['state']."',
                                        zipcode                 = '".$sign_up_details['zipcode']."',
                                        full_address            = '".addcslashes($google_api_fetch['full_address'])."',
                                        latitude                = '".$google_api_fetch['lat']."',
                                        longitude               = '".$google_api_fetch['long']."',
                                        user_details_userid    = '".$user_id."',
                                        profile_pic_url         = '".$sign_up_details['profile_pic_url']."'";
        $this->db_conn->query($insert_general_details);

        #$select_role = "SELECT role_name FROM roles WHERE role_id='".$sign_up_details['role_id']."'";
        #$res_role = $this->db_conn->query($select_role);
        #$result_data = $this->db_conn->fetch_data($res_role);

        if($sign_up_details['role_id'] == 1){

            $insert_dog_owner = "INSERT INTO ".DB_NAME.".dog_owner_details SET
                                    user_details_userid = '".$user_id."'";

            $this->db_conn->query($insert_dog_owner);
        }
        elseif($sign_up_details['role_id'] == 2){
            $insert_pet_sitter = "INSERT INTO ".DB_NAME.".pet_sitter_details SET
                                    user_details_userid = '".$user_id."'";

            $this->db_conn->query($insert_pet_sitter);
        }

        $response['status'] = SUCCESS;
        $response['msg'] = SUCCESS_MSG;

        return $response;

    }



    function update_password($USER_DETAILS){
        $user_password_update = "UPDATE ".DB_NAME.".user_details SET 
                                    password                    = '".md5($USER_DETAILS['password'])."',
                                    is_active                   = 1,
                                    last_changed_password_date  = '".date('Y-m-d H:i:s')."'
                                   WHERE 
                                    email_id                    = '".$USER_DETAILS['userid']."'";

        $this->db_conn->query($user_password_update);

        $response['status'] = SUCCESS;
        $response['msg'] = SUCCESS_MSG;

        return $response;
    }

    function update_user_details($USER_DETAILS){
        $user_info_update = "UPDATE ".DB_NAME.".user_details SET 
                                    user_first_name = '".$USER_DETAILS['user_first_name']."',
                                    user_last_name  = '".$USER_DETAILS['user_last_name']."'
                                    is_active       = 1
                                   WHERE 
                                    userid          = '".$USER_DETAILS['userid']."'";

        $this->db_conn->query($user_info_update);

        $select_role = "SELECT roles_role_id FROM user_type WHERE user_details_userid='".$USER_DETAILS['userid']."'";
        $res_role = $this->db_conn->query($select_role);
        $result_data = $this->db_conn->fetch_data($res_role);

        if($result_data['roles_role_id'] != $USER_DETAILS['role_id']) {
            $user_type_update = "UPDATE " . DB_NAME . ".user_type SET 
                                        roles_role_id       = '" . $USER_DETAILS['role_id'] . "'
                                       WHERE
                                        user_details_userid = '" . $USER_DETAILS['userid'] . "'";
            $this->db_conn->query($user_type_update);

            if($USER_DETAILS['role_id'] == 1){

                $insert_dog_owner = "INSERT INTO ".DB_NAME.".dog_owner_details SET
                                    user_details_userid = '".$USER_DETAILS['userid']."'";

                $this->db_conn->query($insert_dog_owner);

                $delete_pet_sitter = "DELETE FROM ".DB_NAME.".pet_sitter_details 
                                    user_details_userid = '".$USER_DETAILS['userid']."'";

                $this->db_conn->query($delete_pet_sitter);
            }
            elseif($USER_DETAILS['role_id'] == 2){
                $insert_pet_sitter = "INSERT INTO ".DB_NAME.".pet_sitter_details SET
                                    user_details_userid = '".$USER_DETAILS['userid']."'";

                $this->db_conn->query($insert_pet_sitter);

                $delete_dog_owner = "DELETE FROM  ".DB_NAME.".dog_owner_details 
                                    user_details_userid = '".$USER_DETAILS['userid']."'";

                $this->db_conn->query($delete_dog_owner);
            }
        }

        $address = $USER_DETAILS['street']." ".$USER_DETAILS['city']." ".$USER_DETAILS['state'];
        $google_api_fetch = class_common::getLatLong($address);

        $update_general_details  = "UPDATE ".DB_NAME.".general_details SET
                                        landline_number         = '".$USER_DETAILS['phone_number']."',
                                        mobile_number           = '".$USER_DETAILS['mobile_number']."',
                                        street                  = '".$USER_DETAILS['street']."',
                                        apt_num                 = '".$USER_DETAILS['apt_num']."',
                                        city                    = '".$USER_DETAILS['city']."',
                                        state                   = '".$USER_DETAILS['state']."',
                                        zipcode                 = '".$USER_DETAILS['zipcode']."',
                                        full_address            = '".$google_api_fetch['full_address']."',
                                        latitude                = '".$google_api_fetch['lat']."',
                                        longitude               = '".$google_api_fetch['long']."',
                                        profile_pic_url         = '".$USER_DETAILS['profile_pic_url']."'
                                       WHERE
                                        user_details_userid    = '".$USER_DETAILS['userid']."'";
        $this->db_conn->query($update_general_details);

        $response['status'] = SUCCESS;
        $response['msg'] = SUCCESS_MSG;

        return $response;

    }

    function delete_user($USER_DETAILS){
        $user_info_update = "UPDATE ".DB_NAME.".user_details SET 
                                    is_active       = 0
                                   WHERE 
                                    userid          = '".$USER_DETAILS['userid']."'";

        $this->db_conn->query($user_info_update);
    }

    function get_user_details($USER_DETAILS){
        $get_user_details = "SELECT ud.user_first_name AS first_name, ud.user_last_name AS last_name, ud.email_id , gd.street, gd.apt_num, gd.city,
                                gd.state, gd.zipcode , gd.landline_number, gd.mobile_number,gd.profile_pic_url, roles.role_name
                                FROM ".DB_NAME.".user_details ud JOIN ".DB_NAME.".general_details gd 
                                ON ud.userid = gd.user_details_userid
                                JOIN user_type ut  ON ud.userid = ut.user_details_userid
                                JOIN roles ON ut.roles_role_id = roles.role_id
                                WHERE ud.userid=".$USER_DETAILS['user_id'];
        $result = $this->db_conn->query($get_user_details);

        if($this->db_conn->num_of_rows($result)>0) {
            $result_data = $this->db_conn->fetch_data($result);

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
}