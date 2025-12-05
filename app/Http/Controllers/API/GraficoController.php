<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Cte;
use App\Models\Mdfe;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Localizacao;

class GraficoController extends Controller
{
    public function cardsConta(Request $request){
        $empresa_id = $request->empresa_id;
        $periodo_inicial = $request->periodo_inicial;
        $periodo_final = $request->periodo_final;


        $recebidas = ContaReceber::where('empresa_id', $empresa_id)
        ->where('status', 1)
        ->when($periodo_inicial, function ($query) use ($periodo_inicial){
            return $query->whereDate('data_vencimento', '>=', $periodo_inicial);
        })
        ->when($periodo_final, function ($query) use ($periodo_final){
            return $query->whereDate('data_vencimento', '<=', $periodo_final);
        })
        ->sum('valor_recebido');

        $aReceber = ContaReceber::where('empresa_id', $empresa_id)
        ->where('status', 0)
        ->when($periodo_inicial, function ($query) use ($periodo_inicial){
            return $query->whereDate('data_vencimento', '>=', $periodo_inicial);
        })
        ->when($periodo_final, function ($query) use ($periodo_final){
            return $query->whereDate('data_vencimento', '<=', $periodo_final);
        })
        ->sum('valor_integral');


        $pagas = ContaPagar::where('empresa_id', $empresa_id)
        ->where('status', 1)
        ->when($periodo_inicial, function ($query) use ($periodo_inicial){
            return $query->whereDate('data_vencimento', '>=', $periodo_inicial);
        })
        ->when($periodo_final, function ($query) use ($periodo_final){
            return $query->whereDate('data_vencimento', '<=', $periodo_final);
        })
        ->sum('valor_pago');

        $aPagar = ContaPagar::where('empresa_id', $empresa_id)
        ->where('status', 0)
        ->when($periodo_inicial, function ($query) use ($periodo_inicial){
            return $query->whereDate('data_vencimento', '>=', $periodo_inicial);
        })
        ->when($periodo_final, function ($query) use ($periodo_final){
            return $query->whereDate('data_vencimento', '<=', $periodo_final);
        })
        ->sum('valor_integral');

        $data = [
            'recebidas' => $recebidas,
            'aReceber' => $aReceber,
            'pagas' => $pagas,
            'aPagar' => $aPagar,
        ];

        return response()->json($data, 200);
    }

