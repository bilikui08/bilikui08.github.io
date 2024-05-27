<?php

namespace App\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Notification 
{
	const FILE_CONFIG = __DIR__ . '/../../config/config.json';
	
	private static $config;
	
	private static $mail;
	
	public static function sendMail($subject, $body, array $to = [])
	{
		self::$config = json_decode(file_get_contents(self::FILE_CONFIG), true);
		
		$config = self::$config['email'][0];
		
		try {
			self::$mail = new PHPMailer(true);
			
			self::$mail->isSMTP();                                            
			self::$mail->Host = $config['smtp_host'];
			self::$mail->SMTPAuth = true;                                   
			self::$mail->Username = $config['smtp_username'];
			self::$mail->Password = $config['smtp_password'];
			
			if ($config['smtp_ssl']) {
				self::$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
			}
			
			self::$mail->Port = $config['smtp_port'];
			
			$from = $config['from'][0];
			self::$mail->setFrom($from['email'], utf8_decode($from['alias']));
			
			$to = empty($to) ? $config['to'] : $to;
			foreach($to as $item) {
				if (isset($item['alias'])) {
					self::$mail->addAddress($item['email'], utf8_decode($item['alias']));
				} else {
					self::$mail->addAddress($item['email']);
				}
			}
			
			self::$mail->Subject = utf8_decode($subject);
			self::$mail->Body = utf8_decode($body);
			self::$mail->isHTML(true);
			
			self::$mail->send();
			
			return self::$mail;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public static function getMail()
	{
		return self::$mail;
	}
}