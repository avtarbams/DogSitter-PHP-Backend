<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 9/18/2018
 * Time: 6:46 PM
 */

require_once (__DIR__."/../config.php");
require_once (DB_CONNECTION_FILE_PATH."/db_connection.php");



class class_common
{

    private $db_connection;

    function class_common()
    {
        $this->db_connection =
            new db_connection(DB_SERVER_NAME, DB_USER_NAME, DB_PASSWORD, DB_NAME);
    }

    /*
     * Using google api to get Latitude and Longitude from address provided
     *
     * @param $address
     *
     * @return Array with following params : status=1 if success else 0, latitude,longitude, full_address.
     */
    public static   function getLatLong($address)
    {
        $address = str_replace(" ", "+", $address);

            $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=".$address."&key=AIzaSyDzb_MEOCtL57im8FKPkyOIHgGznM25gvs";

       // echo $response = file_get_contents($url);

      //  $json = json_decode($response, TRUE); //generate array object from the response from the web
        //if ($json['status'] == "OK") {
            $return_data['status'] = SUCCESS;
            $return_data["msg"] = SUCCESS_MSG;
            $return_data['lat'] = 40.737061;//$json['results'][0]['geometry']['location']['lat'];
            $return_data['long'] = -74.072624;//['results'][0]['geometry']['location']['lng'];
            $return_data['full_address'] = $address;//$json['results']['formatted_address'];
        /*} else {
            $return_data['status'] = FAILED;
            $return_data["msg"] = ERROR_MSG;
        }*/
        return $return_data;
    }

    public function getRoles()
    {
        $select_roles = "SELECT role_id,role_name FROM " . DB_NAME . ".roles";
        $res_roles = $this->db_connection->query($select_roles);
        $return_roles = [];
        if ($this->db_connection->num_of_rows($res_roles)) {
            $result_roles = $this->db_connection->fetch_data($res_roles);

            foreach ($result_roles as $row_key => $row_value) {
                $role_details = [];
                foreach ($row_value as $key => $value) {
                    $role_details[$key] = $value;
                }
                $return_roles[$row_key] = $role_details;
            }
            $return_data['status'] = SUCCESS;
            $return_data["msg"] = SUCCESS_MSG;
            $return_data['data'] = $return_roles;
        }
        else{
            $return_data['status'] = FAILED;
            $return_data["msg"] = ERROR_MSG;
        }

        return $return_roles;
    }

    public function getPhotoUploads($USER_DETAILS)
    {
        $select_photos = "SELECT photo_upload_url,pu_id FROM " . DB_NAME . ".photo_upload WHERE user_details_userid='".$USER_DETAILS['user_id']."'";
        $res_photos = $this->db_connection->query($select_photos);
        $return_roles = [];
        if ($this->db_connection->num_of_rows($res_photos)) {
            $result_photos = $this->db_connection->fetch_data($res_photos);
            foreach ($result_photos as $row_key => $row_value) {
                $role_details = [];
                foreach ($row_value as $key => $value) {
                    $role_details[$key] = $value;
                }
                $return_roles[$row_key] = $role_details;
            }
            $return_data['status'] = SUCCESS;
            $return_data["msg"] = SUCCESS_MSG;
            $return_data['data'] = $return_roles;
        }
        else{
            $return_data['status'] = FAILED;
            $return_data["msg"] = ERROR_MSG;
        }
        return $return_data;
    }

    function save_feedback_comment($feedback_details){

        $get_user_details = "SELECT 
                                dog_owner_details_do_details_id AS dog_owner_id, pet_sitter_details_ps_details_id AS pet_sitter_id
                               FROM " . DB_NAME . ".appointment_booking_details 
                               WHERE abd_id = ".$feedback_details['appointment_id'];

        $res_user_details = $this->db_connection->query($get_user_details);
        if($this->db_connection->num_of_rows($res_user_details)>0) {


            $row_det = $this->db_connection->fetch_data($res_user_details);
            $insert_feedback = "INSERT INTO " . DB_NAME . ".feedback_details SET 
                                    ratings                             = " . $feedback_details['ratings'] . ",
                                    feedback_comments                   = '" . $feedback_details['feedback_comments'] . "',
                                    dog_owner_details_do_details_id     = " . $row_det['dog_owner_id'] . ",
                                    pet_sitter_details_ps_details_id    = " . $row_det['pet_sitter_id'] . ",
                                    appointment_booking_details_abd_id  = " . $feedback_details['appointment_id'];

            $this->db_connection->query($insert_feedback);

            $return_data['status'] = SUCCESS;
            $return_data['msg'] = "Feedback successfully submitted.";
            return $return_data;

        }
        else{
            $return_data['status'] = FAILED;
            $return_data['msg'] = "Appointment details not found for given ID.";
            return $return_data;
        }

    }



    function get_user_feedback($USER_DETAILS,$limit=0){

        $get_user_type = "SELECT role_role_id AS role_id 
                            FROM " . DB_NAME . ".user_type WHERE user_details_userid = ".$USER_DETAILS['user_id'];

        $res_role = $this->db_connection->query($get_user_type);

        $role = $this->db_connection->fetch_data($res_role);

        $role_details = "";
        if ($role['role_id'] == 1){
            $role_details = " WHERE fd.dog_owner_details_do_details_id = ".$USER_DETAILS['user_id']." ORDER BY appointment_date DESC";
        }else if($role['role_id'] == 2) {
            $role_details = " WHERE fd.pet_sitter_details_ps_details_id = ".$USER_DETAILS['user_id']." ORDER BY appointment_date DESC";
        }
        if($limit!=0){
            $role_details .= "LIMIT =".$limit;
        }

        $get_feedback_details = "SELECT 
                                fd.ratings,fd.feedback_comments,fd.dog_owner_details_do_details_id AS dog_owner_id, 
                                fd.pet_sitter_details_ps_details_id AS pet_sitter_id, appointment_date
                               FROM " . DB_NAME . ".feedback_details fd JOIN " . DB_NAME . ".appointment_booking_details abd 
                               ON fd.appointment_booking_details_abd_id = abd.abd_id 
                                ".$role_details;


        $res_feedback_details = $this->db_connection->query($get_feedback_details);
        if($this->db_connection->num_of_rows($res_feedback_details)>0) {
            $feedback_details = $this->db_connection->fetch_data($res_feedback_details);
            return $feedback_details;
        }
    }

}