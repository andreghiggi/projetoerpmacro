<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\EmailConfig;
use App\Utils\EmailUtil;

class EmailController extends Controller
{

    protected $util;
    public function __construct(EmailUtil $util){
        $this->util = $util;
    }

    public function index(){
        return view('mail.index');
    }

    public function send(Request $request){
        $texto = $request->texto;
        $assunto = $request->assunto;
        $destinatario = $request->destinatario;
        $emailConfig = EmailConfig::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->first();

        if($emailConfig != null){

            $body = view('mail.teste', compact('texto'));
            $result = $this->util->enviaEmailPHPMailer($destinatario, $assunto, $body, $emailConfig);
        }else{
            $result = Mail::send('mail.teste', ['texto' => $texto], function($m) use ($destinatario, $assunto){

                $nomeEmail = env('MAIL_FROM_NAME');
                $m->from(env('MAIL_USERNAME'), $nomeEmail);
                $m->subject($assunto);
                $m->to($destinatario);
            });
        }

        dd($result);
    }

}
