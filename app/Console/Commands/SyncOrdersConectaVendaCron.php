<?php

namespace App\Console\Commands;

use App\Models\ConectaVendaConfig;
use App\Models\ConectaVendaPedido;
use App\Models\Empresa;
use App\Utils\ConectaVendaSincronizador;
use App\Utils\HttpUtil;
use App\Utils\EstoqueUtil;
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
                    /** @var ConectaVendaPedido|null $pedido */ 
                    $pedido               = ConectaVendaPedido::where('conecta_pedido_id', $conecta_pedido->id)->first();
                    

                    if(!$pedido){
                        $pedido = $this->util->createOrder($conecta_pedido, $config);
                    }

                    $pedido_situacao_atual = $pedido->situacao;

                    $situacoes_skip_update = [
                        "Finalizado"
                    ];

                    if( in_array( $pedido->situacao, $situacoes_skip_update ) ) {
                        continue;
                    }

                    $pedido->situacao = $conecta_pedido->situacao;
                    $pedido->save();

                    $situacoes_devolver_estoque = [
                        'em aberto',
                        'pago',
                    ];

                    $pedido_situacao_nova = strtolower($conecta_pedido->situacao);
                    $devolver_estoque     = $pedido_situacao_nova == 'cancelado' && in_array( $pedido_situacao_atual, $situacoes_devolver_estoque );

                    if( $devolver_estoque ){

                        $estoque_util = new EstoqueUtil();

                        foreach($pedido->produtos() as $produto) {
                            $estoque_util->reduzEstoque( $produto->produto_id, $produto->qtde, $produto->variacao_id );
                        }
                    }
                    // }else {
                        
                    // }
                }
            }
        }
    }
}
