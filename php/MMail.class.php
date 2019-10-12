<?php
date_default_timezone_set('Asia/Shanghai');

use PHPMailer\PHPMailer\PHPMailer;

include 'PHPMailer/PHPMailer.php';
include 'PHPMailer/SMTP.php';
include 'PHPMailer/Exception.php';

class MMail {
    private $mail;

    public function __construct($tos, $ccs=[], $subject='', $body='', $attatch=[], $isHtml=false){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->CharSet = 'utf-8';
        $mail->SetLanguage('zh_cn');
        $mail->Host = 'xxxxxxxxxxxxx';
        $mail->Port = 25;
        $mail->FromName = 'xxxxxxxxxxxxxxxxxxx';
        $mail->From = 'xxxxxxxxxxxxxxxxxxx';
        $mail->Username = 'xxxxxxxxxxxxxxxxxxx';
        $mail->Password = 'xxxxxxx';

        $mail->Subject = $subject;
        $mail->Body = $body;

        if(!$tos){
            throw new Exception('no send user list');
        }

        foreach($tos as $to){
            $mail->addAddress($to);
        }

        if($ccs){
            foreach($ccs as $cc){
                $mail->addCC($cc);
            }
        }

        if($attatch){
            $mail->addAttachment($attatch['path'], $attatch['name']);
        }

        $this->mail = $mail;
    }

    public function send(){
        $result = ['code'=>0, 'msg'=> ''];
        if (!$this->mail->send()) {
            $result['code'] = 1;
            $result['msg'] = 'Mailer Error: ' . $this->mail->ErrorInfo;
        } else {
            $result['msg'] = 'Message sent';
        }

        return $result;
    }
}
