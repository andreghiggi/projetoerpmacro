<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\Cte;
use App\Models\Empresa;
use App\Models\Mdfe;
use App\Models\PlanoEmpresa;
use Illuminate\Support\Facades\Auth;
use App\Models\ContratoEmpresa;
use App\Models\ContratoConfig;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $empresa_id = 1;

    public function __construct()
    {
        $this->middleware('validaCashBack');
    }

    public function homeContador(){

    }

    public function index()
    {

        $totalEmitidoMes = 0;
        $plano = PlanoEmpresa::where('empresa_id', request()->empresa_id)
        ->orderBy('data_expiracao', 'desc')
        ->first();

        $msgPlano = "";
        $botaoContrato = "";

        if($plano == null){
            $msgPlano = "Empresa sem plano atribuído!";
        }

        if($plano != null){
            if(date('Y-m-d') > $plano->data_expiracao){
                $msgPlano = "Plano expirado!";
            }
        }

        $empresa = Empresa::find(request()->empresa_id);
        if($empresa != null){
            if($empresa->receber_com_boleto){
                $msgPlano = "";
            }

            $contrato = ContratoEmpresa::where('empresa_id', $empresa->id)
            ->first();

            $configContrato = ContratoConfig::first();

            if($configContrato != null && $contrato != null && !$contrato->assinado){
                $dif = strtotime(date("Y-m-d H:i:s")) - strtotime($contrato->created_at);
                $dif = floor($dif / (60 * 60 * 24 * 30));
                $dif = $configContrato->limite_dias_assinar - $dif;
                $botaoContrato = "Assinar contrato de uso, faltam $dif dias";
            }
        }

        if(__isMaster()){
            return redirect()->route('empresas.index');
        }

        if(__isSuporte()){
            return redirect()->route('suporte.index');
        }

        $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->where('tpNF', 1)
        ->whereMonth('created_at', date('m'))
        ->sum('total');

        $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->sum('total');

        $totalEmitidoMes = $totalNfce + $totalNfe;

        $totalNfeCount = Nfe::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->where('tpNF', 1)
        ->whereMonth('created_at', date('m'))
        ->count('id');

        $totalNfceCount = Nfce::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->count('id');

        $totalCteCount = Cte::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->count('id');

        $totalMdfeCount = Mdfe::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado_emissao', 'aprovado')->orWhere('estado_emissao', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->count('id');

        if($empresa == null){
            return redirect()->route('config.index');
        }

        $totalVendasMes = 0;
        $mesAtual = date('m');
        $mes = $this->meses()[$mesAtual-1];

        $somaVendasMesesAnteriores = $this->somaVendasMesesAnteriores();
        $totalVendasMes = $this->somaVendasMes();

        $totalComprasMes = $this->somaComprasMes();
        $somaComprasMesesAnteriores = $this->somaComprasMesesAnteriores();

        return view('home', 
            compact('empresa', 'totalEmitidoMes', 'totalNfeCount', 'totalNfceCount', 'msgPlano', 'totalCteCount', 
                'totalMdfeCount', 'totalVendasMes', 'mes', 'somaVendasMesesAnteriores', 'totalComprasMes',
                'somaComprasMesesAnteriores', 'botaoContrato'));
    }

    private function somaComprasMes(){
        $totalCompra = Nfe::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->whereMonth('created_at', date('m'))
        ->where('tpNF', 0)
        ->sum('total');

        return $totalCompra;
    }

    private function somaVendasMes(){
        $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->whereMonth('created_at', date('m'))
        ->where('tpNF', 1)
        ->sum('total');

        $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->whereMonth('created_at', date('m'))
        ->sum('total');

        return $totalNfce + $totalNfe;
    }

    private function somaComprasMesesAnteriores(){
        $data = [];
        $meses = 3;
        $mesAtual = date('m')-2;

        $cont = 0;
        $i = 0;
        while($cont < $meses){
            if(isset($this->meses()[$mesAtual])){
                $mes = $this->meses()[$mesAtual];
            }else{
                $mes = 'Dezembro';
                $mesAtual = 11;
            }

            $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', $mesAtual+1)
            ->where('tpNF', 0)
            ->sum('total');

            $mesAtual--;
            $cont++;
            $data[$mes] = $totalNfe;
        }

        return $data;
    }

    private function somaVendasMesesAnteriores(){
        $data = [];
        $meses = 3;
        $mesAtual = date('m')-2;

        $cont = 0;
        $i = 0;
        while($cont < $meses){
            if(isset($this->meses()[$mesAtual])){
                $mes = $this->meses()[$mesAtual];
            }else{
                $mes = 'Dezembro';
                $mesAtual = 11;
            }

            $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', $mesAtual+1)
            ->sum('total');

            $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', $mesAtual+1)
            ->sum('total');

            $mesAtual--;
            $cont++;
            $data[$mes] = $totalNfce + $totalNfe;
        }

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

    public function nfe(Request $request)
    {
        $empresa_id = $request->empresa;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;

        $data = NFe::orderBy("id", "desc")
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data_emissao', '<=', $end_date);
        })
        ->when(!empty($empresa_id), function ($query) use ($empresa_id) {
            return $query->where('empresa_id', $empresa_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->paginate(30);

        $empresa = null;
        if($empresa_id){
            $empresa = Empresa::findOrFail($empresa_id);
        }

        return view('nfe.all', compact('data', 'empresa'));
    }

    public function nfce(Request $request)
    {
        $empresa_id = $request->empresa;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;

        $data = Nfce::orderBy("id", "desc")
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data_emissao', '<=', $end_date);
        })
        ->when(!empty($empresa_id), function ($query) use ($empresa_id) {
            return $query->where('empresa_id', $empresa_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->paginate(30);

        $empresa = null;
        if($empresa_id){
            $empresa = Empresa::findOrFail($empresa_id);
        }

        return view('nfce.all', compact('data', 'empresa'));
    }

    public function cte(Request $request)
    {
        $empresa_id = $request->empresa;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;

        $data = Cte::orderBy("id", "desc")
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($empresa_id), function ($query) use ($empresa_id) {
            return $query->where('empresa_id', $empresa_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->paginate(30);

        $empresa = null;
        if($empresa_id){
            $empresa = Empresa::findOrFail($empresa_id);
        }

        return view('cte.all', compact('data', 'empresa'));
    }

    public function mdfe(Request $request)
    {
        $empresa_id = $request->empresa;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;

        $data = Mdfe::orderBy("id", "desc")
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($empresa_id), function ($query) use ($empresa_id) {
            return $query->where('empresa_id', $empresa_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->paginate(30);

        $empresa = null;
        if($empresa_id){
            $empresa = Empresa::findOrFail($empresa_id);
        }

        return view('mdfe.all', compact('data', 'empresa'));
    }
}
