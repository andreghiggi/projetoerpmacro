<?php

namespace App\Utils;

use App\Http\Controllers\NfeController;
use App\Interfaces\EstoqueIntegracaoInterface;
use App\Models\ConectaVendaConfig;
use App\Models\ConectaVendaItemPedido;
use App\Models\Estoque;
use App\Models\Nfe;
use App\Models\Produto;
use App\Models\ConectaVendaPedido;
use App\Models\ProdutoVariacao;
use App\Utils\EstoqueUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use function PHPUnit\Framework\isNull;

class ConectaVendaSincronizador
{
    protected $utilEstoque;
    protected $utilNfe;
    public function __construct(EstoqueUtil $utilEstoque,NfeController $utilNfe)
    {
        $this->utilEstoque = $utilEstoque;
        $this->utilNfe = $utilNfe;
    }
    public function create(ConectaVendaConfig $empresa, Produto $produto)
    {
        $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();
        if (!$config || !$config->client_secret) {
            throw new \Exception("Chave de API do Conecta Venda não encontrada para a empresa.");
        }

        $produto_grupo = $produto->categoria->nome ?? 'Sem Grupo';

        if($produto->subcategoria){
            $produto_grupo = $produto_grupo . ' - ' . $produto->subcategoria->nome;
        }

        $produto_request = [
            'id'                   => (string) $produto->id,
            'referencia'           => $produto->referencia ?? '',
            'nome'                 => $produto->nome,
            'descricao'            => $produto->observacao ?? '',
            'grupo'                => $produto_grupo,
            'peso'                 => (float) $produto->peso * 1000 ?? 0,
            'solicitar_observacao' => (int) ($produto->solicita_observacao ?? 0),
            'ean'                  => $produto->codigo_barras ?? '',
            'multiplicador'        => (int) ($produto->conecta_venda_multiplicador ?? 1),
            'qtde_minima'          => (int) ($produto->conecta_venda_qtd_minima ?? 1),
            'data_publicacao'      => $produto->created_at->format('Y-m-d H:i:s'),
            'ativo'                => 1,
            'fotos'                => [
                $produto->img_app
//                $this->getBase64Image($produto->imagem) ?? []
            ],

        ];

        $variacoes_request = [];
        $fotos_request    = [];

        if($produto->variacoes->isNotEmpty()){
            foreach ($produto->variacoes as $i => $variacao) {
                $variacao_id = "{$produto->id}.{$variacao->id}";
                // @NOTE: Mandar sempre o produto mesmo sem estoque ?? 
                $estoque = (int) ($variacao->estoque()->sum('quantidade'));
                
                $variacao_request = [
                    "id"        => $variacao_id,
                    "descricao" => $variacao->descricao,
                    "ordem"     => $i + 1,
                    "estoque"   => $estoque,
                    "ativo"     => 1,
                    "precos"    => [
                        [
                            "tabela" => "Padrão",
                            "valor" => (float) $variacao->valor
                        ]
                    ]
                ];
                if(!empty($variacao->imagem)){
                    $fotos_request[] = $variacao->img_app;
                }
                $variacoes_request[] = $variacao_request;
            }
        }else {
            $variacao_id = "{$produto->id}.1";
            $estoque     = (int) $produto->estoque->quantidade ?? 0;
            $variacao_request    = [
                "id"        => $variacao_id,
                "descricao" => 'Único',
                "ordem"     => 1,
                "estoque"   => $estoque,
                "ativo"     => 1,
                "precos"    => [
                    [
                        "tabela" => "Padrão",
                        "valor" => (float) $produto->valor_unitario,
                        "moeda" => "R$"
                    ]
                ]
            ];

            $variacoes_request[] = $variacao_request;
        }

        $produto_request['variacoes'] = $variacoes_request;
        $produto_request['fotos']     = $fotos_request;

        $payload = [
            'chave' => $config->client_secret,
            'dados' => [$produto_request]
        ];
        $response = Http::asJson()->post('https://api.conectavenda.com.br/produtos/criar', $payload);

        if (!$response->successful()) {
            throw new \Exception("Erro ao enviar produto ao Conecta Venda: " . $response->body());
        }

        return $response->json();
    }

