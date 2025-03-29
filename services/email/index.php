
<?php

require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($OTP, $email)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.mailersend.net';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'MS_wgOWkH@trial-351ndgwjo9nlzqx8.mlsender.net';
        $mail->Password   = 'mssp.AxwqqAe.pxkjn41x1w6gz781.iUHxSLy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('MS_wgOWkH@trial-351ndgwjo9nlzqx8.mlsender.net', 'Your App Name');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP';
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <div style="background-color: #f8f9fa; padding: 30px; border-radius: 10px;">
                    <h1 style="color: #2d3436; margin-bottom: 25px;">🔒 Your Security Code</h1>
                    
                    <div style="background-color: #ffffff; padding: 25px; border-radius: 8px; text-align: center;">
                        <p style="color: #636e72; margin-bottom: 20px;">Use this code to verify your identity:</p>
                        <div style="font-size: 32px; letter-spacing: 3px; color: #0984e3; 
                                  background-color: #f0f8ff; padding: 15px; border-radius: 5px;
                                  margin: 20px 0;">
                            '.$OTP.'
                        </div>
                        <p style="color: #636e72; font-size: 14px;">
                            This code will expire in 10 minutes.
                        </p>
                    </div>
                    
                    <div style="margin-top: 30px; color: #636e72; font-size: 12px; text-align: center;">
                        <p>If you didn\'t request this code, please ignore this email.</p>
                        <p>© '.date('Y').' Your App Name. All rights reserved.</p>
                    </div>
                </div>
            </div>';

        $mail->AltBody = "Your OTP code is: $OTP\nThis code will expire in 10 minutes.";

        $mail->send();
    } catch (Exception $e) {
        throw $e;
    }
}
