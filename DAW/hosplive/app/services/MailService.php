<?php
    require_once 'deps/PHPMailer-master/src/PHPMailer.php';
    require_once 'deps/PHPMailer-master/src/Exception.php';
    require_once 'deps/PHPMailer-master/src/SMTP.php';
    require_once 'config/config.php';

    use const Config\config_email;
    use PHPMailer\PHPMailer\PHPMailer;
    class MailService{
        //Validates email and returns error message if invalid, null otherwise
        public static function validateEmail(string $email):string|null{
            $valid_format = filter_var($email, FILTER_VALIDATE_EMAIL);
            //If the email is not in a valid format, return false
            if (!$valid_format){
                return "Invalid email format";
            }
            //Check if domain has MX and A(ipv4) records
            $domain_name = substr($email, strpos($email, '@') + 1);
            $valid_domain = checkdnsrr($domain_name, 'MX') || checkdnsrr($domain_name, 'A') || checkdnsrr($domain_name, 'AAAA');
            if (!$valid_domain)
                return "Domain does not have MX, ipv4 or ipv6 records";
            return null;
        }

        //Sends verification email to the user and updates the verification code in the database
        //Verif code duration should be in minutes
        public static function sendVerificationEmail(string $receiver, string $verif_code, int $verif_code_dur){ 
            $subject = 'Authentication verification code';
            $body    = 'This is the <h1>verification code: ' . $verif_code . '</h1> for your registration' . 
                       '<h1>Expires in: ' . $verif_code_dur . ' minutes </h1>';
            $alt_body = 'This is the verification code: ' . $verif_code . ' for your registration(' . 
                        'Expires in: ' . $verif_code_dur . ' minutes)';

            self::sendEmail($receiver, $subject, $body, $alt_body);
        }

        //Send the appointment summary as an attachment to the receiver
        public static function sendAppointmentSummaryEmail(string $receiver, int $appointment_id){
            require_once 'DocumentService.php';
            $subject = "Appointment Summary";
            $body = "The appointment summary is attached to this email";
            $alt_body = "The appointment summary is attached to this email";

            $storage_path = DocumentService::getAppointmentSummaryPath($appointment_id);
            self::sendEmail($receiver, $subject, $body, $alt_body, $storage_path . "summary.pdf");
        }

        private static function sendEmail(string $receiver, string  $subject, string $body, string $alt_body,
                                          string $attachment_file_path = null){
            $mail = new PHPMailer(true);
            //More verbose debug messages
            //$mail->SMTPDebug = 2;
            // Server settings
            $mail->isSMTP();                                      
            $mail->Host       = config_email["smtp"];              
            $mail->SMTPAuth   = true;                                 
            $mail->Username   = config_email["username"];               
            $mail->Password   = config_email["password"];               
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
            $mail->Port       = config_email["port"];                              
            
            //Setting the data about the transmitter and the receptor
            $mail->setFrom(config_email["sender"], config_email["name"]);      
            $mail->addAddress($receiver); 
            
            //Adding attachment if needed
            if ($attachment_file_path !== null)
                $mail->addAttachment($attachment_file_path);

            // Content
            $mail->isHTML(true);                                     
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $alt_body;
            
            $mail->send();
        }
    }
?>