    public function update(ConectaVendaConfig $empresa, Produto $produto)
    {
        // @NOTE:
        // Criar e atualizar produtos usando a API do Conecta são a mesma operação!

        return $this->create($empresa, $produto);

//         $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();

//         if (!$config || !$config->client_secret) {
//             throw new \Exception("Chave de API do Conecta Venda não encontrada para a empresa.");
//         }

//         $produtoConecta = [
//             'id' => (string) $produto->conecta_venda_id,
//             'referencia' => (string) $produto->referencia ?? $produto->id,
//             'nome' => $produto->nome,
//             'descricao' => $produto->observacao ?? 'Descrição do Produto',
//             'grupo' => $produto->categoria->nome ?? 'Grupo de produtos',
//             'peso' => (float) $produto->peso * 1000 ?? 0,
//             'solicitar_observacao' => (int) ($produto->solicita_observacao ?? 0),
//             'ean' => $produto->codigo_barras ?? '',
//             'multiplicador' => (int) ($produto->conecta_venda_multiplicador ?? 1),
//             'qtde_minima' => (int) ($produto->conecta_venda_qtd_minima ?? 1),
//             'data_publicacao' => $produto->created_at->format('Y-m-d H:i:s'),
//             'ativo' => 1,
//             'fotos' => [
//                 $produto->img_app
// //                $this->getBase64Image($produto->imagem) ?? []
//             ],
//             'variacoes' => []
//         ];

//         if($produto->subcategoria){
//             $produtoConecta['grupo'] = $produtoConecta['grupo'].' - '.$produto->subcategoria->nome;
//         }

//         if($produto->variacoes->isNotEmpty()){
//             $variacoes = [];
//             foreach ($produto->variacoes as $i => $v) {

//                 $variacao = [
//                     "id" => (string) $v->id,
//                     "descricao" => $v->descricao,
//                     "ordem" => $i + 1,
//                     "ativo" => 1,
//                     "precos" => [
//                         [
//                             "tabela" => "Padrão",
//                             "valor" => (float) $v->valor
//                         ]
//                     ]
//                 ];

//                 if (!$v->estoque == null) {
//                     $quantidade = (int) ($v->estoque()->sum('quantidade'));
//                     $variacao["estoque"] = $quantidade;
//                 }

//                 $produtoConecta["variacoes"][] = $variacao;

//                 if(!empty($v->imagem)){
//                     $produtoConecta["fotos"][] = $v->img_app;
//                 }
//             }
//         }else {
//             $variacao = [
//                 "id" =>  "1",
//                 "descricao" => '',
//                 "ordem" => 1,
//                 "ativo" => 1,
//                 "precos" => [
//                     [
//                         "tabela" => "Padrão",
//                         "valor" => (float) $produto->valor_unitario,
//                         "moeda" => "R$"
//                     ]
//                 ]
//             ];
//             $quantidade = (int) $produto->estoque->quantidade;
//             if ($quantidade > 0) {
//                 $variacao["estoque"] = $quantidade;
//             }

//             $produtoConecta["variacoes"][] = $variacao;

//         }

//         $payload = [
//             'chave' => $config->client_secret,
//             'dados' => [$produtoConecta]
//         ];
//         $response = Http::asJson()->post('https://api.conectavenda.com.br/produtos/criar', $payload);

//         if (!$response->successful()) {
//             throw new \Exception("Erro ao enviar produto ao Conecta Venda: " . $response->body());
//         }

//         return $response->json();

    }


