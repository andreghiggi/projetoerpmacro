<?php

namespace App\Utils;

use App\Interfaces\EstoqueIntegracaoInterface;
use App\Models\ConectaVendaConfig;
use App\Models\ConectaVendaItemPedido;
use App\Models\Produto;
use App\Models\ConectaVendaPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;



class ConectaVendaUtil
{
    public function create(ConectaVendaConfig $empresa, Produto $produto)
    {
        $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();

        if (!$config || !$config->client_secret) {
            throw new \Exception("Chave de API do Conecta Venda não encontrada para a empresa.");
        }

        $produtoConecta = [
            'id' => (string) $produto->id,
            'referencia' => (string) $produto->observacao ?? $produto->id,
            'nome' => $produto->nome,
            'descricao' => $produto->descricao ?? 'Descrição do Produto',
            'grupo' => $produto->categoria->nome ?? 'Grupo de produtos',
            'peso' => (float) $produto->peso ?? 0,
            'solicitar_observacao' => 1,
            'ean' => $produto->codigo_barras ?? '',
            'multiplicador' => 1,
            'qtde_minima' => 1,
            'data_publicacao' => $produto->created_at->format('Y-m-d H:i:s'),
            'ativo' => 1,
            'fotos' => [
                $produto->img_app
//                $this->getBase64Image($produto->imagem) ?? []
            ],
            'variacoes' => []
        ];

        foreach ($produto->variacoes as $i => $v) {
            $variacao = [
                "id" => (string) $v->id,
                "descricao" => $v->descricao,
                "ordem" => $i + 1,
                "ativo" => 1,
                "precos" => [
                    [
                        "tabela" => "Padrão",
                        "valor" => (float) $v->valor
                    ]
                ]
            ];

            $quantidade = (int) ($v->estoque()->sum('quantidade'));

            if ($quantidade > 0) {
                $variacao["estoque"] = $quantidade;
            }

            $produtoConecta["variacoes"][] = $variacao;
        }

        $payload = [
            'chave' => $config->client_secret,
            'dados' => [$produtoConecta]
        ];

        $response = Http::asJson()->post('https://api.conectavenda.com.br/produtos/criar', $payload);

        if (!$response->successful()) {
            throw new \Exception("Erro ao enviar produto ao Conecta Venda: " . $response->body());
        }

        return $response->json();
    }

