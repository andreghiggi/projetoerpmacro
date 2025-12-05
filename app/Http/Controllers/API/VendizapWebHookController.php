<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendiZapConfig;
use App\Models\Notificacao;
use App\Models\Cliente;
use App\Models\PedidoVendiZap;
use App\Models\Produto;
use App\Models\ItemPedidoVendiZap;
use App\Models\Localizacao;
use App\Models\ProdutoLocalizacao;

class VendizapWebHookController extends Controller
{
    public function index(Request $request){
        $dados = json_encode($request->all(), JSON_PRETTY_PRINT);
        $nomeArquivo = 'vendizapteste_' . date('Y-m-d H:i:s') . '.txt';
        // file_put_contents($nomeArquivo, $dados);
        if($request->objeto == 'pedidos' && $request->operacao == 'criado'){
            $nomeArquivo = 'vendizap_' . date('Y-m-d H:i:s') . '.txt';
            // file_put_contents($nomeArquivo, $dados);

            $config = VendiZapConfig::where('auth_id', $request->client_id)->first();
            if($config){
                $id = $request->id;
                $dataHoraOperacao = $request->dataHoraOperacao;

                $pedido = $this->criaPedido($id, $config);

                if($pedido){
                    $descricao = view('notificacao.partials.vendizap', compact('id', 'dataHoraOperacao'));
                    Notificacao::create([
                        'empresa_id' => $config->empresa_id,
                        'tabela' => 'pedido_vendi_zaps',
                        'descricao' => $descricao,
                        'descricao_curta' => 'Pedido criado #'.$request->id,
                        'referencia' => $request->id,
                        'status' => 1,
                        'por_sistema' => 1,
                        'prioridade' => 'baixa', 
                        'visualizada' => 0,
                        'titulo' => 'Novo pedido vendizap'
                    ]);
                }
            }
        }

        if($request->objeto == 'pedidos' && $request->operacao == 'cancelado'){
            $nomeArquivo = 'vendizap_' . date('Y-m-d H:i:s') . '.txt';
            // file_put_contents($nomeArquivo, $dados);

            $config = VendiZapConfig::where('auth_id', $request->client_id)->first();
            if($config){
                $id = $request->id;
                $dataHoraOperacao = $request->dataHoraOperacao;
                $descricao = view('notificacao.partials.vendizap', compact('id', 'dataHoraOperacao'));
                Notificacao::create([
                    'empresa_id' => $config->empresa_id,
                    'tabela' => 'pedido_vendi_zaps',
                    'descricao' => $descricao,
                    'descricao_curta' => 'Pedido cancelado #'.$request->id,
                    'referencia' => $request->id,
                    'status' => 1,
                    'por_sistema' => 1,
                    'prioridade' => 'baixa', 
                    'visualizada' => 0,
                    'titulo' => 'Pedido cancelado vendizap'
                ]);
            }
        }

    }

    private function criaPedido($id, $config){
        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        $urlFilter = $this->url . "/pedidos/".$id;
        curl_setopt($ch, CURLOPT_URL, $urlFilter);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $item = json_decode(curl_exec($ch));
        curl_close($ch);

        $cliente = null;
        if($item->cliente->documento){
            $doc = $item->cliente->documento;
            $cliente = Cliente::where('empresa_id', $config->empresa_id)
            ->where('cpf_cnpj', $doc)->first();
        }

        if($cliente == null){
            $cliente = Cliente::where('empresa_id', $config->empresa_id)
            ->where('telefone', $item->cliente->telefone)->first();
        }

        $dataPedido = [
            'empresa_id' => $config->empresa_id, 
            'cliente_id' => $cliente ? $cliente->id : null, 
            'data' => $item->data,
            'numero_pedido' => $item->numero,
            'nome' => $item->cliente->nome,
            'documento' => $item->cliente->documento,
            'telefone' => $item->cliente->telefone,
            'email' => $item->email,
            'cep' => $item->cliente->endereco ? $item->cliente->endereco->cep : '',
            'rua' => $item->cliente->endereco ? $item->cliente->endereco->logradouro : '',
            'numero' => $item->cliente->endereco ? $item->cliente->endereco->numero : '',
            'bairro' => $item->cliente->endereco ? $item->cliente->endereco->bairro : '',
            'cidade' => $item->cliente->endereco ? $item->cliente->endereco->cidade : '',
            'uf' => $item->cliente->endereco ? $item->cliente->endereco->estado : '',
            'complemento' => $item->cliente->endereco ? $item->cliente->endereco->complemento : '',
            'total' => $item->valorPedido,
            'observacao' => $item->observacao,
            'entrega' => $item->entrega,
            'taxa_entrega' => $item->taxaEntrega,
            'taxa_retirada' => $item->taxaRetirada,
            '_id' => $item->id,
            'hash' => $item->hash,
            'codigo_link_rastreio' => $item->codigoLinkRastreio,
            'tipo_pagamento' => $item->pagamento ? $item->pagamento->descricao : ''
        ];
        $pedido = PedidoVendiZap::create($dataPedido);

        foreach($item->itens as $itemPedido){
            $produto = Produto::where('vendizap_id', $itemPedido->id_produto)->first();
            if($produto == null){
                $produto = $this->cadastrarProduto($itemPedido, $config->empresa_id);
            }
            $dataItemPedido = [
                'pedido_id' => $pedido->id,
                'produto_id' => $produto ? $produto->id : null,
                'vendizap_produto_id' => $itemPedido->id_produto,
                'descricao' => $itemPedido->descricao,
                'detalhes' => $itemPedido->detalhes,
                'unidade' => $itemPedido->unidadeVenda,
                'observacao' => $itemPedido->observacaoProduto,
                'codigo' => $itemPedido->codigo,
                'valor' => $itemPedido->preco,
                'valor_promociconal' => $itemPedido->precoPromocional,
                'quantidade' => $itemPedido->quantidade,
                'sub_total' => $itemPedido->valorLiquido,
                'valor_adicionais' => $itemPedido->valorAdicionais
            ];
            ItemPedidoVendiZap::create($dataItemPedido);
        }

        return $pedido;

    }

    private function cadastrarProduto($p, $empresa_id){
        $produto = Produto::create([
            'vendizap_id' => $p->id,
            'empresa_id' => $empresa_id,
            'nome' => $p->descricao,
            'valor_unitario' => isset($p->preco) ? $p->preco : 0,
        ]);
        $locais = Localizacao::where('empresa_id', $empresa_id)->get();
        foreach($locais as $l){
            ProdutoLocalizacao::updateOrCreate([
                'produto_id' => $produto->id, 
                'localizacao_id' => $l->id
            ]);
        }
    }
}
