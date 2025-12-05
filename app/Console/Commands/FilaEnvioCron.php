<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FilaEnvioCron as FilaEnvio;
use App\Models\ConfigGeral;
use App\Models\EmailConfig;
use App\Utils\WhatsAppUtil;
use App\Utils\EmailUtil;
use Mail;

class FilaEnvioCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fila-envio:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fila de envio';

    protected $whatsAppUtil;
    protected $emailUtil;

    public function __construct(WhatsAppUtil $whatsAppUtil, EmailUtil $emailUtil){
        parent::__construct();
        $this->whatsAppUtil = $whatsAppUtil;
        $this->emailUtil = $emailUtil;
    }

    public function handle()
    {
        $data = FilaEnvio::where(function($q) {
            $q->where('status', 'pendente')->orWhere('status', 'erro');
        })
        ->whereDate('agendar_para', date('Y-m-d'))
        ->get();

        foreach($data as $item){

            $config = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

            if($item->cliente){
                try{
                    if($item->enviar_whatsapp){
                        $this->__enviarWhatsApp($item->cliente, $item->texto, $config);
                    }

                    if($item->enviar_email){
                        $this->__enviarEmail($item->cliente, $item->texto);
                    }
                    $item->enviado_em = date('Y-m-d H:i:s');
                    $item->status = 'enviado';
                    $item->save();
                }catch(\Exception $e){
                    $item->erro = $e->getMessage();
                    $item->status = 'erro';
                    $item->save();
                }
            }

        }
    }

    private function __enviarEmail($cliente, $texto){

        if(!$cliente->email){
            return null;
        }

        $emailConfig = EmailConfig::where('empresa_id', $cliente->empresa_id)
        ->where('status', 1)
        ->first();

        if($emailConfig != null){

            $result = $this->emailUtil->enviaEmailPHPMailer($cliente->email, 'Como foi sua experiência', $texto, $emailConfig);
        }else{
            Mail::send('mail.envio_cron', ['texto' => $texto], function($m) use ($cliente){

                $nomeEmail = env('MAIL_FROM_NAME');
                $m->from(env('MAIL_USERNAME'), $nomeEmail);
                $m->subject('Como foi sua experiência');
                $m->to($cliente->email);
            });
        }
    }

    private function __enviarWhatsApp($cliente, $texto, $config){

        if(!$cliente->telefone){
            return null;
        }

        $token = '';
        if($config && strlen($config->token_whatsapp) > 10){
            $token = $config->token_whatsapp;
        }

        if($token != ''){
            $numero = preg_replace('/[^0-9]/', '', $cliente->telefone);
            $retorno = $this->whatsAppUtil->sendMessageWithToken('55'.$numero, $texto, $cliente->empresa_id, $token);
        }
    }

}
