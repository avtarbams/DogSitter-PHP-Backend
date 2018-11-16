<?php
/**
 * Created by PhpStorm.
 * User: RICHIE
 * Date: 11/9/2018
 * Time: 7:42 PM
 */
require 'PHPMailer/PHPMailerAutoload.php';
require_once "config.php";

class send_mail
{
    private  $mail;
    public function send_mail($mail_to,$subject,$body){
        $this->mail = new PHPMailer;

        //$this->mail->SMTPDebug = 4;
        $this->mail->isSMTP();
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $this->mail->Host = 'smtp.gmail.com';                 // Specify main and backup server
        $this->mail->Port = 587;                                    // Set the SMTP port
        $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mail->Username = WE_LOVE_PETS_EMAIL;                // SMTP username
        $this->mail->Password = WE_LOVE_PETS_PASS;                  // SMTP password
        $this->mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

        $this->mail->From = 'vlovepets.vars@gmail.com';
        $this->mail->FromName = 'We Love Pets';
        $this->mail->AddAddress('crichiec555@gmail.com');  // Add a name is optional

        $this->mail->IsHTML(true);                                  // Set email format to HTML

        $this->mail->Subject = $subject;
        $this->mail->Body    = $body;
        $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$this->mail->Send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $this->mail->ErrorInfo;
            exit;
        }

        echo 'Message has been sent';
    }
}