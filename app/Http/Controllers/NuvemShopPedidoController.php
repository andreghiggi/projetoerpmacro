<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\NuvemShopUtil;
use App\Utils\EstoqueUtil;
use App\Models\NuvemShopPedido;
use App\Models\NuvemShopItemPedido;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\PadraoTributacaoProduto;
use App\Models\Transportadora;
use App\Models\NaturezaOperacao;
use App\Models\Nfe;

class NuvemShopPedidoController extends Controller
{

    protected $util;
    protected $utilEstoque;

    public function __construct(NuvemShopUtil $util, EstoqueUtil $utilEstoque)
    {
        $this->util = $util;
        $this->utilEstoque = $utilEstoque;
    }

    public function index(Request $request){
        $store_info = session('store_info');
        if(!$store_info){
            return redirect()->route('nuvem-shop-auth.index');
        }

        $page = $request->page ? $request->page : 1;
        $cliente = $request->cliente;
        $data_inicial = $request->start_date;
        $data_final = $request->end_date;

        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')'); 
        $pedidos = [];
        try{
            if($cliente != "" || $data_inicial != "" || $data_final != ""){
                $sql = "orders?q=".$cliente."";
                if($data_inicial != ""){
                    $sql .= "&created_at_min=".\Carbon\Carbon::parse(str_replace("/", "-", $data_inicial))->format('Y-m-d')."";
                }
                if($data_final != ""){
                    $sql .= "&created_at_max=".\Carbon\Carbon::parse(str_replace("/", "-", $data_final))->format('Y-m-d')."";
                }
                $pedidos = (array)$api->get($sql . "&per_page=10");
            }else{
                $pedidos = (array)$api->get("orders?page=".$page."&per_page=12");
            }
            $pedidos = $pedidos['body'];
            $this->storePedidos($pedidos, $request->empresa_id);
            
        }catch(\Exception $e){
            session()->flash("flash_warning", $e->getMessage());
            // return redirect()->route('nuvem-shop-pedidos.index');
        }


