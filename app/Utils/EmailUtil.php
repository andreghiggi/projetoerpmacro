<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Empresa;
use App\Models\PlanoConta;
use App\Models\EmailConfig;
use App\Models\EscritorioContabil;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Mail;

class EmailUtil {

	public function enviaEmailPHPMailer($destinatario, $subject, $body, $emailConfig, $fileDir = null){
		$mail = new PHPMailer(true);

		try {
			if($emailConfig->smtp_debug){
				$mail->SMTPDebug = SMTP::DEBUG_SERVER;   
			}                   
			$mail->isSMTP();                                            
			$mail->Host = $emailConfig->host;                     
			$mail->SMTPAuth = $emailConfig->smtp_auth;                                   
			$mail->Username = $emailConfig->email;                     
			$mail->Password = $emailConfig->senha;                               
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
			$mail->Port = $emailConfig->porta; 

			$mail->setFrom($emailConfig->email, $emailConfig->nome); 
			$mail->addAddress($destinatario); 

			if($fileDir){
				if(is_array($fileDir)){
					foreach($fileDir as $f){
						$mail->addAttachment($f); 
					}
				}else{
					$mail->addAttachment($fileDir); 
				}
			}

			$mail->isHTML(true);
			$mail->CharSet = 'UTF-8';

			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->send();
			return [
				'sucesso' => true
			];
		} catch (Exception $e) {
			return [
				'erro' => $mail->ErrorInfo
			];
		}
	}

	public function enviarXmlContador($empresa_id, $fileDir, $documento, $chave){
		$escritorio = EscritorioContabil::where('empresa_id', $empresa_id)
		->where('envio_xml_automatico', 1)->first();
		if($escritorio == null) return 0;

		$emailConfig = EmailConfig::where('empresa_id', $empresa_id)
		->where('status', 1)
		->first();

		$destinatario = $escritorio->email;
		$assunto = "Envio de XML";
		$body = "$documento chave $chave";

		try{
			if($emailConfig != null){

				$result = $this->enviaEmailPHPMailer($destinatario, $assunto, $body, $emailConfig, $fileDir);
			}else{
				Mail::send('mail.envio_xml', ['body' => $body], function($m) use ($destinatario, $assunto){

					$nomeEmail = env('MAIL_FROM_NAME');
					$m->from(env('MAIL_USERNAME'), $nomeEmail);
					$m->subject($assunto);
					$m->to($destinatario);

				});
			}
			return 1;
		}catch(\Exception $e){
			return 0;
		}
	}

	public function enviarXmlContadorZip($empresa_id, $fileDir, $documento, $body){
		$escritorio = EscritorioContabil::where('empresa_id', $empresa_id)->first();
		if($escritorio == null) return 0;

		$emailConfig = EmailConfig::where('empresa_id', $empresa_id)
		->where('status', 1)
		->first();

		$destinatario = $escritorio->email;
		$assunto = "Envio de XML $documento";

		try{
			if($emailConfig != null){

				$result = $this->enviaEmailPHPMailer($destinatario, $assunto, $body, $emailConfig, $fileDir);
			}else{
				Mail::send('mail.envio_xml', ['body' => $body], function($m) use ($destinatario, $assunto, $fileDir){

					$nomeEmail = env('MAIL_FROM_NAME');
					$m->from(env('MAIL_USERNAME'), $nomeEmail);
					$m->subject($assunto);
					$m->attach($fileDir);
					$m->to($destinatario);
				});
			}
			return 1;
		}catch(\Exception $e){
			echo $e->getMessage();

			return 0;
		}
	}

}