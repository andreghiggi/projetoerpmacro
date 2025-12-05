<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemNfe;
use App\Models\ItemNfce;
use App\Models\Produto;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\Cte;
use App\Models\Empresa;
use App\Models\Mdfe;
use App\Models\PlanoEmpresa;
use App\Models\ConfigGeral;
use App\Models\Caixa;
use App\Models\PromocaoProduto;
use Illuminate\Support\Facades\Auth;
use App\Models\ContratoEmpresa;
use App\Models\ContratoConfig;
use Illuminate\Support\Facades\DB;

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

    // public function homeContador(){

    // }

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
        ->whereYear('created_at', date('Y'))
        ->sum('total');

        $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->sum('total');

        $totalEmitidoMes = $totalNfce + $totalNfe;

        $totalNfeCount = Nfe::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->where('tpNF', 1)
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->count('id');

        $totalNfceCount = Nfce::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->count('id');

        $totalCteCount = Cte::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->count('id');

        $totalMdfeCount = Mdfe::where('empresa_id', request()->empresa_id)
        ->where(function($q) {
            $q->where('estado_emissao', 'aprovado')->orWhere('estado_emissao', 'cancelado');
        })
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
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

        $homeComponentes = [];
        $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        if($configGeral != null){
            $homeComponentes = $configGeral != null && $configGeral->home_componentes ? json_decode($configGeral->home_componentes) : [];

            if($homeComponentes == null) $homeComponentes = [];
        }

        $emPromocao = PromocaoProduto::where('promocao_produtos.status', 1)
        ->where('produtos.empresa_id', request()->empresa_id)
        ->select('promocao_produtos.*')
        ->join('produtos', 'produtos.id', '=', 'promocao_produtos.produto_id')
        ->whereDate('promocao_produtos.data_inicio', '<=', now())
        ->whereDate('promocao_produtos.data_fim', '>=', now())->count();

        $inicioSemana = \Carbon\Carbon::now()->startOfWeek();
        $fimSemana = \Carbon\Carbon::now()->endOfWeek();

        $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
        ->whereBetween('created_at', [$inicioSemana, $fimSemana])
        ->where('tpNF', 1)
        ->sum('total');

        $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
        ->whereBetween('created_at', [$inicioSemana, $fimSemana])
        ->sum('total');
        $somaSemanal = $totalNfe + $totalNfce;

        $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
        ->whereMonth('created_at', date('m'))
        ->where('tpNF', 1)
        ->whereYear('created_at', date('Y'))
        ->sum('total');

        $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
        ->whereYear('created_at', date('Y'))
        ->whereMonth('created_at', date('m'))
        ->sum('total');

        $somaMensal = $totalNfe + $totalNfce;
        $custoMensal = $this->custoProdutoMensal();
        $totalEmEstoque = $this->somaTotalEmEstoque();

        $totalDeVendaSemana = Nfce::where('empresa_id', request()->empresa_id)
        ->whereBetween('created_at', [$inicioSemana, $fimSemana])
        ->count() + 
        Nfe::where('empresa_id', request()->empresa_id)
        ->whereBetween('created_at', [$inicioSemana, $fimSemana])
        ->where('tpNF', 1)
        ->count();

        $produtosMaisVendidosMensal = $this->maisVendidosMensal();

        $caixas = Caixa::where('status', 1)->where('empresa_id', request()->empresa_id)
        ->get();

        $melhoresClientes = $this->melhoresClientes();

        return view('home', 
            compact('empresa', 'totalEmitidoMes', 'totalNfeCount', 'totalNfceCount', 'msgPlano', 'totalCteCount', 
                'totalMdfeCount', 'totalVendasMes', 'mes', 'somaVendasMesesAnteriores', 'totalComprasMes',
                'somaComprasMesesAnteriores', 'botaoContrato', 'homeComponentes', 'configGeral', 'emPromocao', 'somaMensal',
                'somaSemanal', 'custoMensal', 'totalEmEstoque', 'totalDeVendaSemana', 'inicioSemana', 'produtosMaisVendidosMensal',
                'caixas', 'melhoresClientes'));
    }

    private function melhoresClientes(){
        $nfeClientes = Nfe::join('clientes', 'clientes.id', '=', 'nves.cliente_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->whereYear('nves.created_at', date('Y'))
        ->whereMonth('nves.created_at', date('m'))
        ->where('tpNF', 1)
        ->select(
            'clientes.id as cliente_id',
            'clientes.razao_social',
            DB::raw('COUNT(nves.id) as total_vendas'),
            DB::raw('SUM(nves.total) as total')
        )
        ->groupBy('clientes.id', 'clientes.razao_social')
        ->get();

        $nfceClientes = Nfce::join('clientes', 'clientes.id', '=', 'nfces.cliente_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->whereYear('nfces.created_at', date('Y'))
        ->whereMonth('nfces.created_at', date('m'))
        ->select(
            'clientes.id as cliente_id',
            'clientes.razao_social',
            DB::raw('COUNT(nfces.id) as total_vendas'),
            DB::raw('SUM(nfces.total) as total')
        )
        ->groupBy('clientes.id', 'clientes.razao_social')
        ->get();


        $maioresClientesMes = $nfeClientes->concat($nfceClientes)
        ->groupBy('cliente_id')
        ->map(function ($grupo) {
            return [
                'cliente_id' => $grupo->first()->cliente_id,
                'imagem' => $grupo->first()->cliente->img,
                'razao_social' => $grupo->first()->razao_social,
                'total_vendas' => $grupo->sum('total_vendas'),
                'total' => $grupo->sum('total'),
            ];
        })
        ->sortByDesc('total')
        ->take(10)
        ->values();

        return $maioresClientesMes;
    }

    private function maisVendidosMensal(){
        $itensNfe = ItemNfe::join('nves', 'nves.id', '=', 'item_nves.nfe_id')
        ->join('produtos', 'produtos.id', '=', 'item_nves.produto_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->whereYear('item_nves.created_at', date('Y'))
        ->whereMonth('item_nves.created_at', date('m'))
        ->where('tpNF', 1)
        ->select(
            'item_nves.produto_id',
            'produtos.nome',
            'produtos.imagem',
            'produtos.valor_unitario',
            'produtos.unidade',
            DB::raw('COUNT(DISTINCT item_nves.nfe_id) as total_vendas'),
            DB::raw('SUM(item_nves.quantidade) as total_itens'),
            DB::raw('SUM(item_nves.quantidade * item_nves.valor_unitario) as sub_total')
        )
        ->groupBy('item_nves.produto_id', 'produtos.nome')
        ->get();

        $itensNfce = ItemNfce::join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
        ->join('produtos', 'produtos.id', '=', 'item_nfces.produto_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->whereYear('item_nfces.created_at', date('Y'))
        ->whereMonth('item_nfces.created_at', date('m'))
        ->select(
            'item_nfces.produto_id',
            'produtos.nome',
            'produtos.imagem',
            'produtos.valor_unitario',
            'produtos.unidade',
            DB::raw('COUNT(DISTINCT item_nfces.nfce_id) as total_vendas'),
            DB::raw('SUM(item_nfces.quantidade) as total_itens'),
            DB::raw('SUM(item_nfces.quantidade * item_nfces.valor_unitario) as sub_total')
        )
        ->groupBy('item_nfces.produto_id', 'produtos.nome')
        ->get();

        $merged = $itensNfe->concat($itensNfce);

        $data = $merged->groupBy('produto_id')->map(function($rows) {
            return [
                'produto_id' => $rows->first()->produto_id,
                'nome' => $rows->first()->nome,
                'imagem' => $rows->first()->produto->img,
                'valor_unitario' => $rows->first()->valor_unitario,
                'unidade' => $rows->first()->unidade,
                'total_vendas' => $rows->sum('total_vendas'),
                'total_itens' => $rows->sum('total_itens'),
                'sub_total' => $rows->sum('sub_total')
            ];
        })->values()
        ->sortByDesc('total_itens')
        ->take(10)
        ->values();
        // dd($data[0]);
        return $data;
    }

    private function custoProdutoMensal(){
        $itensNfe = ItemNfe::join('nves', 'nves.id', '=', 'item_nves.nfe_id')
        ->where('nves.empresa_id', request()->empresa_id)
        ->whereYear('item_nves.created_at', date('Y'))
        ->whereMonth('item_nves.created_at', date('m'))
        ->select('produto_id', DB::raw('SUM(quantidade) as total_quantidade'))
        ->groupBy('produto_id')
        ->where('tpNF', 1)
        ->get();

        $itensNfce = ItemNfce::join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
        ->where('nfces.empresa_id', request()->empresa_id)
        ->whereYear('item_nfces.created_at', date('Y'))
        ->whereMonth('item_nfces.created_at', date('m'))
        ->select('produto_id', DB::raw('SUM(quantidade) as total_quantidade'))
        ->groupBy('produto_id')
        ->get();

        $merged = $itensNfe->concat($itensNfce);
        $resultado = $merged->groupBy('produto_id')->map(function($rows){
            return [
                'produto_id' => $rows->first()->produto_id,
                'quantidade' => $rows->sum('total_quantidade')
            ];
        })->values();

        $soma = 0;
        foreach($resultado as $r){
            $produto = Produto::find($r['produto_id']);
            if($produto){
                $soma += $r['quantidade'] * $produto->valor_compra;
            }
        }
        return $soma;
    }

    private function somaTotalEmEstoque(){
        return DB::table('estoques')
        ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
        ->where('produtos.empresa_id', request()->empresa_id)
        ->where('estoques.quantidade', '>', 0)
        ->select(DB::raw('SUM(estoques.quantidade * produtos.valor_unitario) as total_geral'))
        ->value('total_geral');
    }

    private function somaComprasMes(){
        $totalCompra = Nfe::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->where('tpNF', 0)
        ->sum('total');

        return $totalCompra;
    }

    private function somaVendasMes(){
        $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->where('tpNF', 1)
        ->sum('total');

        $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
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
            ->whereYear('created_at', date('Y'))
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
        $mesAtual = date('m')-($meses);
        // dd($mesAtual);
        $cont = 0;
        $i = 0;
        while($cont < $meses){
            if(isset($this->meses()[$mesAtual-1])){
                $mes = $this->meses()[$mesAtual-1];
            }else{
                $mes = 'Dezembro';
                $mesAtual = 12;
            }

            $totalNfe = Nfe::where('empresa_id', request()->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', $mesAtual)
            ->whereYear('created_at', date('Y'))
            ->where('tpNF', 1)
            ->sum('total');

            $totalNfce = Nfce::where('empresa_id', request()->empresa_id)
            ->where('estado', '!=', 'cancelado')
            ->whereMonth('created_at', $mesAtual)
            ->whereYear('created_at', date('Y'))
            ->sum('total');

            $mesAtual++;

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