        return view('nuvem_shop_pedidos.index', compact('pedidos', 'page'));

    }

    private function storePedidos($pedidos, $empresa_id){

        foreach($pedidos as $p){
            $pedido = NuvemShopPedido::where('pedido_id', $p->id)->first();
            if($pedido != null){
                $this->atualizaCliente($p, $empresa_id);
                $this->atualizaPedido($p);
            }else{


                $data = [
                    'pedido_id' => $p->id,
                    'rua' => $p->billing_address,
                    'numero' => $p->billing_number ?? 0,
                    'bairro' => $p->billing_locality ?? '',
                    'cidade' => $p->billing_city,
                    'cep' => $p->billing_zipcode,
                    'total' => $p->total,
                    'cliente_id' => $p->customer->id,
                    'observacao' => $p->shipping_option,
                    'nome' => $p->customer->name,
                    'valor_frete' => $p->shipping_cost_customer,
                    'email' => $p->customer->email,
                    'documento' => $p->customer->identification ? $p->customer->identification : '',
                    'empresa_id' => $empresa_id,
                    'subtotal' => $p->subtotal,
                    'desconto' => $p->discount,
                    'numero_nfe' => 0,
                    'status_envio' => $p->shipping_status,
                    'gateway' => $p->gateway,
                    'status_pagamento' => $p->payment_status,
                    'data' => $p->created_at
                ];

                $this->storeCliente($p, $empresa_id);

                $pedido = NuvemShopPedido::create($data);

                foreach($p->products as $prod){

                    $produto = $this->validaProduto($prod, $empresa_id);

                    $item = [
                        'pedido_id' => $pedido->id,
                        'produto_id' => $produto->id,
                        'quantidade' => $prod->quantity,
                        'valor_unitario' => $prod->price,
                        'sub_total' => $prod->quantity * $prod->price,
                        'nome' => $prod->name
                    ];

                    NuvemShopItemPedido::create($item);
                }
            }
        }
    }

    private function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }

    private function storeCliente($pedido, $empresa_id){
        $customer = $pedido->customer;
        $address = $customer->default_address;

        if($pedido->shipping_address){
            $address = $pedido->shipping_address;
        }

        $cidade = null;

        if($address){
            $cidade = Cidade::
            where('nome', $address->city)
            ->first();
        }

        $telefone = $address ? ($address->phone ? $address->phone : $customer->billing_phone) : '';
        if(substr($telefone, 0, 3) == '+55'){
            $telefone = substr($telefone, 3, strlen($telefone));
        }

        $doc = $customer->identification ? $customer->identification : $pedido->contact_identification;

        if(strlen($doc) >= 11){
            $mask = '00.000.000/0000-00';
            if(strlen($doc) == 11){
                $mask = '000.000.000-00';
            }
            $doc = $this->mask($doc, $mask);
        }

        $cliente_id = null;

        if(isset($pedido->rua) && isset($pedido->nome)){

            $doc = $pedido->identification;

            $mask = '00.000.000/0000-00';
            if(strlen($doc) == 11){
                $mask = '000.000.000-00';
            }
            $doc = $this->mask($doc, $mask);

            $cidade = Cidade::
            where('nome', $pedido->cidade)
            ->first();

            $data = [
                'razao_social' => $pedido->nome,
                'nome_fantasia' => $pedido->nome,
                'bairro' => $pedido->bairro,
                'numero' => $pedido->numero,
                'rua' => $pedido->rua,
                'cpf_cnpj' => $doc,
                'telefone' => '', 
                'celular' => '',
                'email' => $pedido->email,
                'cep' => $pedido->cep,
                'ie_rg' => '',
                'consumidor_final' => 0,
                'cidade_id' => $cidade == null ? null : $cidade->id,
                'empresa_id' => $empresa_id,
                'nuvem_shop_id' => $pedido->cliente_id,
            ];
            $client_id = $pedido->cliente_id;

        }else{
            $numero = $pedido->billing_number;

            if(!$telefone){
                $telefone = $pedido->contact_phone;
                $tel = explode("+55", $telefone);
                if(isset($tel[1])){
                    $telefone = $tel[1];
                }

            }

            $dataCliente = [
                'razao_social' => $customer->name,
                'nome_fantasia' => $customer->name,
                'bairro' => $address->locality ? $address->locality : $customer->billing_locality,
                'numero' => $numero,
                'rua' => $address->address ? $address->address : $customer->billing_address,
                'cpf_cnpj' => $doc,
                'telefone' => $telefone,
                'email' => $customer->email,
                'cep' => $address->zipcode,
                'ie_rg' => '',
                'empresa_id' => $empresa_id,
                'nuvem_shop_id' => $customer->id,
                'cidade_id' => $cidade == null ? null : $cidade->id,
                'consumidor_final' => 1,
                'contribuinte' => 0,
            ];
            $client_id = $customer->id;

        }

        $cliente = Cliente::where('nuvem_shop_id', $client_id)->first();
        if($cliente == null){
            $cliente = Cliente::create($dataCliente);
        }
        return $cliente;
    }

    private function validaProduto($prod, $empresa_id){

        $produto = Produto::where('nuvem_shop_id', $prod->product_id)->first();
        
        if($produto != null) return $produto;

        $config = Empresa::findOrFail($empresa_id);
        $tributacao = PadraoTributacaoProduto::where('empresa_id', $empresa_id)
        ->where('padrao', 1)->first();
        if($tributacao == null){
            $tributacao = PadraoTributacaoProduto::where('empresa_id', $empresa_id)->first();
        }
        $valorVenda = $prod->price;

        $last = Produto::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numeroSequencial = $last != null ? $last->numero_sequencial : 0;
        $numeroSequencial++;

        $dataProduto = [
            'nome' => $prod->name,
            'valor_unitario' => $valorVenda,
            'valor_compra' => 0,
            'numero_sequencial' => $numeroSequencial,
            'ncm' => $tributacao ? $tributacao->ncm : '',
            'cst_csosn' => $tributacao ? $tributacao->cst_csosn : '',
            'cst_pis' => $tributacao ? $tributacao->cst_pis : '',
            'cst_cofins' => $tributacao ? $tributacao->cst_cofins : '',
            'cst_ipi' => $tributacao ? $tributacao->cst_ipi : '',
            'perc_red_bc' => $tributacao ? $tributacao->perc_red_bc : '',
            'cEnq' => $tributacao ? $tributacao->cEnq : '999',
            'pST' => $tributacao ? $tributacao->pST : '',
            'cfop_estadual' => $tributacao ? $tributacao->cfop_estadual : '',
            'cfop_outro_estado' => $tributacao ? $tributacao->cfop_outro_estado : '',
            'cest' => $tributacao ? $tributacao->cest : '',
            'codigo_beneficio_fiscal' => $tributacao ? $tributacao->codigo_beneficio_fiscal : '',
            'cfop_entrada_estadual' => $tributacao ? $tributacao->cfop_entrada_estadual : '',
            'cfop_entrada_outro_estado' => $tributacao ? $tributacao->cfop_entrada_outro_estado : '',
            'codigo_barras' => 'SEM GTIN',
            'largura' => $prod->width,
            'comprimento' => $prod->depth,
            'altura' => $prod->height,
            'peso' => $prod->weight,
            'empresa_id' => $empresa_id,
            "nuvem_shop_id" => $prod->product_id,
            'valor_prazo' => 0
        ];
        // print_r($arr);
        // die;

        $produto = Produto::create($dataProduto);

        return $produto;

    }

    private function atualizaCliente($pedido, $empresa_id){

        $customer = $pedido->customer;
        // dd($pedido);
        // dd($customer);
        if($customer){

            $cliente = Cliente::where('nuvem_shop_id', $customer->id)->first();
            if($cliente == null){
                $this->storeCliente($pedido, $empresa_id);
                return 1;
            }
            try{

                $cliente->razao_social = $customer->name;
                // $cliente->nome_fantasia = $customer->name;
                $cliente->cpf_cnpj = $customer->identification ? $customer->identification : $pedido->contact_identification;

                if(isset($pedido->shipping_address)){
                    $address = $pedido->shipping_address;

                    $telefone = $address ? ($address->phone ? $address->phone : $customer->billing_phone) : '';

                    if(substr($telefone, 0, 3) == '+55'){
                        $telefone = substr($telefone, 3, strlen($telefone));
                    }

                    $cidade = Cidade::
                    where('nome', $address->city)
                    ->first();

                    $cliente->telefone = $telefone;
                    $cliente->cep = $address->zipcode;
                    $cliente->bairro = $address->locality;
                    $cliente->numero = $address->number;
                    $cliente->rua = $address->address;
                    $cliente->cidade_id = $cidade == null ? null : $cidade->id;

                    $cliente->save();
                }
            }catch(\Exception $e){

            }
        }
    }

    private function atualizaPedido($p){
        $pedido = NuvemShopPedido::where('pedido_id', $p->id)->first();
        $pedido->valor_frete = $p->shipping_cost_customer;
        $pedido->save();
    }

    public function show($id){
        $item = NuvemShopPedido::where('pedido_id', $id)->first();
        return view('nuvem_shop_pedidos.show', compact('item'));
    }

    public function gerarNfe($id)
    {
        $item = NuvemShopPedido::findOrFail($id);

        if(!$item->cliente){
            session()->flash("flash_error", "Cliente não cadastrado no sistema");
            return redirect()->back();
        }
        $cliente = $item->cliente;
        
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

        $item->cliente_id = $cliente->id;

        $isPedidoNuvemShop = 1;
        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'isPedidoNuvemShop', 'numeroNfe',
            'caixa'));
    }

}
