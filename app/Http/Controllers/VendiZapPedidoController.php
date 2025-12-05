<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendiZapConfig;
use App\Models\PedidoVendiZap;
use App\Models\ItemPedidoVendiZap;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Cidade;
use App\Models\Transportadora;
use App\Models\NaturezaOperacao;
use App\Models\Empresa;
use App\Models\Nfe;
use App\Models\Funcionario;
use App\Models\PadraoTributacaoProduto;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;

class VendiZapPedidoController extends Controller
{
    protected $url = "https://app.vendizap.com/api";

    public function index(Request $request){

        $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();
        if($config == null){
            session()->flash("flash_error", "Configure as credenciais!");
            return redirect()->route('vendizap-config.index');
        }

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        $urlFilter = $this->url . "/pedidos";
        curl_setopt($ch, CURLOPT_URL, $urlFilter);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);

        if(!is_array($data)){
            session()->flash("flash_error", $data);
            return redirect()->route('vendizap-config.index');
        }
        // dd($data);
        foreach($data as $item){

            $pedido = PedidoVendiZap::where('_id', $item->id)
            ->where('empresa_id', $request->empresa_id)
            ->first();

            $cliente = null;
            if($item->cliente->documento){
                $doc = $item->cliente->documento;
                $cliente = Cliente::where('empresa_id', $request->empresa_id)
                ->where('cpf_cnpj', $doc)->first();
            }

            if($cliente == null){
                $cliente = Cliente::where('empresa_id', $request->empresa_id)
                ->where('telefone', $item->cliente->telefone)->first();
            }

            if($pedido == null){
                $dataPedido = [
                    'empresa_id' => $request->empresa_id, 
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
                    'codigo_link_rastreio' => isset($item->codigoLinkRastreio) ? $item->codigoLinkRastreio : '',
                    'tipo_pagamento' => $item->pagamento ? $item->pagamento->descricao : ''
                ];

                $pedido = PedidoVendiZap::create($dataPedido);
                foreach($item->itens as $itemPedido){
                    $produto = Produto::where('vendizap_id', $itemPedido->id_produto)->first();
                    if($produto == null){
                        $produto = $this->cadastrarProduto($itemPedido);
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
            }
        }

        $nome = $request->nome;
        $telefone = $request->telefone;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = PedidoVendiZap::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data', '<=', $end_date);
        })
        ->when(!empty($nome), function ($query) use ($nome) {
            return $query->where('nome', 'like', "%$nome%");
        })
        ->when(!empty($telefone), function ($query) use ($telefone) {
            return $query->where('nomtelefonee', 'like', "%$telefone%");
        })
        ->paginate(__itensPagina());

        return view('vendizap_pedidos.index', compact('data'));
    }

    private function cadastrarProduto($p){
        if (isset($p->id)) {
            $produto = Produto::create([
                'vendizap_id' => $p->id,
                'empresa_id' => request()->empresa_id,
                'nome' => $p->descricao,
                'valor_unitario' => isset($p->preco) ? $p->preco : 0,
            ]);
            $locais = Localizacao::where('empresa_id', request()->empresa_id)->get();
            foreach($locais as $l){
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $produto->id, 
                    'localizacao_id' => $l->id
                ]);
            }
        }
    }

    public function show($id){
        $pedido = PedidoVendiZap::findOrFail($id);

        $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();
        if($config == null){
            session()->flash("flash_error", "Configure as credenciais!");
            return redirect()->route('vendizap-config.index');
        }

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        $urlFilter = $this->url . "/pedidos/".$pedido->_id;
        curl_setopt($ch, CURLOPT_URL, $urlFilter);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);

        if(!isset($data->cliente)){
            session()->flash("flash_error", $data);
            return redirect()->back();
        }
        $cidade = null;
        if($data->cliente->endereco){
            $cidade = Cidade::where('nome', $data->cliente->endereco->cidade)
            ->where('uf', $data->cliente->endereco->estado)->first();
        }

        foreach($data->itens as $it){
            $it->variacaoNome = "";
            if($it->relacaoVariacao){
                foreach($it->relacaoVariacao as $r){
                    $it->variacaoNome .= " " . $r->variaveis[0]->nome;
                }
            }
        }
        // dd($data);
        return view('vendizap_pedidos.show', compact('data', 'pedido', 'cidade'));
    }

    public function setCliente(Request $request, $id){
        $item = PedidoVendiZap::findOrFail($id);
        $cliente = Cliente::findOrFail($request->cliente_id);

        if($cliente){
            $item->documento = $cliente->cpf_cnpj;
            $item->telefone = $cliente->telefone;
            $item->cliente_id = $cliente->id;
            $item->save();
            session()->flash("flash_success", "Cliente alterado!");
        }
        return redirect()->back();
    }

    public function storeCliente(Request $request, $id){
        $item = PedidoVendiZap::findOrFail($id);
        $cliente = Cliente::create($request->all());

        $item->cliente_id = $cliente->id;
        $item->save();
        session()->flash("flash_success", "Cliente cadastrado!");
        return redirect()->back();

    }

    public function gerarNfe($id)
    {
        $item = PedidoVendiZap::findOrFail($id);

        if(!$item->cliente){
            session()->flash("flash_error", "Cliente não cadastrado no sistema");
            return redirect()->back();
        }
        $cliente = $item->cliente;

        foreach($item->itens as $i){
            if($i->produto == null){
                session()->flash("flash_error", "Produto " . $i->descricao . " não está cadastrado!");
                return redirect()->back();
            }
            if(!$i->produto->cfop_estadual || !$i->produto->cfop_outro_estado || !$i->produto->cst_csosn){
                $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();
                return view('vendizap_pedidos.definir_tributacao', compact('item', 'padroes'));
            }
        }

        $cidades = Cidade::all();
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();

        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        } 
        // $produtos = Produto::where('empresa_id', request()->empresa_id)->get();
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $caixa = __isCaixaAberto();
        $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        $numeroNfe = Nfe::lastNumero($empresa);
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();

        $isPedidoVendiZap = 1;

        $naturezaPadrao = NaturezaOperacao::where('empresa_id', request()->empresa_id)
        ->where('padrao', 1)->first();
        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'isPedidoVendiZap', 'numeroNfe',
            'caixa', 'funcionarios', 'naturezaPadrao'));
    }

    public function updateTributacao(Request $request, $id){
        try{
            for($i=0; $i<sizeof($request->produto_id); $i++){
                $item = Produto::findOrFail($request->produto_id[$i]);


                $item->cst_csosn = $request->cst_csosn[$i];
                $item->cst_pis = $request->cst_pis[$i];
                $item->cst_cofins = $request->cst_cofins[$i];
                $item->cst_ipi = $request->cst_ipi[$i];

                $item->perc_icms = $request->perc_icms[$i];
                $item->perc_pis = $request->perc_pis[$i];
                $item->perc_cofins = $request->perc_cofins[$i];
                $item->perc_ipi = $request->perc_ipi[$i];
                $item->perc_red_bc = $request->perc_red_bc[$i] ?? '';
                $item->cfop_estadual = $request->cfop_estadual[$i];
                $item->cfop_outro_estado = $request->cfop_outro_estado[$i];

                $item->cfop_entrada_estadual = $request->cfop_entrada_estadual[$i];
                $item->cfop_entrada_outro_estado = $request->cfop_entrada_outro_estado[$i];
                $item->ncm = $request->ncm[$i];
                $item->cest = $request->cest[$i];

                $item->save();
            }

            session()->flash("flash_success", "Tributação definida!");
            return redirect()->route('vendizap-pedidos.gerar-nfe', [$id]);

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

}
