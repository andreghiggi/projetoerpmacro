<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaConta;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\ServicesIA\FinancePrevisaoService;

class FinanceiroDashboardController extends Controller
{
    public function __construct(){
        $this->middleware('permission:caixa_view', ['only' => ['show', 'index']]);
    }

    public function index(Request $request){

        $queryPagar = ContaPagar::query();
        $queryReceber = ContaReceber::query();

        $queryPagar->where('empresa_id', $request->empresa_id);
        $queryReceber->where('empresa_id', $request->empresa_id);
        
        $dados = [
            // PAGAR
            'pagarHoje' => (clone $queryPagar)->whereDate('data_vencimento', today())
            ->where('status', 0)->sum('valor_integral'),

            'pagarMes' => (clone $queryPagar)->whereMonth('data_vencimento', now()->month)
            ->where('status', 0)->sum('valor_integral'),

            'pagarVencidas' => (clone $queryPagar)->where('status', 0)
            ->whereDate('data_vencimento', '<', today())->sum('valor_integral'),

            // RECEBER
            'receberHoje' => (clone $queryReceber)->whereDate('data_vencimento', today())
            ->where('status', 0)->sum('valor_integral'),

            'receberMes' => (clone $queryReceber)->whereMonth('data_vencimento', now()->month)
            ->where('status', 0)->sum('valor_integral'),

            'receberVencidas' => (clone $queryReceber)->where('status', 0)
            ->whereDate('data_vencimento', '<', today())->sum('valor_integral'),

            'recebimentosParciais' => (clone $queryReceber)->where('recebimento_parcial', 1)
            ->sum('valor_recebido'),

            // SALDO
            'saldoMes' => $this->saldoMensal($queryPagar, $queryReceber),

        ];

        $dadosPagar = $this->contasPagarCategoria();
        $dadosReceber = $this->contasReceberCategoria();
        $lucroGrafico = $this->dadosLucro();

        $grafico = $this->dadosGrafico();

        $insights = app(\App\Utils\FinanceInsightUtil::class)
        ->gerarInsights(request()->empresa_id);

        $inadimplentes = $this->inadimplentes();
        $dadosPagarVencidos = $this->pagarVencidos();

        $prevService = app(FinancePrevisaoService::class);
        $previsoes = $prevService->previsoes(request()->empresa_id);

        return view('financeiro.dashboard', compact('dados', 'grafico', 'dadosPagar', 'dadosReceber', 'lucroGrafico', 'insights', 'inadimplentes',
            'dadosPagarVencidos', 'previsoes'));
    }

    private function inadimplentes()
    {
        $registros = ContaReceber::where('empresa_id', request()->empresa_id)
        ->where('cliente_id', '!=', null)
        ->where('status', 0)
        ->whereDate('data_vencimento', '<', now())
        ->with('cliente:id,razao_social,cpf_cnpj')
        ->get();

        $agrupado = $registros
        ->groupBy('cliente_id')
        ->map(function ($itens, $clienteId) {
            return [
                'cliente_id' => $clienteId,
                'cliente' => $itens->first()->cliente,
                'qtd_titulos' => $itens->count(),
                'total_vencido' => $itens->sum('valor_integral'),
                'dias_atraso' => $itens->max(fn($item) => now()->diffInDays($item->data_vencimento)),
            ];
        })
        ->sortByDesc('total_vencido')
        ->values();

        return [
            'inadimplentes' => $agrupado,
            'totalInadimplentes' => $agrupado->count(),
            'valorTotalInadimplentes' => $agrupado->sum('total_vencido'),
        ];
    }

    private function pagarVencidos()
    {
        $registros = ContaPagar::where('empresa_id', request()->empresa_id)
        ->where('status', 0)
        ->whereDate('data_vencimento', '<', now())
        ->with('fornecedor:id,razao_social,cpf_cnpj')
        ->get();

        $agrupado = $registros
        ->groupBy('fornecedor_id')
        ->map(function ($itens, $fornecedorId) {
            return [
                'fornecedor_id' => $fornecedorId,
                'fornecedor' => $itens->first()->fornecedor,
                'qtd_titulos' => $itens->count(),
                'total_vencido' => $itens->sum('valor_integral'),
                'dias_atraso' => $itens->max(fn($item) => now()->diffInDays($item->data_vencimento)),
            ];
        })
        ->sortByDesc('total_vencido')
        ->values();

        return [
            'pagarVencidos' => $agrupado,
            'totalFornecedoresVencidos' => $agrupado->count(),
            'valorTotalPagarVencido' => $agrupado->sum('total_vencido'),
        ];
    }


