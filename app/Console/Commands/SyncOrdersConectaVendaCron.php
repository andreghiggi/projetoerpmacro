<?php

namespace App\Console\Commands;

use App\Models\ConectaVendaConfig;
use App\Models\ConectaVendaPedido;
use App\Models\Empresa;
use App\Utils\ConectaVendaSincronizador;
use App\Utils\HttpUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncOrdersConectaVendaCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-orders-conecta-venda';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os pedidos do conecta venda com o ERP';
    protected ConectaVendaSincronizador $util;

    public function __construct(ConectaVendaSincronizador $util)
    {
        parent::__construct();
        $this->util = $util;

    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $empresas = Empresa::where('status', 1)->get();
        foreach ($empresas as $empresa){

            $config = ConectaVendaConfig::where('empresa_id', $empresa->id)->first();
            if($config == null){
                continue;
            }

            $data = DB::table('conecta_venda_pedidos')
                ->select('data_atualizacao_status')
                ->where('empresa_id', $empresa->id)
                ->orderBy('data_atualizacao_status', 'desc')->first();

            $last_update_date = '2000-01-01 00:00:00';
            if( !empty($data->data_atualizacao_status) ) {
                $last_update_date = $data->data_atualizacao_status;
            }

            // $last_update_date = '2025-10-14 19:02:00'; // Para testes

            $payload = [
                'chave' => $config->client_secret,
                'data'  => $last_update_date,
            ];

            $response = Http::withOptions(['verify' => false])->asJson()->post('https://api.conectavenda.com.br/pedidos/listar', $payload);

            // HttpUtil::dd($response, $payload);

            if($response->status() == 200){
                $conecta_pedidos = json_decode($response);
                foreach($conecta_pedidos->dados as $conecta_pedido){
                    $data = ConectaVendaPedido::where('conecta_pedido_id', $conecta_pedido->id)->first();
                    if(!$data){
                        $pedido = $this->util->createOrder($conecta_pedido, $config);
                    }
                    if($conecta_pedido->situacao == 'Cancelado' || $conecta_pedido->situacao == 'cancelado'){
                        $this->util->returnStock($conecta_pedido, $config);
                    }else {
                        $data = ConectaVendaPedido::where('conecta_pedido_id', $conecta_pedido->id)->first();
                        $data->situacao = $conecta_pedido->situacao;
                        $data->save();
                    }
                }
            }
        }
    }
}
