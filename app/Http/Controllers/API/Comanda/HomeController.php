<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\FaturaNfe;
use App\Models\FaturaNfce;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Funcionario;
use App\Models\ContaReceber;
use App\Models\Localizacao;

class HomeController extends Controller
{
    public function index(Request $request){
        $empresa_id = $request->empresa_id;

        $funcionario = Funcionario::where('codigo', $request->codigo_operador)
        ->where('empresa_id', $empresa_id)->first();

        $locais = null;
        if($funcionario->usuario){
            $usuario_id = $funcionario->usuario_id;
            $locais = Localizacao::where('usuario_localizacaos.usuario_id', $usuario_id)
            ->select('localizacaos.*')
            ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
            ->where('localizacaos.status', 1)->get();
            $locais = $locais->pluck(['id']);
        }

        
        $totalProdutos = Produto::
        where('empresa_id', $empresa_id)
        ->select('produtos.*')
        ->count();

        $totalClientes = Cliente::
        where('empresa_id', $empresa_id)
        ->count();

        $somaVendas = Nfe::
        where('empresa_id', $empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->where('tpNF', 1)
        ->where('orcamento', 0)
        // ->whereDate('created_at', '>=', date('Y-m-01'))
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        // ->when($locais != null, function ($query) use ($locais) {
        //     return $query->whereIn('local_id', $locais);
        // })
        ->sum('total');

        $somaVendasPdv = Nfce::
        where('empresa_id', $empresa_id)
        ->where('estado', '!=', 'cancelado')
        // ->whereDate('created_at', '>=', date('Y-m-01'))
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        // ->when($locais != null, function ($query) use ($locais) {
        //     return $query->whereIn('local_id', $locais);
        // })
        ->sum('total');

        $somaContaReceber = ContaReceber::
        where('empresa_id', $empresa_id)
        ->where('status', 0)
        ->whereMonth('data_vencimento', date('m'))
        ->when($locais != null, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->sum('valor_integral');

        $somaVendasMeses = $this->somaVendasMeses($empresa_id);
        $somaUltimasVendas = $this->somaUltimasVendas($empresa_id);
        $somaTiposPagamento = $this->somaTiposPagamento($empresa_id);

        $data = [
            'total_produtos' => $totalProdutos,
            'total_clientes' => $totalClientes,
            'soma_vendas' => $somaVendas + $somaVendasPdv,
            'contas_receber' => $somaContaReceber,
            'soma_vendas_meses' => $somaVendasMeses,
            'soma_ultimas_vendas' => $somaUltimasVendas,
            'soma_tipos_pagamento' => $somaTiposPagamento,
            'usuario_nome' => $funcionario->usuario->name
        ];

        return response()->json($data, 200);
    }

    private function somaTiposPagamento($empresa_id){
        $data = [];
        $tiposPagamento = Nfce::tiposPagamento();
        $cores = [
            '#3F51B5', '#9C27B0', '#FF9800', '#009688', '#4CAF50', '#E91E63', '#FF5722',
            '#2196F3', '#795548', '#CDDC39', '#673AB7', '#F44336', '#00BCD4', '#8BC34A'
        ];
        $cont = 0;
        foreach($tiposPagamento as $key => $tipo){
            $valorNfe = FaturaNfe::select('fatura_nves.*')
            ->join('nves', 'nves.id', '=', 'fatura_nves.nfe_id')
            ->where('nves.empresa_id', $empresa_id)
            ->where('fatura_nves.tipo_pagamento', $key)
            ->whereMonth('nves.created_at', date('m'))
            ->whereYear('nves.created_at', date('Y'))
            ->where('nves.tpNF', 1)
            ->where('nves.orcamento', 0)
            ->where('nves.estado', '!=', 'cancelado')

            ->sum('valor');

            $valorNfce = FaturaNfce::select('fatura_nfces.*')
            ->join('nfces', 'nfces.id', '=', 'fatura_nfces.nfce_id')
            ->where('nfces.empresa_id', $empresa_id)
            ->where('fatura_nfces.tipo_pagamento', $key)
            ->whereMonth('nfces.created_at', date('m'))
            ->whereYear('nfces.created_at', date('Y'))
            ->where('nfces.estado', '!=', 'cancelado')
            ->sum('valor');
            $tipo = $tiposPagamento[$key];
            if($tipo == 'Pagamento Instantâneo (PIX)'){
                $tipo = 'PIX';
            }
            if($valorNfce + $valorNfe > 0){
                $data[] = [
                    'label' => $tipo,
                    'value' => $valorNfe + $valorNfce,
                    'color' => $cores[$cont]
                ];
                $cont++;
            }
        }
        return $data;
    }

    private function somaUltimasVendas($empresa_id){
        $dia = date('Y-m-d');
        $data = [];

        $total = 0;

        $cores = [
            '#3F51B5', '#9C27B0', '#FF9800', '#009688', '#4CAF50', '#E91E63', '#FF5722'
        ];
        for($i=0; $i<7; $i++){
            $somaVendas = Nfe::
            where('empresa_id', $empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->where('tpNF', 1)
            ->where('orcamento', 0)
            ->whereDate('created_at', $dia)
            ->sum('total');

            $somaVendasPdv = Nfce::
            where('empresa_id', $empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereDate('created_at', $dia)
            ->sum('total');

            $total += $somaVendas + $somaVendasPdv;

            if($somaVendas + $somaVendasPdv > 0){
                $data[] = [
                    'label' => \Carbon\Carbon::parse($dia)->format('d/m'),
                    'value' => $somaVendas + $somaVendasPdv,
                    'color' => $cores[$i]
                ];
            }

            $dia = date('Y-m-d', strtotime('-1 day', strtotime($dia)));
        }

        $retorno = [
            'total' => $total,
            'data' => $data
        ];
        return $retorno;
    }

    private function somaVendasMeses($empresa_id){
        $data = [];
        $mesAtual = (int)date('m')-1;
        $cont = 0;
        $i = 0;
        $meses = 4;
        $ano = date('Y');
        while($cont < $meses){
            if(isset($this->meses()[$mesAtual])){
                $mes = $this->meses()[$mesAtual];
            }else{
                $mes = 'Dezembro';
                $mesAtual = 12;
                $ano--;
            }

            $totalNfe = Nfe::where('empresa_id', $empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', $mesAtual+1)
            ->whereYear('created_at', $ano)
            ->where('tpNF', 1)
            ->where('orcamento', 0)
            ->sum('total');

            $totalNfce = Nfce::where('empresa_id', $empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', $mesAtual+1)
            ->whereYear('created_at', $ano)
            ->sum('total');

            // $totalNfce = 0;
            $mesAtual--;

            $cont++;
            $data[] = [
                'mes' => $mes,
                'valor' => $totalNfce + $totalNfe
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    private function meses(){
        return [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro',
        ];
    }
}