    private function dadosLucro()
    {
        $labels = [];
        $lucros = [];

        for ($i = 5; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $mesNum = $data->format('m');

            $nomesMes = [
                '01' => 'Janeiro',
                '02' => 'Fevereiro',
                '03' => 'Março',
                '04' => 'Abril',
                '05' => 'Maio',
                '06' => 'Junho',
                '07' => 'Julho',
                '08' => 'Agosto',
                '09' => 'Setembro',
                '10' => 'Outubro',
                '11' => 'Novembro',
                '12' => 'Dezembro',
            ];

            $mesNome = $nomesMes[$mesNum];

            $pagar = ContaPagar::whereMonth('data_vencimento', $mesNum)
            ->whereYear('data_vencimento', $data->year)
            ->where('empresa_id', request()->empresa_id)
            ->get()
            ->sum(function ($item) {
                return $item->status == 1
                ? (float) $item->valor_pago
                : (float) $item->valor_integral;
            });

            $receber = ContaReceber::whereMonth('data_vencimento', $mesNum)
            ->whereYear('data_vencimento', $data->year)
            ->where('empresa_id', request()->empresa_id)
            ->get()
            ->sum(function ($item) {
                return $item->status == 1
                ? (float) $item->valor_recebido
                : (float) $item->valor_integral;
            });

            $lucro = $receber - $pagar;

            $labels[] = ucfirst($mesNome);
            $lucros[] = round($lucro, 2);
        }

        return [
            'labels' => $labels,
            'lucros' => $lucros
        ];
    }

    private function contasReceberCategoria(){

        $categoriasReceber = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('tipo', 'receber')
        ->where('status', 1)
        ->get();

        $dadosReceber = [];

        foreach ($categoriasReceber as $cat) {
            $total = ContaReceber::where('empresa_id', request()->empresa_id)
            ->where('categoria_conta_id', $cat->id)
            ->where('status', 0)
            ->whereMonth('data_vencimento', date('m'))
            ->sum('valor_integral');

            $dadosReceber[] = [
                'categoria' => $cat->nome,
                'valor' => (float)$total
            ];
        }
        return $dadosReceber;
    }

    private function contasPagarCategoria(){
        $categoriasPagar = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('tipo', 'pagar')
        ->where('status', 1)
        ->get();

        $dadosPagar = [];

        foreach ($categoriasPagar as $cat) {
            $total = ContaPagar::where('empresa_id', request()->empresa_id)
            ->where('categoria_conta_id', $cat->id)
            ->where('status', 0)
            ->whereMonth('data_vencimento', date('m'))
            ->sum('valor_integral');

            $dadosPagar[] = [
                'categoria' => $cat->nome,
                'valor' => (float)$total
            ];
        }
        return $dadosPagar;
    }

    private function saldoMensal($pagarQuery, $receberQuery)
    {
        $pagar = (clone $pagarQuery)->whereMonth('data_vencimento', now()->month)
        ->sum('valor_integral');

        $receber = (clone $receberQuery)->whereMonth('data_vencimento', now()->month)
        ->sum('valor_integral');

        return $receber - $pagar;
    }

    private function dadosGrafico()
    {
        $meses = collect([]);
        $pagar = collect([]);
        $receber = collect([]);

        $nomesMes = [
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro',
        ];

        for ($i = 11; $i >= 0; $i--) {
            $mes = now()->subMonths($i)->format('m');
            $mesNumero = now()->subMonths($i)->format('m');  
            $mesLabel = $nomesMes[$mesNumero];

            $totalPagar = ContaPagar::whereMonth('data_vencimento', $mes)
            ->whereYear('data_vencimento', now()->year)
            ->where('empresa_id', request()->empresa_id)
            ->sum('valor_integral');

            $totalReceber = ContaReceber::whereMonth('data_vencimento', $mes)
            ->whereYear('data_vencimento', now()->year)
            ->where('empresa_id', request()->empresa_id)
            ->sum('valor_integral');

            $meses->push($mesLabel);
            $pagar->push($totalPagar);
            $receber->push($totalReceber);
        }

        return [
            'labels' => $meses,
            'pagar' => $pagar,
            'receber' => $receber
        ];
    }

}
