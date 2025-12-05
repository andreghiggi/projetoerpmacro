<?php

namespace App\Utils;

use App\Models\ContaPagar;
use App\Models\ContaReceber;
use Carbon\Carbon;

class FinanceInsightUtil
{
    public function gerarInsights($empresaId)
    {
        $hoje = Carbon::today();

        // Totais do mês
        $totalPagarMes = ContaPagar::whereMonth('data_vencimento', $hoje->month)
        ->where('empresa_id', $empresaId)
        ->sum('valor_integral');

        $totalReceberMes = ContaReceber::whereMonth('data_vencimento', $hoje->month)
        ->where('empresa_id', $empresaId)
        ->sum('valor_integral');

        // Mês anterior
        $totalPagarAnterior = ContaPagar::whereMonth('data_vencimento', $hoje->subMonth()->month)
        ->where('empresa_id', $empresaId)
        ->sum('valor_integral');

        $totalReceberAnterior = ContaReceber::whereMonth('data_vencimento', $hoje->month)
        ->where('empresa_id', $empresaId)
        ->sum('valor_integral');

        // Categoria que mais pesa nas despesas
        $categoriaTop = ContaPagar::selectRaw('categoria_conta_id, SUM(valor_integral) as total')
        ->whereMonth('data_vencimento', now()->month)
        ->where('empresa_id', $empresaId)
        ->groupBy('categoria_conta_id')
        ->orderByDesc('total')
        ->first();

        $insights = [];

        // Insight 1 – Saldo geral do mês
        $insights[] = $this->insightSaldo($totalReceberMes, $totalPagarMes);

        // Insight 2 – Tendência (comparação com mês anterior)
        $insights[] = $this->insightTendencia($totalPagarMes, $totalPagarAnterior, "despesas");
        $insights[] = $this->insightTendencia($totalReceberMes, $totalReceberAnterior, "receitas");

        // Insight 3 – Categoria mais pesada
        if ($categoriaTop) {
            if($categoriaTop->categoriaConta){
                $insights[] = "A categoria com maior impacto nas suas despesas este mês é <strong>" . 
                optional($categoriaTop->categoriaConta)->nome . 
                "</strong> com R$ " . number_format($categoriaTop->total, 2, ',', '.') . ".";
            }
        }

        // Insight 4 – Contas vencidas
        $vencidas = ContaPagar::where('empresa_id', $empresaId)
        ->where('status', 0)
        ->whereDate('data_vencimento', '<', now())
        ->sum('valor_integral');

        if ($vencidas > 0) {
            $insights[] = "Você possui <strong>R$ " . number_format($vencidas, 2, ',', '.') . 
            " em contas vencidas</strong> que precisam de atenção.";
        }

        return $insights;
    }

    private function insightSaldo($receber, $pagar)
    {
        $saldo = $receber - $pagar;

        if ($saldo > 0) {
            return "Seu saldo do mês está <strong>positivo</strong> em R$ " . number_format($saldo, 2, ',', '.') . ".";
        } else {
            return "Seu saldo do mês está <strong>negativo</strong> em R$ " . number_format(abs($saldo), 2, ',', '.') . ".";
        }
    }

    private function insightTendencia($atual, $anterior, $tipo)
    {
        if ($anterior == 0) {
            return ucfirst($tipo) . " iniciaram este mês sem histórico anterior.";
        }

        $percentual = (($atual - $anterior) / $anterior) * 100;

        if ($percentual > 0) {
            return ucfirst($tipo) . " aumentaram <strong>" . number_format($percentual, 1, ',', '.') . "%</strong> em relação ao mês anterior.";
        } else {
            return ucfirst($tipo) . " diminuíram <strong>" . number_format(abs($percentual), 1, ',', '.') . "%</strong> em relação ao mês anterior.";
        }
    }
}