    public function update(ConectaVendaConfig $empresa, Produto $produto)
    {
        $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();

        if (!$config || !$config->client_secret) {
            throw new \Exception("Chave de API do Conecta Venda não encontrada para a empresa.");
        }

        $produtoConecta = [
            'id' => (string) $produto->conecta_venda_id,
            'referencia' => (string) $produto->observacao ?? $produto->id,
            'nome' => $produto->nome,
            'descricao' => $produto->descricao ?? 'Descrição do Produto',
            'grupo' => $produto->categoria->nome ?? 'Grupo de produtos',
            'peso' => (float) $produto->peso ?? 0,
            'solicitar_observacao' => 1,
            'ean' => $produto->codigo_barras ?? '',
            'multiplicador' => 1,
            'qtde_minima' => 1,
            'data_publicacao' => $produto->created_at->format('Y-m-d H:i:s'),
            'ativo' => 1,
            'fotos' => [
                $produto->img_app
//                $this->getBase64Image($produto->imagem) ?? []
            ],
            'variacoes' => []
        ];

        foreach ($produto->variacoes as $i => $v) {
            $variacao = [
                "id" => (string) $v->id,
                "descricao" => $v->descricao,
                "ordem" => $i + 1,
                "ativo" => 1,
                "precos" => [
                    [
                        "tabela" => "Padrão",
                        "valor" => (float) $v->valor
                    ]
                ]
            ];

            $quantidade = (int) ($v->estoque()->sum('quantidade'));

            if ($quantidade > 0) {
                $variacao["estoque"] = $quantidade;
            }

            $produtoConecta["variacoes"][] = $variacao;
        }

        $payload = [
            'chave' => $config->client_secret,
            'dados' => [$produtoConecta]
        ];

        $response = Http::asJson()->post('https://api.conectavenda.com.br/produtos/criar', $payload);

        if (!$response->successful()) {
            throw new \Exception("Erro ao enviar produto ao Conecta Venda: " . $response->body());
        }

        return $response->json();

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
    public function createOrder($dadosPedido, $config, ?int $clienteId = null)
    {
        return ConectaVendaPedido::updateOrCreate(
            ['id' => $dadosPedido->id],
            [
                'empresa_id' => $config->empresa_id,
                'situacao' => $dadosPedido->situacao ?? null,
                'comprador' => $dadosPedido->comprador ?? null,
                'vendedor' => $dadosPedido->vendedor ?? null,
                'vendedor_id' => $dadosPedido->vendedor_id ?? null,
                'nfe_id' => null,
                'catalogo' => $dadosPedido->catalogo ?? null,
                'tabela' => $dadosPedido->tabela ?? null,
                'email' => $dadosPedido->email ?? null,
                'telefone' => $dadosPedido->telefone ?? null,
                'observacao' => $dadosPedido->observacao ?? null,
                'razao_social' => $dadosPedido->razao_social ?? null,
                'inscricao_estadual' => $dadosPedido->inscricao_estadual ?? null,
                'cpf' => $dadosPedido->cpf ?? null,
                'cnpj' => $dadosPedido->cnpj ?? null,
                'cep' => $dadosPedido->cep ?? null,
                'estado' => $dadosPedido->estado ?? null,
                'cidade' => $dadosPedido->cidade ?? null,
                'endereco' => $dadosPedido->endereco ?? null,
                'numero' => $dadosPedido->numero ?? null,
                'complemento' => $dadosPedido->complemento ?? null,
                'bairro' => $dadosPedido->bairro ?? null,
                'data_criacao' => $dadosPedido->data_criacao ?? now(),
                'indice_catalogo' => $dadosPedido->indice_catalogo ?? null,
                'valor_pedido' => $dadosPedido->valor_pedido ?? 0,
                'valor_frete' => $dadosPedido->valor_frete ?? 0,
                'frete_tipo' => $dadosPedido->frete_tipo ?? null,
                'desconto' => $dadosPedido->desconto ?? 0,
                'valor_desconto' => $dadosPedido->valor_desconto ?? 0,
                'valor_pagamento' => $dadosPedido->valor_pagamento ?? 0,
                'pagamento_intermediador' => $dadosPedido->pagamento_intermediador ?? null,
                'pagamento_tipo' => $dadosPedido->pagamento_tipo ?? null,
                'parcelas' => $dadosPedido->parcelas ?? 1,
                'data_atualizacao_status' => $dadosPedido->data_atualizacao_status ?? null,
                'cliente_id' => $clienteId,
            ]
        );
    }

    public function createItemOrder($item, $pedidoId)
    {
        return ConectaVendaItemPedido::updateOrCreate(
            [
                'pedido_id' => $pedidoId,
            ],
            [
                'produto_id'      => $item->produto_id,
                'variacao_id'     => $item->variacao_id,
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
            'id' => (string) $produto->conecta_venda_id,
            'referencia' => (string) $produto->observacao ?? $produto->id,
            'nome' => $produto->nome,
            'descricao' => $produto->descricao ?? 'Descrição do Produto',
            'grupo' => $produto->categoria->nome ?? 'Grupo de produtos',
            'peso' => (float) ($produto->peso ?? 0),
            'solicitar_observacao' => 1,
            'ean' => $produto->codigo_barras ?? '',
            'multiplicador' => 1,
            'qtde_minima' => 1,
            'data_publicacao' => $produto->created_at->format('Y-m-d H:i:s'),
            'ativo' => 1,
            'variacoes' => [],
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



