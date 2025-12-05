<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utils\WhatsAppUtil;
use App\Models\Cliente;
use App\Models\MensagemPadraoCrm;
use App\Models\ConfigGeral;
use App\Models\EmailConfig;
use App\Utils\EmailUtil;
use App\Models\FilaEnvioCron;

use Mail;

class AniversarioCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aniversario:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de mensagens aniversario clientes';

    protected $whatsAppUtil;
    protected $emailUtil;

    public function __construct(WhatsAppUtil $whatsAppUtil, EmailUtil $emailUtil){
        parent::__construct();
        $this->whatsAppUtil = $whatsAppUtil;
        $this->emailUtil = $emailUtil;
    }

    public function handle()
    {

        $data = MensagemPadraoCrm::where('status', 1)
        ->where('tipo', 'aniversario')->get();

        $hoje = \Carbon\Carbon::now();
        foreach($data as $item){
            $clientes = Cliente::where('empresa_id', $item->empresa_id)
            ->whereDay('data_nascimento', $hoje->day)
            ->whereMonth('data_nascimento', $hoje->month)
            ->get();

            $config = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

            foreach($clientes as $cliente){
                $texto = $this->__replaceText($item, $cliente);

                try{
                    if($item->enviar_email){
                        $this->__enviarEmail($cliente, $texto);
                    }

                    if($item->enviar_whatsapp){
                        $this->__enviarWhatsApp($cliente, $texto, $config);
                    }

                    FilaEnvioCron::create([
                        'empresa_id' => $cliente->empresa_id,
                        'mensagem' => $texto,
                        'erro' => '',
                        'status' => 'enviado',
                        'enviar_whatsapp' => $item->enviar_whatsapp,
                        'enviar_email' => $item->enviar_email,
                        'whatsapp' => $cliente->telefone,
                        'cliente_id' => $cliente->id,
                        'tipo' => $item->tipo,
                        'email' => $cliente->email,
                        'agendar_para' => date('Y-m-d')
                    ]);
                }catch(\Exception $e){
                    FilaEnvioCron::create([
                        'empresa_id' => $cliente->empresa_id,
                        'mensagem' => $texto,
                        'erro' => $e->getMessage(),
                        'status' => 'erro',
                        'enviar_whatsapp' => $item->enviar_whatsapp,
                        'enviar_email' => $item->enviar_email,
                        'whatsapp' => $cliente->telefone,
                        'cliente_id' => $cliente->id,
                        'tipo' => $item->tipo,
                        'email' => $cliente->email,
                        'agendar_para' => date('Y-m-d')
                    ]);
                }
            }
        }
    }

    private function __replaceText($item, $cliente){
        $texto = str_replace("[nome_cliente]", $cliente->razao_social, $item->mensagem);
        return $texto;
    }

    private function __enviarEmail($cliente, $texto){

        if(!$cliente->email){
            return null;
        }

        $emailConfig = EmailConfig::where('empresa_id', $cliente->empresa_id)
        ->where('status', 1)
        ->first();

        if($emailConfig != null){

            $result = $this->emailUtil->enviaEmailPHPMailer($cliente->email, 'Feliz aniversário', $texto, $emailConfig);
        }else{
            Mail::send('mail.envio_cron', ['texto' => $texto], function($m) use ($cliente){

                $nomeEmail = env('MAIL_FROM_NAME');
                $m->from(env('MAIL_USERNAME'), $nomeEmail);
                $m->subject('Feliz aniversário');
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
