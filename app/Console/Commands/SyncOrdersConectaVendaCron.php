<?php

namespace App\Console\Commands;

use App\Models\ConectaVendaConfig;
use App\Models\ConectaVendaPedido;
use App\Models\Empresa;
use App\Utils\ConectaVendaUtil;
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
    protected ConectaVendaUtil $util;

    public function __construct(ConectaVendaUtil $util)
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

            $payload = [
                'chave' => $config->client_secret,
                'data' => (string) ($data->data_atualizacao_status ?? today()),
            ];

            $response = Http::withOptions(['verify' => false])->asJson()->post('https://api.conectavenda.com.br/pedidos/listar', $payload);

            if($response->status() == 200){
                $orders = json_decode($response);
                foreach($orders->dados as $order){
                    $data = ConectaVendaPedido::Where('id', $order->id)->first();
                    if(!$data){
                        $pedido = $this->util->createOrder($order, $config);

                        if($pedido && isset($order->produtos)){
                            foreach ($order->produtos as $item){
                                $this->util->createItemOrder($item, $pedido->id);
                            }
                        }
                    }
                    if($order->situacao == 'Cancelado' || $order->situacao == 'cancelado'){
                        $this->util->returnStock($order, $config);
                    }else {
                        $data = ConectaVendaPedido::Where('id', $order->id)->first();
                        $data->situacao = $order->situacao;
                        $data->save();
                    }
                }
            }
        }
    }
}