    public function dadosCards(Request $request){
        $periodo = $request->periodo;
        $empresa_id = $request->empresa_id;
        $usuario_id = $request->usuario_id;
        $local_id = $request->local_id;

        $locais = Localizacao::where('usuario_localizacaos.usuario_id', $usuario_id)
        ->select('localizacaos.*')
        ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
        ->where('localizacaos.status', 1)->get();
        $locais = $locais->pluck(['id']);

        $somaVendas = Nfe::
        where('empresa_id', $empresa_id)
        ->when($periodo == 1, function ($query) {
            return $query->whereDate('created_at', date('Y-m-d'));
        })
        ->when($periodo == 7, function ($query) {
            return $query->whereRaw('WEEK(created_at) = ' . (date('W')-1));
        })
        ->when($periodo == 30, function ($query) {
            return $query->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
        })
        ->when($periodo == 365, function ($query) {
            return $query->whereYear('created_at', date('Y'));
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->where('estado', '!=', 'cancelado')
        ->where('tpNF', 1)
        ->where('orcamento', 0)
        ->sum('total');

        $somaVendasPdv = Nfce::
        where('empresa_id', $empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->when($periodo == 1, function ($query) {
            return $query->whereDate('created_at', date('Y-m-d'));
        })
        ->when($periodo == 7, function ($query) {
            return $query->whereRaw('WEEK(created_at) = ' . (date('W')-1));
        })
        ->when($periodo == 30, function ($query) {
            return $query->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
        })
        ->when($periodo == 365, function ($query) {
            return $query->whereYear('created_at', date('Y'));
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->sum('total');

        $totalClientes = Cliente::
        where('empresa_id', $empresa_id)
        ->when($periodo == 1, function ($query) {
            return $query->whereDate('created_at', date('Y-m-d'));
        })
        ->when($periodo == 7, function ($query) {
            return $query->whereRaw('WEEK(created_at) = ' . (date('W')-1))->whereYear('created_at', date('Y'));
        })
        ->when($periodo == 30, function ($query) {
            return $query->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
        })
        ->when($periodo == 365, function ($query) {
            return $query->whereYear('created_at', date('Y'));
        })
        ->count('id');

        $totalProdutos = Produto::
        where('empresa_id', $empresa_id)
        ->select('produtos.*')
        ->when($periodo == 1, function ($query) {
            return $query->whereDate('produtos.created_at', date('Y-m-d'));
        })
        ->when($periodo == 7, function ($query) {
            return $query->whereRaw('WEEK(produtos.created_at) = ' . (date('W')-1))->whereYear('produtos.created_at', date('Y'));
        })
        ->when($periodo == 30, function ($query) {
            return $query->whereMonth('produtos.created_at', date('m'))->whereYear('produtos.created_at', date('Y'));
        })
        ->when($periodo == 365, function ($query) {
            return $query->whereYear('produtos.created_at', date('Y'));
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        ->count('produtos.id');

        $somaCompras = Nfe::
        where('empresa_id', $empresa_id)
        ->when($periodo == 1, function ($query) {
            return $query->whereDate('created_at', date('Y-m-d'));
        })
        ->when($periodo == 7, function ($query) {
            return $query->whereRaw('WEEK(created_at) = ' . (date('W')-1));
        })
        ->when($periodo == 30, function ($query) {
            return $query->whereMonth('created_at', date('m'));
        })
        ->when($periodo == 365, function ($query) {
            return $query->whereYear('created_at', date('Y'));
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->where('tpNF', 0)
        ->sum('total');

        $somaContaReceber = ContaReceber::
        where('empresa_id', $empresa_id)
        ->where('status', 0)
        ->when($periodo == 1, function ($query) {
            return $query->whereDate('data_vencimento', date('Y-m-d'));
        })
        ->when($periodo == 7, function ($query) {
            return $query->whereRaw('WEEK(data_vencimento) = ' . (date('W')-1));
        })
        ->when($periodo == 30, function ($query) {
            return $query->whereMonth('data_vencimento', date('m'));
        })
        ->when($periodo == 365, function ($query) {
            return $query->whereYear('data_vencimento', date('Y'));
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->sum('valor_integral');

        $somaContaPagar = ContaPagar::
        where('empresa_id', $empresa_id)
        ->where('status', 0)
        ->when($periodo == 1, function ($query) {
            return $query->whereDate('data_vencimento', date('Y-m-d'));
        })
        ->when($periodo == 7, function ($query) {
            return $query->whereRaw('WEEK(data_vencimento) = ' . (date('W')-1));
        })
        ->when($periodo == 30, function ($query) {
            return $query->whereMonth('data_vencimento', date('m'));
        })
        ->when($periodo == 365, function ($query) {
            return $query->whereYear('data_vencimento', date('Y'));
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->sum('valor_integral');

        $data = [
            'vendas' => $somaVendas + $somaVendasPdv,
            'compras' => $somaCompras,
            'clientes' => $totalClientes,
            'produtos' => $totalProdutos,
            'contas_receber' => $somaContaReceber,
            'contas_pagar' => $somaContaPagar
        ];

        return response()->json($data, 200);
    }

    public function graficoVendasMes(Request $request)
    {

        $diaHoje = date('d');
        $mes = date('m');
        $data = [];
        for ($i = 1; $i <= $diaHoje; $i++) {
            $totalNfe = Nfe::where('empresa_id', $request->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', date('m'))
            ->whereDay('created_at', ($i < 10 ? "0$i" : $i))
            ->where('tpNF', 1)
            ->sum('total');

            $totalNfce = Nfce::where('empresa_id', $request->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', date('m'))
            ->whereDay('created_at', ($i < 10 ? "0$i" : $i))
            ->sum('total');

            array_push($data, [
                'dia' => ($i < 10 ? "0$i" : $i) . "/$mes",
                'valor' => $totalNfe + $totalNfce
            ]);
        }
        return response()->json($data, 200);
    }

    public function graficoComprasMes(Request $request)
    {

        $diaHoje = date('d');
        $mes = date('m');
        $data = [];
        for ($i = 1; $i <= $diaHoje; $i++) {
            $totalNfe = Nfe::where('empresa_id', $request->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', date('m'))
            ->whereDay('created_at', ($i < 10 ? "0$i" : $i))
            ->where('tpNF', 0)
            ->sum('total');

            array_push($data, [
                'dia' => ($i < 10 ? "0$i" : $i) . "/$mes",
                'valor' => $totalNfe
            ]);
        }
        return response()->json($data, 200);
    }

    public function graficoMes(Request $request)
    {
        $diaHoje = date('d');
        $mes = date('m');
        $data = [];
        for ($i = 1; $i <= $diaHoje; $i++) {
            $totalNfe = Nfe::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
            })
            ->whereMonth('created_at', date('m'))
            ->whereDay('created_at', ($i < 10 ? "0$i" : $i))
            ->sum('total');

            $totalNfce = Nfce::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
            })
            ->whereMonth('created_at', date('m'))
            ->whereDay('created_at', ($i < 10 ? "0$i" : $i))
            ->sum('total');

            array_push($data, [
                'dia' => ($i < 10 ? "0$i" : $i) . "/$mes",
                'valor' => $totalNfe + $totalNfce
            ]);
        }
        return response()->json($data, 200);
    }

    public function graficoMesContador(Request $request)
    {
        $diaHoje = date('d');
        $mes = date('m');
        $data = [];
        for ($i = 1; $i <= $diaHoje; $i++) {
            $totalNfe = Nfe::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
            })
            ->whereMonth('created_at', date('m'))
            ->whereDay('created_at', ($i < 10 ? "0$i" : $i))
            ->count('id');

            $totalNfce = Nfce::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
            })
            ->whereMonth('created_at', date('m'))
            ->whereDay('created_at', ($i < 10 ? "0$i" : $i))
            ->count('id');

            array_push($data, [
                'dia' => ($i < 10 ? "0$i" : $i) . "/$mes",
                'valor' => $totalNfe + $totalNfce
            ]);
        }
        return response()->json($data, 200);
    }

    public function graficoUltMeses(Request $request)
    {
        $mes = (int)date('m');
        $ano = date('Y');
        $data = [];
        for ($i = 0; $i < 4; $i++) {
            $totalNfe = Nfe::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('total');

            $totalNfce = Nfce::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('total');

            array_push($data, [
                'dia' => $this->getMes($mes - 1) . "/$ano",
                'valor' => $totalNfe + $totalNfce
            ]);

            if ($mes == 1) {
                $mes = 12;
                $ano--;
            } else {
                $mes--;
            }
        }
        return response()->json($data, 200);
    }

    private function getMes($m)
    {
        $meses = [
            'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set',
            'Out', 'Nov', 'Dez'
        ];
        return $meses[$m];
    }

    public function graficoContaReceber(Request $request)
    {
        $mes = (int)date('m');
        $ano = date('Y');
        $data = [];
        for ($i = 0; $i < 4; $i++) {
            $receber = ContaReceber::where('empresa_id', $request->empresa_id)
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_integral');

            $pendente = ContaReceber::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('status', false);
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_integral');

            $quitado = ContaReceber::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('status', true);
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_integral');

            array_push($data, [
                'dia' => $this->getMes($mes - 1) . "/$ano",
                'valor' => $receber,
                'valorPendente' => $pendente,
                'valorQuitado' => $quitado
            ]);

            if ($mes == 1) {
                $mes = 12;
                $ano--;
            } else {
                $mes--;
            }
        }
        return response()->json($data, 200);
    }

    public function graficoContaPagar(Request $request)
    {
        $mes = (int)date('m');
        $ano = date('Y');
        $data = [];
        for ($i = 0; $i < 4; $i++) {
            $pagar = ContaPagar::where('empresa_id', $request->empresa_id)
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_integral');

            $pendentes = ContaPagar::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('status', false);
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_integral');

            $quitadas = ContaPagar::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('status', true);
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_integral');

            array_push($data, [
                'dia' => $this->getMes($mes - 1) . "/$ano",
                'valor' => $pagar,
                'valorPendente' => $pendentes,
                'valorQuitado' => $quitadas
            ]);

            if ($mes == 1) {
                $mes = 12;
                $ano--;
            } else {
                $mes--;
            }
        }
        return response()->json($data, 200);
    }

    public function graficoMesCte(Request $request)
    {
        $mes = (int)date('m');
        $ano = date('Y');
        $data = [];
        for ($i = 0; $i < 4; $i++) {
            $totalNfe = Cte::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->count('id');

            array_push($data, [
                'dia' => $this->getMes($mes - 1) . "/$ano",
                'valor' => $totalNfe
            ]);

            if ($mes == 1) {
                $mes = 12;
                $ano--;
            } else {
                $mes--;
            }
        }
        return response()->json($data, 200);
    }

    public function graficoMesMdfe(Request $request)
    {
        $mes = (int)date('m');
        $ano = date('Y');
        $data = [];
        for ($i = 0; $i < 4; $i++) {
            $totalNfe = Mdfe::where('empresa_id', $request->empresa_id)
            ->where(function ($q) {
                $q->where('estado_emissao', 'aprovado')->orWhere('estado_emissao', 'cancelado');
            })
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->count('id');

            array_push($data, [
                'dia' => $this->getMes($mes - 1) . "/$ano",
                'valor' => $totalNfe
            ]);

            if ($mes == 1) {
                $mes = 12;
                $ano--;
            } else {
                $mes--;
            }
        }
        return response()->json($data, 200);
    }

}
