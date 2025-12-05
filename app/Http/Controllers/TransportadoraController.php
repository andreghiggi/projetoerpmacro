<?php

namespace App\Http\Controllers;

use App\Models\Transportadora;
use Exception;
use Illuminate\Http\Request;

class TransportadoraController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:transportadoras_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:transportadoras_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:transportadoras_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:transportadoras_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Transportadora::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->razao_social), function ($q) use ($request) {
            return $q->where('razao_social', 'LIKE', "%$request->razao_social%");
        })
        ->when(!empty($request->cpf_cnpj), function ($q) use ($request) {
            return $q->where('cpf_cnpj', 'LIKE', "%$request->cpf_cnpj%");
        })
        ->paginate(__itensPagina());
        return view('transportadoras.index', compact('data'));
    }

    public function create()
    {
        return view('transportadoras.create');
    }

    public function edit($id)
    {
        $item = Transportadora::findOrFail($id);
        return view('transportadoras.edit', compact('item'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {
            Transportadora::create($request->all());
            __createLog($request->empresa_id, 'Transportadora', 'cadastrar', $request->razao_social);
            session()->flash("flash_success", "Transportadora cadastrada!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Transportadora', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('transportadoras.index');
    }

    public function update(Request $request, $id)
    {
        $item = Transportadora::findOrFail($id);
        try {
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Transportadora', 'editar', $request->razao_social);
            session()->flash("flash_success", "Transportadora atualizada!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Transportadora', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('transportadoras.index');
    }


    private function __validate(Request $request)
    {
        $rules = [
            'razao_social' => 'required',
            'nome_fantasia' => 'required',
            'cpf_cnpj' => 'required',
            // 'ie' => 'required',
            'email' => 'required',
            'telefone' => 'required',
            'cidade_id' => 'required',
            'rua' => 'required',
            'cep' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            // 'antt' => 'required',
        ];
        $messages = [
            'razao_social.required' => 'Campo Obrigatório',
            'nome_fantasia.required' => 'Campo Obrigatório',
            'cpf_cnpj.required' => 'Campo Obrigatório',
            'ie.required' => 'Campo Obrigatório',
            'email.required' => 'Campo Obrigatório',
            'telefone.required' => 'Campo Obrigatório',
            'cidade_id.required' => 'Campo Obrigatório',
            'rua.required' => 'Campo Obrigatório',
            'cep.required' => 'Campo Obrigatório',
            'numero.required' => 'Campo Obrigatório',
            'bairro.required' => 'Campo Obrigatório',
            'antt.required' => 'Campo Obrigatório',
        ];
        $this->validate($request, $rules, $messages);
    }

    public function destroy($id)
    {
        $item = Transportadora::findOrFail($id);
        try{
            $descricaoLog = $item->razao_social;
            $item->delete();
            __createLog(request()->empresa_id, 'Transportadora', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Transportadora removida!");
        }catch(Exception $e){
            __createLog(request()->empresa_id, 'Transportadora', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('transportadoras.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = Transportadora::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->razao_social;
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Transportadora', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Transportadora', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('transportadoras.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->route('transportadoras.index');
    }
}
