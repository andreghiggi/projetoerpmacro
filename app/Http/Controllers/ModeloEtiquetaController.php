<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModeloEtiqueta;

class ModeloEtiquetaController extends Controller
{
    public function index(Request $request)
    {
        $data = ModeloEtiqueta::where('empresa_id', $request->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());

        $super = ModeloEtiqueta::where('empresa_id', null)->count();
        $importar = false;
        if($super > 0){
            $importados = ModeloEtiqueta::where('empresa_id', $request->empresa_id)
            ->where('importado_super', 1)->count();
            if($importados == 0){
                $importar = true;
            }
        }
        return view('modelo_etiqueta.index', compact('data', 'importar'));
    }

    public function importar(){
        $super = ModeloEtiqueta::where('empresa_id', null)->get();
        try{
            foreach($super as $s){
                $s->empresa_id = request()->empresa_id;
                $s->importado_super = 1;
                ModeloEtiqueta::create($s->toArray());
            }
            session()->flash("flash_success", "Modelos importados!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function create()
    {
        return view('modelo_etiqueta.create');
    }

    public function edit($id)
    {
        $item = ModeloEtiqueta::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('modelo_etiqueta.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'nome_empresa' => $request->nome_empresa ? 1 : 0,
                'nome_produto' => $request->nome_produto ? 1 : 0,
                'valor_produto' => $request->valor_produto ? 1 : 0,
                'codigo_produto' => $request->codigo_produto ? 1 : 0,
                'codigo_barras_numerico' => $request->codigo_barras_numerico ? 1 : 0
            ]);
            
            ModeloEtiqueta::create($request->all());
            __createLog($request->empresa_id, 'Modelo Etiqueta', 'cadastrar', $request->nome);
            session()->flash("flash_success", "Modelo criado com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Modelo Etiqueta', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('modelo-etiquetas.index');
    }

    public function update(Request $request, $id)
    {
        try {
            $item = ModeloEtiqueta::findOrFail($id);
            __validaObjetoEmpresa($item);

            $request->merge([
                'nome_produto' => $request->nome_produto ? 1 : 0,
                'nome_produto' => $request->nome_produto ? 1 : 0,
                'valor_produto' => $request->valor_produto ? 1 : 0,
                'codigo_produto' => $request->codigo_produto ? 1 : 0,
                'codigo_barras_numerico' => $request->codigo_barras_numerico ? 1 : 0
            ]);
            
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Modelo Etiqueta', 'editar', $request->nome);
            session()->flash("flash_success", "Modelo criado com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Modelo Etiqueta', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('modelo-etiquetas.index');
    }

    public function destroy($id)
    {
        $item = ModeloEtiqueta::findOrFail($id);
        __validaObjetoEmpresa($item);

        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Modelo Etiqueta', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Modelo removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Modelo Etiqueta', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }
}
