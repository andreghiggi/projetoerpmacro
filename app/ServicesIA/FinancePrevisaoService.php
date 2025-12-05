<?php

namespace App\ServicesIA;

use App\Models\ContaPagar;
use App\Models\ContaReceber;
use Carbon\Carbon;

class FinancePrevisaoService
{
    public function previsoes(int $empresaId)
    {
        $dados = $this->dados6Meses($empresaId);

        $receitas = array_column($dados, 'receber');
        $despesas = array_column($dados, 'pagar');
        $lucros   = array_column($dados, 'lucro');

        return [
            'previsao_receita' => $this->preverProximoValor($receitas),
            'previsao_despesa' => $this->preverProximoValor($despesas),
            'previsao_lucro'   => $this->preverProximoValor($lucros),
            'tendencia_lucro'  => $this->calcularTendencia($lucros),
            'historico'        => $dados, // para gráficos se quiser
        ];
    }

    private function dados6Meses(int $empresaId)
    {
        $ret = [];

        for ($i = 5; $i >= 0; $i--) {
            $data = now()->subMonths($i);

            $mes = (int)$data->format('m');
            $ano = (int)$data->format('Y');

            $receber = $this->totalReceberMes($empresaId, $mes, $ano);
            $pagar   = $this->totalPagarMes($empresaId, $mes, $ano);

            $ret[] = [
                'mes' => $data->translatedFormat('M'),
                'receber' => $receber,
                'pagar' => $pagar,
                'lucro' => $receber - $pagar,
            ];
        }

        return $ret;
    }

    private function totalPagarMes(int $empresaId, int $mes, int $ano): float
    {
        return (float) ContaPagar::where('empresa_id', $empresaId)
        ->whereMonth('data_vencimento', $mes)
        ->whereYear('data_vencimento', $ano)
        ->get()
        ->sum(fn($i) => $i->status == 1 ? $i->valor_pago : $i->valor_integral);
    }

    private function totalReceberMes(int $empresaId, int $mes, int $ano): float
    {
        return (float) ContaReceber::where('empresa_id', $empresaId)
        ->whereMonth('data_vencimento', $mes)
        ->whereYear('data_vencimento', $ano)
        ->get()
        ->sum(fn($i) => $i->status == 1 ? $i->valor_recebido : $i->valor_integral);
    }

    private function preverProximoValor(array $valores): float
    {
        $n = count($valores);

        if ($n < 2) {
            return $valores[$n - 1] ?? 0;
        }

        $x = range(1, $n);
        $y = $valores;

        $sumX  = array_sum($x);
        $sumY  = array_sum($y);
        $sumXY = array_sum(array_map(fn($a, $b) => $a * $b, $x, $y));
        $sumX2 = array_sum(array_map(fn($a) => $a * $a, $x));

        $b = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $a = ($sumY - $b * $sumX) / $n;

        $xPrev = $n + 1;
        return $a + $b * $xPrev;
    }

    private function calcularTendencia(array $valores): float
    {
        $inicio = $valores[0] ?? 0;
        $fim    = $valores[count($valores) - 1] ?? 0;

        if ($inicio == 0) {
            return $fim > 0 ? 100 : 0;
        }

        return (($fim - $inicio) / abs($inicio)) * 100;
    }
}
