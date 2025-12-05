<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Nfce;

use App\Models\NaturezaOperacao;
use App\Models\Transportadora;

use App\Models\VendaSuspensa;
use App\Models\ConfigGeral;
use App\Models\Pedido;
use App\Models\ConfiguracaoCardapio;
use Illuminate\Support\Facades\DB;

class PdvMoboController extends Controller
{
    public function index(Request $request){

        if (!file_exists(public_path('style_pdv_mobo.css'))) {
            die;
        }

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $caixa = __isCaixaAberto();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();

        $mes = date('m');
        $ano = date('Y');

        $produtos = Produto::where('produtos.empresa_id', $request->empresa_id)
        ->leftJoin('item_nfces', function($join) use ($mes, $ano){
            $join->on('item_nfces.produto_id', '=', 'produtos.id')
            ->whereMonth('item_nfces.created_at', $mes)
            ->whereYear('item_nfces.created_at', $ano);
        })
        ->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
        ->select(
            'produtos.id',
            DB::raw('COALESCE(FLOOR(estoques.quantidade), 0) as estoque_atual'),
            'produtos.numero_sequencial',
            'produtos.codigo_barras',
            'produtos.referencia',
            'produtos.referencia_balanca',
            'produtos.valor_unitario',
            'produtos.nome',
            'produtos.categoria_id',
            'produtos.imagem',
            'produtos.gerenciar_estoque',
            'produtos.valor_atacado',
            'produtos.valor_minimo_venda',
            'produtos.quantidade_atacado',
            DB::raw('COALESCE(SUM(item_nfces.quantidade), 0) as total_vendido_mes')
        )
        ->where('produtos.status', 1)
        ->groupBy(
            'produtos.id',
            'produtos.numero_sequencial',
            'produtos.codigo_barras',
            'produtos.referencia',
            'produtos.referencia_balanca',
            'produtos.valor_unitario',
            'produtos.nome',
            'produtos.categoria_id',
            'produtos.imagem',
            'produtos.gerenciar_estoque',
            'produtos.valor_atacado',
            'produtos.valor_minimo_venda',
            'produtos.quantidade_atacado'
        )
        ->orderByDesc('total_vendido_mes')
        ->orderBy('produtos.nome')
        ->with('adicionais', 'categoria')
        ->limit(50)
        ->get();

        $clientes = Cliente::where('empresa_id', request()->empresa_id)
        ->select('razao_social', 'cpf_cnpj', 'id', 'telefone')
        ->orderBy('razao_social')
        ->where('status', 1)
        ->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamentoMobo();

        if($config != null){
            $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($config->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $config->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }

        $item = null;
        $itens = [];
        $isVendaSuspensa = 0;
        $title = 'Nova Venda - PDV';

        if(isset($request->venda_suspensa)){
            $item = VendaSuspensa::findOrfail($request->venda_suspensa);
            $isVendaSuspensa = 1;
            $title = 'Venda Suspensa';
            foreach($item->itens as $i){
                $itens[] = [
                    'id' => $i->produto_id,
                    'nome' => $i->produto->nome,
                    'valor_unitario' => $i->valor_unitario,
                    'subtotal' => $i->sub_total,
                    'quantidade' => (int)$i->quantidade,
                    'referencia' => $i->produto->referencia,
                    'codigo_barras' => $i->produto->codigo_barras,
                    'categoria_id' => $i->produto->categoria_id,
                    'img' => $i->produto->imgApp,
                    'observacao' => $i->observacao,
                ];
            }
        }

        $isComanda = 0;
        if(isset($request->pedido_id)){
            $item = Pedido::findOrfail($request->pedido_id);
            $isComanda = 1;
            $title = 'Comanda';
            if($item->status == 0){
                session()->flash("flash_warning", "Esta comanda já esta finalizada!");
                return redirect()->route('pdv-mobo.index');
            }
            foreach($item->itens as $i){
                $itens[] = [
                    'id' => $i->produto_id,
                    'nome' => $i->produto->nome,
                    'valor_unitario' => $i->valor_unitario,
                    'quantidade' => (int)$i->quantidade,
                    'subtotal' => $i->sub_total,
                    'referencia' => $i->produto->referencia,
                    'codigo_barras' => $i->produto->codigo_barras,
                    'categoria_id' => $i->produto->categoria_id,
                    'img' => $i->produto->imgApp,
                    'observacao' => $i->observacao,
                ];
            }
        }

        if(isset($request->comanda)){
            $item = Pedido::where('comanda', $request->comanda)
            ->where('status', 1)
            ->where('empresa_id', $request->empresa_id)->first();
            if($item == null){
                $data = [
                    'cliente_nome' => '',
                    'cliente_fone' => '',
                    'comanda' => $request->comanda,
                    'total' => 0,
                    'empresa_id' => $request->empresa_id,
                    'local_pedido' => 'PDV'
                ];
                $item = Pedido::create($data);
            }

            $isComanda = 1;
            $title = 'Comanda';

            foreach($item->itens as $i){
                $itens[] = [
                    'id' => $i->produto_id,
                    'nome' => $i->produto->nome,
                    'valor_unitario' => $i->valor_unitario,
                    'quantidade' => (int)$i->quantidade,
                    'subtotal' => $i->sub_total,
                    'referencia' => $i->produto->referencia,
                    'codigo_barras' => $i->produto->codigo_barras,
                    'categoria_id' => $i->produto->categoria_id,
                    'img' => $i->produto->imgApp,
                    'observacao' => $i->observacao,
                ];
            }
        }

        $modelo = '';
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)
        ->orderBy('razao_social')
        ->get();
        $naturezas = [];
        $naturezaPadrao = null;
        if(isset($request->modelo)){
            $title = 'Nova Venda';
            $modelo = $request->modelo;
            $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
            if (sizeof($naturezas) == 0) {
                session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
                return redirect()->route('natureza-operacao.create');
            }

            $naturezaPadrao = NaturezaOperacao::where('empresa_id', request()->empresa_id)
            ->where('padrao', 1)->first();
        }

        $configCardapio = ConfiguracaoCardapio::where('empresa_id', $request->empresa_id)->first();

        return view('pdv_mobo.create', compact('config', 'categorias', 'caixa', 'produtos', 'clientes', 'tiposPagamento', 'isVendaSuspensa', 
            'item', 'itens', 'title', 'isComanda', 'configCardapio', 'modelo', 'transportadoras', 'naturezas'));
    }
}
