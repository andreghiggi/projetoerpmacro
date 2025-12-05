<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Empresa;
use App\Models\Nfce;
use App\Models\Troca;
use App\Models\Nfe;
use App\Models\SangriaCaixa;
use App\Models\OrdemServico;
use App\Models\SuprimentoCaixa;
use App\Models\User;
use App\Models\ContaEmpresa;
use App\Models\ItemServicoNfce;
use App\Models\ItemContaEmpresa;
use App\Models\FaturaNfe;
use App\Models\FaturaNfce;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\ContaEmpresaUtil;
use Illuminate\Support\Facades\DB;

class CaixaController extends Controller
{
    protected $util;
    public function __construct(ContaEmpresaUtil $util){
        $this->util = $util;
        $this->middleware('permission:caixa_view', ['only' => ['show', 'index']]);

    }

    public function index()
    {

        $item = Caixa::where('usuario_id', Auth::user()->id)->where('status', 1)->first();
        if ($item == null) {
            session()->flash('flash_warning', 'Não há caixa aberto no momento!');
            return redirect()->route('caixa.create');
        }

        $valor_abertura = $item->valor_abertura;

        $somaTiposPagamento = [];
        $contas = [];

        $idsReceber = ContaReceber::where('empresa_id', request()->empresa_id)->pluck('nfce_id')->toArray();
        // dd($idsReceber);
        $nfce = Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        ->get();

        $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->get();

        $contasReceber = ContaReceber::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('status', 1)->get();

        $contasPagar = ContaPagar::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('status', 1)->get();

        $trocas = Troca::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->get();

        $trocasPagasPorCliente = Troca::where('empresa_id', request()->empresa_id)
        ->where('caixa_id', $item->id)
        ->whereColumn('valor_troca', '>', 'valor_original')
        ->selectRaw('SUM(ABS(valor_troca - valor_original)) as total')
        ->value('total');

        $trocasPagasAoCliente = Troca::where('empresa_id', request()->empresa_id)
        ->where('caixa_id', $item->id)
        ->whereColumn('valor_troca', '<', 'valor_original')
        ->selectRaw('SUM(ABS(valor_original - valor_troca)) as total')
        ->value('total');

        $ordens = OrdemServico::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->get();
        // dd($ordens);

        $compras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->get();

        $totalVendas = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->join('fatura_nves', 'fatura_nves.nfe_id', '=', 'nves.id')
        ->sum('total');

        $totalVendas += Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->whereNotIn('id', $idsReceber)
        ->sum('total');

        // $vendas = $this->agrupaDados($nfce, $nfe, $ordens, $compras);

        $nfce->map(function($d){
            $d->tipo = 'PDV';
            return $d;
        });

        $nfe->map(function($d){
            $d->tipo = 'Pedido';
            return $d;
        });
        $vendas = $nfce->concat($nfe)->sortByDesc('created_at')->values();
        $somaTiposPagamento = $this->somaTiposPagamento($vendas);

        $somaServicos = ItemServicoNfce::join('nfces', 'nfces.id', '=', 'item_servico_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)->where('nfces.caixa_id', $item->id)
        ->sum('sub_total') +
        OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->sum('valor');

        $suprimentos = SuprimentoCaixa::where('caixa_id', $item->id)->get();
        $sangrias = SangriaCaixa::where('caixa_id', $item->id)->get();

        $somaVendas = $vendas->sum('total');
        $somaCompras = $compras->sum('total');
        $somaContasReceber = $contasReceber->sum('valor_recebido');
        $somaContasPagar = $contasPagar->sum('valor_pago');
        $somaSuprimentos = $suprimentos->sum('valor');
        $somaSangrias = $sangrias->sum('valor');

        $somaPendentesCrediario = FaturaNfe::join('nves', 'nves.id', '=', 'fatura_nves.nfe_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->where('nves.caixa_id', $item->id)
        ->where('fatura_nves.tipo_pagamento', '06')
        ->sum('fatura_nves.valor');

        $somaPendentesCrediario += FaturaNfce::join('nfces', 'nfces.id', '=', 'fatura_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->where('nfces.caixa_id', $item->id)
        ->where('fatura_nfces.tipo_pagamento', '06')
        ->sum('fatura_nfces.valor');

        $somaPendentBoleto = FaturaNfe::join('nves', 'nves.id', '=', 'fatura_nves.nfe_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->where('nves.caixa_id', $item->id)
        ->where('fatura_nves.tipo_pagamento', '15')
        ->sum('fatura_nves.valor');

        $somaPendentBoleto += FaturaNfce::join('nfces', 'nfces.id', '=', 'fatura_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->where('nfces.caixa_id', $item->id)
        ->where('fatura_nfces.tipo_pagamento', '15')
        ->sum('fatura_nfces.valor');

        $somaPendentesCreditoLoja = FaturaNfe::join('nves', 'nves.id', '=', 'fatura_nves.nfe_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->where('nves.caixa_id', $item->id)
        ->where('fatura_nves.tipo_pagamento', '05')
        ->sum('fatura_nves.valor');

        $somaPendentesCreditoLoja += FaturaNfce::join('nfces', 'nfces.id', '=', 'fatura_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->where('nfces.caixa_id', $item->id)
        ->where('fatura_nfces.tipo_pagamento', '05')
        ->sum('fatura_nfces.valor');

        if ($item != null) {
            $contasEmpresa = ContaEmpresa::where('empresa_id', request()->empresa_id)
            ->where('status', 1)
            ->get();
            return view('caixa.index', compact(
                'item',
                'vendas',
                'somaTiposPagamento',
                'valor_abertura',
                'somaServicos',
                'totalVendas',
                'compras',
                'suprimentos',
                'sangrias',
                'trocas',
                'contasReceber',
                'contasPagar',
                'contasEmpresa',

                'somaVendas',
                'somaCompras',
                'somaContasReceber',
                'somaSuprimentos',
                'trocasPagasPorCliente',
                'trocasPagasAoCliente',
                'somaSangrias',
                'somaContasPagar',
                'somaPendentesCrediario',
                'somaPendentBoleto',
                'somaPendentesCreditoLoja',
            ));
        } else {
            session()->flash('flash_warning', 'Não há caixa aberto no momento!');
            return redirect()->back();
        }
    }

    private function agrupaDados($nfce, $nfe, $ordens, $compras)
    {
        $temp = [];
        foreach ($nfe as $v) {
            $v->tipo = 'Pedido';
            $v->receita = 1;
            array_push($temp, $v);
        }
        foreach ($nfce as $v) {
            $v->tipo = 'PDV';
            $v->receita = 1;
            array_push($temp, $v);
        }

        if($ordens != null){
            foreach ($ordens as $v) {
                $v->tipo = 'OS';
                $v->receita = 1;
                array_push($temp, $v);
            }
        }

        // if($compras != null){
        //     foreach ($compras as $v) {
        //         $v->tipo = 'Compra';
        //         $v->receita = 0;
        //         array_push($temp, $v);
        //     }
        // }
        
        usort($temp, function($a, $b){
            return $a['created_at'] < $b['created_at'] ? 1 : -1;
        });
        return $temp;
    }

    private function agrupaContas($pagar, $receber)
    {
        $temp = [];
        foreach ($pagar as $c) {
            $c->tipo = 'Conta Paga';
            array_push($temp, $c);
        }
        foreach ($receber as $c) {
            $c->tipo = 'Conta Recebida';
            array_push($temp, $c);
        }
        return $temp;
    }


    private function somaTiposPagamento($vendas)
    {
        $tipos = $this->preparaTipos();

        foreach ($vendas as $v) {
            if ($v->estado != 'cancelado') {
                $troco = false;
                if ($v->fatura && sizeof($v->fatura) > 0) {
                    if ($v->fatura) {
                        foreach ($v->fatura as $f) {
                            if(isset($tipos[trim($f->tipo_pagamento)])){
                                if($f->tipo_pagamento == '01' && $troco == false){
                                    $f->valor -= $v->troco;
                                    $troco = true;
                                }
                                $tipos[trim($f->tipo_pagamento)] += $f->valor;
                            }
                        }
                    }
                }
            }
        }

        return $tipos;
    }

    private function somaTiposContas($contas)
    {
        $tipos = $this->preparaTipos();

        foreach ($contas as $c) {
            if ($c->status == 1) {
                if(isset($tipos[trim($c->tipo_pagamento)])){
                    $tipos[trim($c->tipo_pagamento)] += $c->valor_integral;
                }
            }
        }
        return $tipos;
    }


    private function preparaTipos()
    {
        $temp = [];
        foreach (Nfce::tiposPagamento() as $key => $tp) {
            $temp[$key] = 0;
        }
        return $temp;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $item = Caixa::where('usuario_id', Auth::user()->id)->where('status', 1)->first();

        return view('caixa.create', compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->merge([
                'usuario_id' => Auth::user()->id,
                'valor_abertura' => __convert_value_bd($request->valor_abertura),
                'observacao' => $request->observacao ?? '',
                'status' => 1,
                'valor_fechamento' => 0,
            ]);
            $item = Caixa::create($request->all());

            $descricaoLog = $item->usuario->name . " | CAIXA ABERTO - abertura: " . __data_pt($item->created_at) . " - valor abertura: " . __moeda($item->valor_abertura);
            __createLog($request->empresa_id, 'Caixa', 'cadastrar', $descricaoLog);
            session()->flash('flash_success', 'Caixa aberto com sucesso!');
        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            __createLog($request->empresa_id, 'Caixa', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível abrir o caixa' . $e->getMessage());
        }
        return redirect()->route('caixa.index');
    }

    public function show($id)
    {

        $item = Caixa::FindOrFail($id);
        if ($item == null) {
            session()->flash('flash_warning', 'Não há caixa aberto no momento!');
            return redirect()->route('caixa.create');
        }

        $valor_abertura = $item->valor_abertura;

        $somaTiposPagamento = [];
        $contas = [];

        $idsReceber = ContaReceber::where('empresa_id', request()->empresa_id)->pluck('nfce_id')->toArray();
        // dd($idsReceber);
        $nfce = Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        ->get();

        $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->get();

        $contasReceber = ContaReceber::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('status', 1)->get();

        $contasPagar = ContaPagar::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('status', 1)->get();

        $trocas = Troca::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->get();

        $trocasPagasPorCliente = Troca::where('empresa_id', request()->empresa_id)
        ->where('caixa_id', $item->id)
        ->whereColumn('valor_troca', '>', 'valor_original')
        ->selectRaw('SUM(ABS(valor_troca - valor_original)) as total')
        ->value('total');

        $trocasPagasAoCliente = Troca::where('empresa_id', request()->empresa_id)
        ->where('caixa_id', $item->id)
        ->whereColumn('valor_troca', '<', 'valor_original')
        ->selectRaw('SUM(ABS(valor_original - valor_troca)) as total')
        ->value('total');

        $ordens = OrdemServico::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->get();
        // dd($ordens);

        $compras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->get();

        $totalVendas = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->join('fatura_nves', 'fatura_nves.nfe_id', '=', 'nves.id')
        ->sum('total');

        $totalVendas += Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->whereNotIn('id', $idsReceber)
        ->sum('total');

        // $vendas = $this->agrupaDados($nfce, $nfe, $ordens, $compras);

        $nfce->map(function($d){
            $d->tipo = 'PDV';
            return $d;
        });

        $nfe->map(function($d){
            $d->tipo = 'Pedido';
            return $d;
        });
        $vendas = $nfce->concat($nfe)->sortByDesc('created_at')->values();
        $somaTiposPagamento = $this->somaTiposPagamento($vendas);

        $somaServicos = ItemServicoNfce::join('nfces', 'nfces.id', '=', 'item_servico_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)->where('nfces.caixa_id', $item->id)
        ->sum('sub_total') +
        OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->sum('valor');

        $suprimentos = SuprimentoCaixa::where('caixa_id', $item->id)->get();
        $sangrias = SangriaCaixa::where('caixa_id', $item->id)->get();

        $somaVendas = $vendas->sum('total');
        $somaCompras = $compras->sum('total');
        $somaContasReceber = $contasReceber->sum('valor_recebido');
        $somaContasPagar = $contasPagar->sum('valor_pago');
        $somaSuprimentos = $suprimentos->sum('valor');
        $somaSangrias = $sangrias->sum('valor');

        $somaPendentesCrediario = FaturaNfe::join('nves', 'nves.id', '=', 'fatura_nves.nfe_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->where('nves.caixa_id', $item->id)
        ->where('fatura_nves.tipo_pagamento', '06')
        ->sum('fatura_nves.valor');

        $somaPendentesCrediario += FaturaNfce::join('nfces', 'nfces.id', '=', 'fatura_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->where('nfces.caixa_id', $item->id)
        ->where('fatura_nfces.tipo_pagamento', '06')
        ->sum('fatura_nfces.valor');

        $somaPendentBoleto = FaturaNfe::join('nves', 'nves.id', '=', 'fatura_nves.nfe_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->where('nves.caixa_id', $item->id)
        ->where('fatura_nves.tipo_pagamento', '15')
        ->sum('fatura_nves.valor');

        $somaPendentBoleto += FaturaNfce::join('nfces', 'nfces.id', '=', 'fatura_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->where('nfces.caixa_id', $item->id)
        ->where('fatura_nfces.tipo_pagamento', '15')
        ->sum('fatura_nfces.valor');

        $somaPendentesCreditoLoja = FaturaNfe::join('nves', 'nves.id', '=', 'fatura_nves.nfe_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->where('nves.caixa_id', $item->id)
        ->where('fatura_nves.tipo_pagamento', '05')
        ->sum('fatura_nves.valor');

        $somaPendentesCreditoLoja += FaturaNfce::join('nfces', 'nfces.id', '=', 'fatura_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->where('nfces.caixa_id', $item->id)
        ->where('fatura_nfces.tipo_pagamento', '05')
        ->sum('fatura_nfces.valor');

        if ($item != null) {
            $contasEmpresa = ContaEmpresa::where('empresa_id', request()->empresa_id)
            ->where('status', 1)
            ->get();
            return view('caixa.show', compact(
                'item',
                'vendas',
                'somaTiposPagamento',
                'valor_abertura',
                'somaServicos',
                'totalVendas',
                'compras',
                'suprimentos',
                'sangrias',
                'trocas',
                'contasReceber',
                'contasPagar',
                'contasEmpresa',

                'somaVendas',
                'somaCompras',
                'somaContasReceber',
                'somaSuprimentos',
                'trocasPagasPorCliente',
                'trocasPagasAoCliente',
                'somaSangrias',
                'somaContasPagar',
                'somaPendentesCrediario',
                'somaPendentBoleto',
                'somaPendentesCreditoLoja',
            ));
        } else {
            session()->flash('flash_warning', 'Não há caixa aberto no momento!');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {

    //     $item = Caixa::FindOrFail($id);
    //     $vendas = [];
    //     $somaTiposPagamento = [];


    //     $nfce = Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
    //     ->where('estado', '!=', 'cancelado')
    //     // ->where('tipo_pagamento', '!=', '06')
    //     ->get();
    //     // $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
    //     // ->where('tpNF', 1)
    //     // ->get();

    //     $nfe = Nfe::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
    //     ->where('orcamento', 0)
    //     ->where('finNFe', 1)
    //     ->where('estado', '!=', 'cancelado')
    //     ->get();

    //     $ordens = OrdemServico::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
    //     ->get();

    //     $compras = Nfe::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
    //     ->where('orcamento', 0)
    //     ->where('finNFe', 1)
    //     ->where('estado', '!=', 'cancelado')
    //     ->get();

    //     $totalCompras = Nfe::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
    //     ->where('orcamento', 0)
    //     ->where('finNFe', 1)
    //     ->sum('total');

    //     $totalVendas = Nfe::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
    //     ->where('orcamento', 0)
    //     ->where('finNFe', 1)
    //     ->where('estado', '!=', 'cancelado')
    //     ->join('fatura_nves', 'fatura_nves.nfe_id', '=', 'nves.id')
    //     ->sum('total');

    //     $totalVendas +=  Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
    //     ->where('estado', '!=', 'cancelado')
    //     // ->where('tipo_pagamento', '!=', '06')
    //     ->sum('total');

    //     $data = $this->agrupaDados($nfce, $nfe, $ordens, $compras);
    //     $somaTiposPagamento = $this->somaTiposPagamento($data);

    //     $suprimentos = [];
    //     $sangrias = [];
    //     $contas = [];

    //     $pagar = ContaPagar::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->get();
    //     $receber = ContaReceber::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->get();

    //     $somaTiposContas = $this->somaTiposContas($contas);
    //     if ($item != null) {
    //         $suprimentos = SuprimentoCaixa::where('caixa_id', $item->id)->get();
    //         $sangrias = SangriaCaixa::where('caixa_id', $item->id)->get();
    //     }

    //     $somaServicos = ItemServicoNfce::join('nfces', 'nfces.id', '=', 'item_servico_nfces.nfce_id')
    //     ->where('nfces.empresa_id', request()->empresa_id)->where('nfces.caixa_id', $item->id)
    //     ->sum('sub_total') + 
    //     OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
    //     ->sum('valor');

    //     return view('caixa.show', compact(
    //         'item',
    //         'data',
    //         'somaTiposPagamento',
    //         'suprimentos',
    //         'totalCompras',
    //         'totalVendas',
    //         'sangrias',
    //         'contasReceber',
    //         'somaServicos'
    //     ));
    // }

    public function fecharEmpresa(string $id)
    {

        $item = Caixa::FindOrFail($id);
        $somaTiposPagamento = [];

        $nfce = Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->where('tipo_pagamento', '!=', '06')
        ->get();
        // $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        // ->where('tpNF', 1)
        // ->get();

        $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->get();
        $ordens = OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->get();

        $compras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->get();

        $data = $this->agrupaDados($nfce, $nfe, $ordens, $compras);
        $somaTiposPagamento = $this->somaTiposPagamento($data);
        $suprimentos = [];
        $sangrias = [];
        $contas = [];

        $pagar = ContaPagar::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->get();
        $receber = ContaReceber::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->get();

        $contas = $this->agrupaContas($pagar, $receber);
        $somaTiposContas = $this->somaTiposContas($contas);
        if ($item != null) {
            $suprimentos = SuprimentoCaixa::where('caixa_id', $item->id)->get();
            $sangrias = SangriaCaixa::where('caixa_id', $item->id)->get();
        }

        $somaServicos = ItemServicoNfce::join('nfces', 'nfces.id', '=', 'item_servico_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)->where('nfces.caixa_id', $item->id)
        ->sum('sub_total') +
        OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->sum('valor');

        $valor_abertura = $item->valor_abertura;

        $contasEmpresa = ContaEmpresa::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $totalVendas = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->join('fatura_nves', 'fatura_nves.nfe_id', '=', 'nves.id')
        ->sum('total');

        $totalVendas +=  Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->where('tipo_pagamento', '!=', '06')
        ->sum('total');

        $totalCompras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->sum('total');
        
        return view('caixa.fechar_empresa', compact(
            'item',
            'data',
            'somaTiposPagamento',
            'suprimentos',
            'sangrias',
            'contas',
            'receber',
            'pagar',
            'totalVendas',
            'totalCompras',
            'contasEmpresa',
            'valor_abertura',
            'somaServicos'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Caixa::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = "Caixa do usuário " . $item->usuario->name;
            $item->delete();
            __createLog(request()->empresa_id, 'Caixa', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Caixa removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Caixa', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function list()
    {

        $data = Caixa::where('empresa_id', request()->empresa_id)
        ->orderBy('id', 'desc')->get();

        return view('caixa.list', compact('data'));
    }

    public function fechar(Request $request)
    {
        $item = Caixa::findOrFail($request->caixa_id);
        try {
            $item->status = 0;
            $item->valor_fechamento = $request->valor_fechamento;
            $item->valor_dinheiro = $request->valor_dinheiro ? __convert_value_bd($request->valor_dinheiro) : 0;
            $item->valor_cheque = $request->valor_cheque ? __convert_value_bd($request->valor_cheque) : 0;
            $item->valor_outros = $request->valor_outros ? __convert_value_bd($request->valor_outros) : 0;
            $item->observacao .= " " . $request->observacao ?? '';
            $item->data_fechamento = date('Y-m-d h:i:s');
            $item->save();

            $descricaoLog = $item->usuario->name . " | CAIXA FECHADO - abertura: " . __data_pt($item->created_at) . " - fechamento: " . __data_pt($item->data_fechamento);
            __createLog($request->empresa_id, 'Caixa', 'editar', $descricaoLog);
            session()->flash('flash_success', 'Caixa Fechado');
        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            __createLog($request->empresa_id, 'Caixa', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível fechar');
        }
        return redirect()->route('caixa.list');
    }


    public function imprimir($id)
    {
        $item = Caixa::findOrFail($id);

        $config = Empresa::where('id', request()->empresa_id)->first();
        $nfce = Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->where('tipo_pagamento', '!=', '06')
        ->get();
        $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->get();
        $ordens = OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->get();

        $compras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->get();

        $data = $this->agrupaDados($nfce, $nfe, $ordens, $compras);
        $somaTiposPagamento = $this->somaTiposPagamento($data);

        $usuario = User::findOrFail(Auth::user()->id);

        $sangrias = SangriaCaixa::where('caixa_id', $item->id)->get();

        $suprimentos = SuprimentoCaixa::where('caixa_id', $item->id)->get();
        $somaServicos = ItemServicoNfce::join('nfces', 'nfces.id', '=', 'item_servico_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)->where('nfces.caixa_id', $item->id)
        ->sum('sub_total') + 
        OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->sum('valor');

        $totalVendas = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->join('fatura_nves', 'fatura_nves.nfe_id', '=', 'nves.id')
        ->sum('total');

        $totalVendas +=  Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->where('tipo_pagamento', '!=', '06')
        ->sum('total');

        $totalCompras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->sum('total');

        $produtos = $this->totalizaProdutos($data);
        $p = view('caixa.imprimir', compact(
            'item',
            'data',
            'usuario',
            'somaTiposPagamento',
            'config',
            'sangrias',
            'somaServicos',
            'suprimentos',
            'totalCompras',
            'totalVendas',
            'produtos'
        ));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de caixa.pdf", array("Attachment" => false));
    }

    public function imprimir80($id)
    {
        $item = Caixa::findOrFail($id);
        $config = Empresa::where('id', request()->empresa_id)->first();
        $nfce = Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->where('tipo_pagamento', '!=', '06')
        ->get();

        $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        ->where('finNFe', 1)
        ->get();
        $ordens = OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->get();

        $compras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->get();

        $data = $this->agrupaDados($nfce, $nfe, $ordens, $compras);
        $somaTiposPagamento = $this->somaTiposPagamento($data);

        $usuario = User::findOrFail(Auth::user()->id);

        $sangrias = SangriaCaixa::where('caixa_id', $item->id)->get();

        $totalVendas = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->join('fatura_nves', 'fatura_nves.nfe_id', '=', 'nves.id')
        ->sum('total');

        $totalVendas += Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->where('tipo_pagamento', '!=', '06')
        ->sum('total');

        $totalCompras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->sum('total');

        $suprimentos = SuprimentoCaixa::where('caixa_id', $item->id)->get();
        $somaServicos = ItemServicoNfce::join('nfces', 'nfces.id', '=', 'item_servico_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)->where('nfces.caixa_id', $item->id)
        ->sum('sub_total') + 
        OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->sum('valor');

        $produtos = $this->totalizaProdutos($data);
        $p = view('caixa.imprimir_80', compact(
            'item',
            'data',
            'usuario',
            'totalVendas',
            'totalCompras',
            'somaTiposPagamento',
            'config',
            'sangrias',
            'somaServicos',
            'suprimentos',
            'produtos'
        ));
        $height = 250;
        $height += sizeof($data)*32;
        $height += sizeof($produtos)*30;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper([0,0,204,$height]);
        $pdf = $domPdf->render();
        $domPdf->stream("Relatório de caixa.pdf", array("Attachment" => false));

    }

    private function totalizaProdutos($vendas){
        $produtos = [];
        $produtos_id = [];
        foreach($vendas as $v){
            foreach($v->itens as $item){
                if(!in_array($item->produto_id, $produtos_id)){
                    $quantidade = $item->quantidade;
                    if($item->produto->unidade == 'UN' || $item->produto->unidade == 'UNID'){
                        $quantidade = number_format($item->quantidade, 0);
                    }
                    $p = [
                        'id' => $item->produto->id,
                        'nome' => $item->produto->nome,
                        'quantidade' => $quantidade,
                        'valor_venda' => $item->produto->valor_unitario,
                        'valor_compra' => $item->produto->valor_compra
                    ];
                    array_push($produtos, $p);
                    array_push($produtos_id, $item->produto_id);
                }else{
                    //atualiza
                    for($i=0; $i<sizeof($produtos); $i++){
                        if($produtos[$i]['id'] == $item->produto_id){
                            $produtos[$i]['quantidade'] += $item->quantidade;

                            if($item->produto->unidade == 'UN' || $item->produto->unidade == 'UNID'){
                                $produtos[$i]['quantidade'] = number_format($produtos[$i]['quantidade'], 0);
                            }else{
                                $produtos[$i]['quantidade'] = number_format($produtos[$i]['quantidade'], 3);
                            }
                        }
                    }
                }
            }
        }

        return $produtos;
    }

    public function fecharConta($id){
        $item = Caixa::findOrFail($id);

        $somaTiposPagamento = [];
        $contas = [];
        $nfce = Nfce::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)
        ->where('estado', '!=', 'cancelado')
        // ->where('tipo_pagamento', '!=', '06')
        ->get();
        $nfe = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 1)
        ->where('finNFe', 1)
        ->where('estado', '!=', 'cancelado')
        ->get();

        $ordens = OrdemServico::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)
        ->get();

        $pagar = ContaPagar::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->get();
        $receber = ContaReceber::where('empresa_id', request()->empresa_id)->where('caixa_id', $item->id)->get();

        $compras = Nfe::where('empresa_id',  request()->empresa_id)->where('caixa_id', $item->id)->where('tpNF', 0)
        ->where('orcamento', 0)
        ->where('finNFe', 1)
        ->get();

        $data = $this->agrupaDados($nfce, $nfe, $ordens, $compras);
        $somaTiposPagamento = $this->somaTiposPagamento($data);
        $contasEmpresa = ContaEmpresa::where('empresa_id', request()->empresa_id)
        // ->where('local_id', $item->local_id)
        ->where('status', 1)->get();

        return view('caixa.fechar_lista', compact('item', 'somaTiposPagamento', 'contasEmpresa'));
    }

    public function fecharTiposPagamento(Request $request, $id){
        $item = Caixa::findOrFail($id);
        $item->status = 0;
        $item->data_fechamento = date('Y-m-d h:i:s');
        try{
            $result = DB::transaction(function () use ($request, $item) {

                for($i=0; $i<sizeof($request->conta_empresa_id); $i++){
                    $data = [
                        'conta_id' => $request->conta_empresa_id[$i],
                        'descricao' => "Fechamento de caixa - " . ($request->descricao[$i] ? $request->descricao[$i] : ""),
                        'tipo_pagamento' => $request->tipo_pagamento[$i],
                        'valor' => __convert_value_bd($request->valor[$i]),
                        'caixa_id' => $item->id,
                        'tipo' => 'entrada'
                    ];
                    $itemContaEmpresa = ItemContaEmpresa::create($data);
                    $this->util->atualizaSaldo($itemContaEmpresa);
                }

                return true;
            });

            $item->save();
            session()->flash('flash_success', 'Caixa fechado com sucesso!');
        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('caixa.list');
    }

    public function abertosEmpresa(Request $request){
        $data = Caixa::where('empresa_id', $request->empresa_id)->where('status', 1)->get();
        return view('caixa.abertos', compact('data'));
    }
}
