<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManutencaoVeiculo;
use App\Models\Veiculo;
use App\Models\ManutencaoVeiculoServico;
use App\Models\ManutencaoVeiculoProduto;
use App\Models\ManutencaoVeiculoAnexo;
use Illuminate\Support\Facades\DB;
use App\Utils\UploadUtil;

class ManutencaoVeiculoController extends Controller
{

    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:manutencao_veiculo_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:manutencao_veiculo_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:manutencao_veiculo_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:manutencao_veiculo_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $fornecedor_id = $request->get('fornecedor_id');
        $veiculo_id = $request->get('veiculo_id');
        $estado = $request->get('estado');

        $veiculo = null;
        $data = ManutencaoVeiculo::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($fornecedor_id), function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->when(!empty($veiculo_id), function ($query) use ($veiculo_id) {
            return $query->where('veiculo_id', $veiculo_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        if($veiculo_id){
            $veiculo = Veiculo::findOrFail($veiculo_id);
        }

        return view('manutencao_veiculo.index', compact('data', 'veiculo'));
    }

    public function create()
    {
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)->get();
        return view('manutencao_veiculo.create', compact('veiculos'));
    }

    public function edit($id)
    {
        $item = ManutencaoVeiculo::findOrFail($id);
        __validaObjetoEmpresa($item);
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)->get();
        return view('manutencao_veiculo.edit', compact('veiculos', 'item'));
    }

    public function store(Request $request){
        try{

            $last = ManutencaoVeiculo::where('empresa_id', $request->empresa_id)
            ->orderBy('numero_sequencial', 'desc')->first();

            $request->merge([
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'total' => __convert_value_bd($request->total),
                'numero_sequencial' => $last != null ? ($last->numero_sequencial+1) : 1
            ]);
            DB::transaction(function () use ($request) {
                $item = ManutencaoVeiculo::create($request->all());

                for($i=0; $i<sizeof($request->servico_id); $i++){
                    if($request->quantidade_servico[$i] && $request->valor_unitario_servico[$i]){
                        ManutencaoVeiculoServico::create([
                            'manutencao_id' => $item->id,
                            'servico_id' => $request->servico_id[$i],
                            'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                            'valor_unitario' => __convert_value_bd($request->valor_unitario_servico[$i]),
                            'sub_total' => __convert_value_bd($request->sub_total_servico[$i]),
                            'observacao' => $request->observacao_servico[$i]
                        ]);
                    }
                }

                for($i=0; $i<sizeof($request->produto_id); $i++){
                    if($request->quantidade_produto[$i] && $request->valor_unitario_produto[$i]){
                        ManutencaoVeiculoProduto::create([
                            'manutencao_id' => $item->id,
                            'produto_id' => $request->produto_id[$i],
                            'quantidade' => __convert_value_bd($request->quantidade_produto[$i]),
                            'valor_unitario' => __convert_value_bd($request->valor_unitario_produto[$i]),
                            'sub_total' => __convert_value_bd($request->sub_total_produto[$i]),
                            'observacao' => $request->observacao_produto[$i]
                        ]);
                    }
                }
            });

            __createLog($request->empresa_id, 'Manutenção de Veículo', 'cadastrar', $request->nome);
            session()->flash("flash_success", "Manutenção criada com sucesso!");

        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            __createLog($request->empresa_id, 'Manutenção de Veículo', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('manutencao-veiculos.index');

    }

    public function update(Request $request, $id){
        try{
            $item = ManutencaoVeiculo::findOrFail($id);
            $request->merge([
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'total' => __convert_value_bd($request->total),
            ]);

            $item->fill($request->all())->save();
            $item->servicos()->delete();
            $item->produtos()->delete();

            for($i=0; $i<sizeof($request->servico_id); $i++){
                if($request->quantidade_servico[$i] && $request->valor_unitario_servico[$i]){
                    ManutencaoVeiculoServico::create([
                        'manutencao_id' => $item->id,
                        'servico_id' => $request->servico_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_servico[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_servico[$i]),
                        'observacao' => $request->observacao_servico[$i]
                    ]);
                }
            }

            for($i=0; $i<sizeof($request->produto_id); $i++){
                if($request->quantidade_produto[$i] && $request->valor_unitario_produto[$i]){
                    ManutencaoVeiculoProduto::create([
                        'manutencao_id' => $item->id,
                        'produto_id' => $request->produto_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_produto[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_produto[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_produto[$i]),
                        'observacao' => $request->observacao_produto[$i]
                    ]);
                }
            }
            
            __createLog($request->empresa_id, 'Manutenção de Veículo', 'editar', $request->nome);
            session()->flash("flash_success", "Manutenção alterada com sucesso!");

        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            __createLog($request->empresa_id, 'Manutenção de Veículo', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('manutencao-veiculos.index');

    }

    public function destroy($id)
    {
        $item = ManutencaoVeiculo::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->numero_sequencial;
            $item->servicos()->delete();
            $item->produtos()->delete();

            $item->delete();
            __createLog(request()->empresa_id, 'Manutenção de Veículo', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Manutenção removida com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Manutenção de Veículo', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();

    }

    public function show($id)
    {   
        $item = ManutencaoVeiculo::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('manutencao_veiculo.show', compact('item'));
    }

    public function alterarEstado(Request $request, $id)
    {   
        $item = ManutencaoVeiculo::findOrFail($id);
        __validaObjetoEmpresa($item);
        try{
            $item->estado = $request->estado;
            $item->save();
            session()->flash("flash_success", "Estado alterado!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function gerarContaReceber($id)
    {   
        $item = ManutencaoVeiculo::findOrFail($id);
        $item->valor_integral = $item->total;
        __validaObjetoEmpresa($item);
        return view('manutencao_veiculo.conta_pagar', compact('item'));
    }

    public function upload(Request $request, $id){
        $item = ManutencaoVeiculo::findOrFail($id);
        try{

            if ($request->hasFile('file')) {
                $file_name = $this->util->uploadImage($request, '/manutencao', 'file');
                ManutencaoVeiculoAnexo::create([
                    'manutencao_id' => $id,
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

    public function destroyFile($id){
        $item = ManutencaoVeiculoAnexo::findOrFail($id);
        if($item){
            $this->util->unlinkImage($item, '/manutencao', 'arquivo');
            $item->delete();
            session()->flash("flash_success", 'Arquivo removido!');
        }else{
            session()->flash("flash_error", 'Nenhum arquivo encontrado!');
        }
        return redirect()->back();
    }

}
