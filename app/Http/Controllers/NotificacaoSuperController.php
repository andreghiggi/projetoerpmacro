<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacao;
use App\Models\Empresa;

class NotificacaoSuperController extends Controller
{
    public function index(Request $request){
        $status = $request->get('status');
        $empresa_id = $request->get('empresa');
        $prioridade = $request->get('prioridade');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $empresa = null;
        if($empresa_id){
            $empresa = Empresa::findOrFail($empresa_id);
        }

        $data = Notificacao::orderBy('id', 'desc')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($prioridade), function ($query) use ($prioridade) {
            return $query->where('prioridade', $prioridade);
        })
        ->when($status != '', function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when($empresa != null, function ($query) use ($empresa) {
            return $query->where('empresa_id', $empresa->id);
        })
        ->paginate(60);

        return view('notificacao_super.index', compact('data', 'empresa'));
    }

    public function create(){
        return view('notificacao_super.create');
    }

    public function edit($id)
    {
        $item = Notificacao::findOrFail($id);
        return view('notificacao_super.edit', compact('item'));
    }

    public function show($id)
    {
        $item = Notificacao::findOrFail($id);
        $item->visualizada = 1;
        $item->save();

        return view('notificacao_super.show', compact('item'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);

        try {
            if($request->empresa){
                $request->merge([
                    'empresa_id' => $request->empresa
                ]);
                Notificacao::create($request->all());
                session()->flash("flash_success", "Notificação criada com sucesso!");

            }else{
                $empresas = Empresa::where('status', 1)->get();
                foreach($empresas as $e){
                    $request->merge([
                        'empresa_id' => $e->id
                    ]);
                    if(!__isEmpresaMaster($e)){
                        Notificacao::create($request->all());
                    }
                }
                session()->flash("flash_success", "Notificação criada para todas as empresas!");
            }
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('notificacao-super.index');
    }

    public function update(Request $request, $id)
    {
        $this->__validate($request);

        $item = Notificacao::findOrFail($id);
        try {

            $request->merge([
                'empresa_id' => $item->empresa_id
            ]);

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Notificação alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('notificacao-super.index');
    }

    public function destroy($id)
    {
        $item = Notificacao::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Notificação removida com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('notificacao-super.index');
    }

    private function __validate(Request $request)
    {
        $rules = [
            'descricao' => 'required',
            'titulo' => 'required|max:30'
        ];

        $messages = [
            'descricao.required' => 'Campo Obrigatório',
            'titulo.required' => 'Campo Obrigatório',
            'titulo.max' => 'Máximo de 30 caracteres',
        ];
        $this->validate($request, $rules, $messages);
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = Notificacao::findOrFail($request->item_delete[$i]);
            try {
                $item->delete();
                $removidos++;
            } catch (\Exception $e) {
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('notificacao-super.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->route('notificacao-super.index');
    }

}
