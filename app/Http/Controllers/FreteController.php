<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Frete;
use App\Models\FreteAnexo;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Veiculo;
use App\Models\TipoDespesaFrete;
use App\Models\DespesaFrete;
use App\Utils\UploadUtil;
use Illuminate\Support\Facades\DB;

class FreteController extends Controller
{

    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:frete_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:frete_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:frete_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:frete_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $veiculo_id = $request->get('veiculo_id');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $veiculo = null;
        $cliente = null;
        $data = Frete::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($veiculo_id), function ($query) use ($veiculo_id) {
            return $query->where('veiculo_id', $veiculo_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        if($veiculo_id){
            $veiculo = Veiculo::findOrFail($veiculo_id);
        }
        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }
        return view('fretes.index', compact('data', 'veiculo', 'cliente'));
    }

    public function create()
    {
        $cidades = Cidade::all();
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)->get();
        $tiposDespesas = TipoDespesaFrete::where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->orderBy('nome')
        ->get();
        return view('fretes.create', compact('cidades', 'tiposDespesas', 'veiculos'));
    }

    public function edit($id)
    {   
        $item = Frete::findOrFail($id);
        __validaObjetoEmpresa($item);

        $cidades = Cidade::all();
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)->get();
        $tiposDespesas = TipoDespesaFrete::where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->orderBy('nome')
        ->get();
        return view('fretes.edit', compact('cidades', 'tiposDespesas', 'veiculos', 'item'));
    }

    public function store(Request $request){
        try{
            DB::transaction(function () use ($request) {

                $last = Frete::where('empresa_id', $request->empresa_id)
                ->orderBy('numero_sequencial', 'desc')->first();

                $request->merge([
                    'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                    'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    'total' => __convert_value_bd($request->total),
                    'numero_sequencial' => $last != null ? ($last->numero_sequencial+1) : 1
                ]);

                $item = Frete::create($request->all());
                $totalDespesa = 0;
                for($i=0; $i<sizeof($request->valor_despesa); $i++){
                    if($request->valor_despesa[$i] && $request->tipo_despesa_id[$i]){
                        DespesaFrete::create([
                            'frete_id' => $item->id,
                            'tipo_despesa_id' => $request->tipo_despesa_id[$i],
                            'fornecedor_id' => $request->fornecedor_id[$i] ?? null,
                            'valor' => __convert_value_bd($request->valor_despesa[$i]),
                            'observacao' => $request->observacao_despesa[$i]
                        ]);
                        $totalDespesa += __convert_value_bd($request->valor_despesa[$i]);
                    }
                }
                $item->total_despesa = $totalDespesa;
                $item->save();
            });
            __createLog($request->empresa_id, 'Frete', 'cadastrar', $request->nome);
            session()->flash("flash_success", "Frete criado com sucesso!");

        } catch (\Exception $e) {
            // echo $e->getLine();
            // die;
            __createLog($request->empresa_id, 'Frete', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('fretes.index');
    }

    public function update(Request $request, $id){
        try{
            $item = Frete::findOrFail($id);
            $request->merge([
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'total' => __convert_value_bd($request->total),
            ]);

            $item->fill($request->all())->save();
            $totalDespesa = 0;
            $item->despesas()->delete();

            for($i=0; $i<sizeof($request->valor_despesa); $i++){
                if($request->valor_despesa[$i] && $request->tipo_despesa_id[$i]){
                    DespesaFrete::create([
                        'frete_id' => $item->id,
                        'tipo_despesa_id' => $request->tipo_despesa_id[$i],
                        'fornecedor_id' => $request->fornecedor_id[$i] ?? null,
                        'valor' => __convert_value_bd($request->valor_despesa[$i]),
                        'observacao' => $request->observacao_despesa[$i]
                    ]);
                    $totalDespesa += __convert_value_bd($request->valor_despesa[$i]);
                }
            }
            $item->total_despesa = $totalDespesa;
            $item->save();
            __createLog($request->empresa_id, 'Frete', 'editar', $request->nome);
            session()->flash("flash_success", "Frete alterado com sucesso!");

        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            __createLog($request->empresa_id, 'Frete', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('fretes.index');
    }

    public function destroy($id)
    {
        $item = Frete::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->numero_sequencial;
            $item->despesas()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Frete', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Frete removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Frete', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function show($id)
    {   
        $item = Frete::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('fretes.show', compact('item'));
    }

    public function alterarEstado(Request $request, $id)
    {   
        $item = Frete::findOrFail($id);
        __validaObjetoEmpresa($item);
        try{
            $item->estado = $request->estado;
            $item->save();
            session()->flash("flash_success", "Estado do frete alterado!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function gerarContaReceber($id)
    {   
        $item = Frete::findOrFail($id);
        __validaObjetoEmpresa($item);
        $item->valor_integral = $item->total;

        return view('fretes.conta_receber', compact('item'));
    }

    public function upload(Request $request, $id){
        $item = Frete::findOrFail($id);
        try{

            if ($request->hasFile('file')) {
                $file_name = $this->util->uploadImage($request, '/fretes', 'file');
                FreteAnexo::create([
                    'frete_id' => $id,
                    'arquivo' => $file_name
                ]);

                session()->flash("flash_success", 'Upload realizado!');
            }else{
                session()->flash("flash_error", 'Nenhum arquivo selecionado!');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function gerarContaPagar($id){
        $item = DespesaFrete::findOrFail($id);
        __validaObjetoEmpresa($item->frete);
        $item->valor_integral = $item->valor;
        
        return view('fretes.conta_pagar', compact('item'));
    }

    public function destroyFile($id){
        $item = FreteAnexo::findOrFail($id);
        if($item){
            $this->util->unlinkImage($item, '/fretes', 'arquivo');
            $item->delete();
            session()->flash("flash_success", 'Arquivo removido!');
        }else{
            session()->flash("flash_error", 'Nenhum arquivo encontrado!');
        }
        return redirect()->back();
    }
    
}
