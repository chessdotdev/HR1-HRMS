<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//required files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Create an instance; passing `true` enables exceptions
class MailService {

    public static function send($to, $subject, $body, $silent = false){

    $mail = new PHPMailer(true);
        try {
        //Server settings
        $mail->isSMTP();                              //Send using SMTP
        $mail->Host       = $_ENV['SMTP_HOST'];       //Set the SMTP server to send through
        $mail->SMTPAuth   = true;             //Enable SMTP authentication
        $mail->Username   = $_ENV['SMTP_USERNAME'];   //SMTP write your email
        $mail->Password   = $_ENV['SMTP_PASSWORD'];      //SMTP password
        $mail->SMTPSecure = 'ssl';            //Enable implicit SSL encryption
        $mail->Port       = $_ENV['SMTP_PORT'];                                    

        //Recipients
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']); // Sender Email and name
        $mail->addAddress($to);     //Add a recipient email  
        $mail->addReplyTo($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']); // reply to sender email

        //Content
        $mail->isHTML(true);               //Set email format to HTML
        $mail->Subject = $subject;   // email subject headings
        $mail->Body    = $body; //email message

        // Success sent message alert
        $mail->send();
        
        if($silent) {
            return true;
        }
        
        echo
        " 
        <script> 
            alert('Message was sent successfully!');
            document.location.href = 'test-email.php';
        </script>
        ";
        } catch (\Throwable $e) {
             // Any other error (missing ENV, etc.)
             error_log('MailService Error: ' . $e->getMessage());
             if ($silent) return false;
             echo "<script>alert('Unexpected error. Please try again later.');</script>";
             return false;
         }
        
    }
}
?>