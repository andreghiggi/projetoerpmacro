<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashBackCliente;
use App\Models\Empresa;
use App\Models\CashBackConfig;
use App\Models\ConfigGeral;
use App\Utils\WhatsAppUtil;

class CashBackCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cash-back:cron';
    protected $util;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eventos do cashback';

    public function __construct(WhatsAppUtil $util)
    {
        parent::__construct();
        $this->util = $util;

    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $current = "John Smith";
        // file_put_contents('aaaa.txt', $current);

        $this->enviaMensagensDia();
        $this->enviaMensagens5Dias();
    }

    private function enviaMensagensDia(){

        $empresas = Empresa::all();
        $date = date('Y-m-d', strtotime('+1 days'));
        foreach($empresas as $e){

            $data = CashBackCliente::whereDate('data_expiracao', $date)
            ->where('empresa_id', $e->id)
            ->get();
            $config = CashBackConfig::where('empresa_id', $e->id)->first();

            $configGeral = ConfigGeral::where('empresa_id', $e->id)->first();
            $token = '';
            if($configGeral && strlen($configGeral->token_whatsapp) > 10){
                $token = $configGeral->token_whatsapp;
            }
            if($config){
                foreach($data as $cashback){
                    if($cashback->status_mensagem_1_dia == 0 && $cashback->status == 1){
                        $numero = preg_replace('/[^0-9]/', '', $cashback->cliente->telefone);
                        $texto = $config->mensagem_automatica_1_dia;

                        $nomeCliente = $cashback->cliente->razao_social;
                        if($cashback->cliente->nome_fantasia != ''){
                            $nomeCliente = $cashback->cliente->nome_fantasia;
                        }
                        // echo $cashback->cliente;

                        $texto = str_replace("{credito}", moeda($cashback->valor_credito), $texto);
                        $texto = str_replace("{expiracao}", __date($cashback->data_expiracao, 0), $texto);
                        $texto = str_replace("{nome}", $nomeCliente, $texto);
                        if($numero != ''){
                            if($token == ''){
                                $retorno = $this->util->sendMessage('55'.$numero, $texto, $e->id);
                            }else{
                                $retorno = $this->util->sendMessageWithToken('55'.$numero, $texto, $e->id, $token);
                            }
                            $retorno = json_decode($retorno);
                            if($retorno->success == true){
                                if($retorno){
                                    $cashback->status_mensagem_1_dia = 1;
                                    $cashback->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function enviaMensagens5Dias(){

        $empresas = Empresa::all();
        $date = date('Y-m-d', strtotime('+5 days'));
        foreach($empresas as $e){

            $data = CashBackCliente::whereDate('data_expiracao', $date)
            ->where('empresa_id', $e->id)
            ->get();
            $config = CashBackConfig::where('empresa_id', $e->id)->first();

            $configGeral = ConfigGeral::where('empresa_id', $e->id)->first();
            $token = '';
            if($configGeral && strlen($configGeral->token_whatsapp) > 10){
                $token = $configGeral->token_whatsapp;
            }

            if($config){
                foreach($data as $cashback){
                    if($cashback->status_mensagem_5_dias == 0 && $cashback->status == 1){
                        $numero = preg_replace('/[^0-9]/', '', $cashback->cliente->telefone);
                        $texto = $config->mensagem_automatica_5_dias;

                        $nomeCliente = $cashback->cliente->razao_social;
                        if($cashback->cliente->nome_fantasia != ''){
                            $nomeCliente = $cashback->cliente->nome_fantasia;
                        }
                        // echo $cashback->cliente;

                        $texto = str_replace("{credito}", moeda($cashback->valor_credito), $texto);
                        $texto = str_replace("{expiracao}", __date($cashback->data_expiracao, 0), $texto);
                        $texto = str_replace("{nome}", $nomeCliente, $texto);
                        if($numero != ''){
                            // $retorno = $this->util->sendMessage('55'.$numero, $texto, $e->id);

                            if($token == ''){
                                $retorno = $this->util->sendMessage('55'.$numero, $texto, $e->id);
                            }else{
                                $retorno = $this->util->sendMessageWithToken('55'.$numero, $texto, $e->id, $token);
                            }

                            $retorno = json_decode($retorno);
                            if($retorno->success == true){
                                if($retorno){
                                    $cashback->status_mensagem_5_dias = 1;
                                    $cashback->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