    public function listProducts(Request $request, ConectaVendaConfig $empresa)
    {

        $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();
        $payload = [
            'chave' => $config->client_secret,
            'dados' => [
                [
                    'referencia' => $request->nome,
                ]
            ]
        ];

        $response = Http::asJson()->post('https://api.conectavenda.com.br/produtos/listar', $payload);

        return  json_decode($response, true);

    }
    public function createOrder($conecta_pedido, $config, ?int $clienteId = null)
    {
        $pedido = ConectaVendaPedido::where('conecta_pedido_id', $conecta_pedido->id)->first();

        $pedido_id = 0;

        if( $pedido ) {
            $pedido_id = $pedido->id;
        }

        $pedido = ConectaVendaPedido::updateOrCreate( [ 'id' => $pedido_id ], 
        [
            'empresa_id'              => $config->empresa_id,
            'conecta_pedido_id'       => $conecta_pedido->id,
            'situacao'                => $conecta_pedido->situacao ?? null,
            'comprador'               => $conecta_pedido->comprador ?? null,
            'vendedor'                => $conecta_pedido->vendedor ?? null,
            'vendedor_id'             => $conecta_pedido->vendedor_id ?? null,
            'nfe_id'                  => null,
            'catalogo'                => $conecta_pedido->catalogo ?? null,
            'tabela'                  => $conecta_pedido->tabela ?? null,
            'email'                   => $conecta_pedido->email ?? null,
            'telefone'                => $conecta_pedido->telefone ?? null,
            'observacao'              => $conecta_pedido->observacao ?? null,
            'razao_social'            => $conecta_pedido->razao_social ?? null,
            'inscricao_estadual'      => $conecta_pedido->inscricao_estadual ?? null,
            'cpf'                     => $conecta_pedido->cpf ?? null,
            'cnpj'                    => $conecta_pedido->cnpj ?? null,
            'cep'                     => $conecta_pedido->cep ?? null,
            'estado'                  => $conecta_pedido->estado ?? null,
            'cidade'                  => $conecta_pedido->cidade ?? null,
            'endereco'                => $conecta_pedido->endereco ?? null,
            'numero'                  => $conecta_pedido->numero ?? null,
            'complemento'             => $conecta_pedido->complemento ?? null,
            'bairro'                  => $conecta_pedido->bairro ?? null,
            'data_criacao'            => $conecta_pedido->data_criacao ?? now(),
            'indice_catalogo'         => $conecta_pedido->indice_catalogo ?? null,
            'valor_pedido'            => $conecta_pedido->valor_pedido ?? 0,
            'valor_frete'             => $conecta_pedido->valor_frete ?? 0,
            'frete_tipo'              => $conecta_pedido->frete_tipo ?? null,
            'cupom'                   => $conecta_pedido->cupom ?? null,
            'desconto'                => $conecta_pedido->desconto ?? 0,
            'valor_desconto'          => $conecta_pedido->valor_desconto ?? 0,
            'valor_pagamento'         => $conecta_pedido->valor_pagamento ?? 0,
            'pagamento_intermediador' => $conecta_pedido->pagamento_intermediador ?? null,
            'pagamento_tipo'          => $conecta_pedido->pagamento_tipo ?? null,
            'parcelas'                => $conecta_pedido->parcelas ?? 1,
            'data_atualizacao_status' => $conecta_pedido->data_atualizacao_status ?? null,
            'cliente_id'              => $clienteId,
        ]);

        if($pedido && isset($conecta_pedido->produtos)){
            foreach ($conecta_pedido->produtos as $item){
                $this->createItemOrder($item, $pedido);
            }
        }

        return $pedido;
    }

    public function createItemOrder($item, $pedido)
    {
        $produto               = Produto::find( $item->produto_id );

        if( !str_contains( $item->variacao_id, "." ) ) {
            // Não faz parte do padrão de variação '{produto_id}.{variacao_id}'
            return null;
        }
        
        [ $_, $variacao_id_part ] = explode('.', $item->variacao_id );
        $variacao              = ProdutoVariacao::find( $variacao_id_part );

        if( !$produto ) {
            throw new \Exception("Produto com ID: '$item->produto_id' não existe!");
        }

        if( !$variacao ) {
            throw new \Exception("Variação com ID: '$variacao_id_part' não existe!");
        }

        $pedido_id   = (int) $pedido->id;
        $produto_id  = (int) $item->produto_id;
        $variacao_id = (int) $variacao_id_part;

        return ConectaVendaItemPedido::updateOrCreate(
            [
                'pedido_id' => $pedido_id,
                'produto_id'  => $produto_id,
                'variacao_id' => $variacao_id,
            ],
            [
                'nome'            => $item->produto_nome,
                'referencia'      => $item->referencia,
                'descricao'       => $item->descricao,
                'ean'             => $item->ean,
                'peso'            => $item->peso,
                'quantidade'      => $item->qtde,
                'valor_unitario'  => $item->valor_unitario,
                'observacao'      => $item->observacao,
                'sub_total'       => $item->qtde * $item->valor_unitario,
            ]
        );
    }

    public function returnStock($order, $config)
    {
        $pedido = ConectaVendaPedido::where('id', $order->id)->first();
        if(!empty($pedido->nfe_id)){
            $nf = Nfe::where('id', $pedido->nfe_id)->first();
            if($nf){
                $this->utilNfe->destroy($pedido->nfe_id);
            }
        }
        foreach($order->produtos as $produto){
            $qtd = $produto->quantidade ?? $produto->qtde ?? 0;
            $transacao = Estoque::where('produto_id', $produto->produto_id)
                ->when($produto->variacao_id, function ($q) use ($produto) {
                    $q->where('produto_variacao_id', $produto->variacao_id);
                })
                ->first();
            $this->utilEstoque->incrementaEstoqueCron($produto->produto_id, $qtd, $produto->variacao_id ?: null);

            $usuarioId = \Auth::check() ? \Auth::id() : null;

            if ($transacao) {
                $this->utilEstoque->movimentacaoProduto(
                    $produto->produto_id,
                    $qtd,
                    'incremento',
                    $transacao->id,
                    'alteracao_estoque',
                    $usuarioId,
                    $produto->variacao_id
                );
            }
        }

        $data = ConectaVendaPedido::find($order->id);
        if($data){
            $data->situacao = $order->situacao;
            $data->save();
        }
    }

    public function updateOrderStatus($empresa, $conecta_venda_id, $situacao)
    {

        if (!$empresa || !$empresa->client_secret) {
            throw new \Exception("Chave de API do Conecta Venda não encontrada para a empresa.");
        }

        $payload = [
            'chave' => $empresa->client_secret,
            'dados' => [
                [
                    'id_pedido' => (string) $conecta_venda_id,
                    'situacao' => $situacao
                ]
            ]
        ];

        $response = Http::asJson()->post('https://api.conectavenda.com.br/pedidos/editar', $payload);

        if (!$response->successful()) {
            throw new \Exception("Erro ao atualizar status do pedido no Conecta Venda: " . $response->body());
        }
        return $response->json();
    }

    private function getBase64Image($filename)
    {
        $path = public_path('uploads/produtos/' . $filename);

        if (!file_exists($path)) {
            return null; // ou uma imagem default em base64
        }

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function atualizarEstoque(ConectaVendaConfig $empresa, Produto $produto)
    {
        $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();

        if (!$config || !$config->client_secret) {
            throw new \Exception("Chave de API do Conecta Venda não encontrada para a empresa.");
        }

        if (!$produto->conecta_venda_id) {
            throw new \Exception("Produto {$produto->id} não possui conecta_venda_id vinculado.");
        }

        $produtoConecta = [
            'id'                   => (string) $produto->conecta_venda_id,
            'referencia'           => (string) $produto->referencia ?? $produto->id,
            'nome'                 => $produto->nome,
            'descricao'            => $produto->observacao ?? 'Descrição do Produto',
            'grupo'                => $produto->categoria->nome ?? 'Grupo de produtos',
            'peso'                 => (float) ($produto->peso ?? 0),
            'solicitar_observacao' => 1,
            'ean'                  => $produto->codigo_barras ?? '',
            'multiplicador'        => 1,
            'qtde_minima'          => 1,
            'data_publicacao'      => $produto->created_at->format('Y-m-d H:i:s'),
            'ativo'                => 1,
            'variacoes'            => [],
        ];

        foreach ($produto->variacoes as $i => $v) {
            $produtoConecta["variacoes"][] = [
                "id" => (string) $v->id,
                "descricao" => $v->descricao,
                "estoque" => (int) ($v->estoque()->sum('quantidade') ?? 0),
                "ordem" => $i + 1,
                "ativo" => 1,
                "precos" => [
                    [
                        "tabela" => "Padrão",
                        "valor" => (float) $v->valor
                    ]
                ]
            ];
        }

        $payload = [
            'chave' => $config->client_secret,
            'dados' => [$produtoConecta]
        ];

        $response = Http::asJson()->post('https://api.conectavenda.com.br/produtos/criar', $payload);

        if (!$response->successful()) {
            throw new \Exception("Erro ao atualizar estoque no Conecta Venda: " . $response->body());
        }

        return $response->json();
    }

}



