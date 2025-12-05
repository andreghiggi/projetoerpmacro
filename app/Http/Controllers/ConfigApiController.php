<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiConfig;
use App\Models\ApiLog;

class ConfigApiController extends Controller
{
    public function index(Request $request){
        $data = ApiConfig::where('empresa_id', $request->empresa_id)
        ->get();

        return view('api_config.index', compact('data'));
    }

    public function create(){
        return view('api_config.create');
    }

    public function store(Request $request){
        try {

            $request->merge([
                'permissoes_acesso' => json_encode($request->permissoes_acesso)
            ]);
            ApiConfig::create($request->all());
            session()->flash("flash_success", "Token cadastrado com sucesso!");
            return redirect()->route('config-api.index');

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit($id){

        $item = ApiConfig::findOrFail($id);
        __validaObjetoEmpresa($item);

        $item->permissoes_acesso = $item->permissoes_acesso != 'null' ? json_decode($item->permissoes_acesso) : [];

        return view('api_config.edit', compact('item'));
    }

    public function update(Request $request, $id){
        try {
            $item = ApiConfig::findOrFail($id);
            __validaObjetoEmpresa($item);

            $request->merge([
                'permissoes_acesso' => json_encode($request->permissoes_acesso)
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Token atualizado com sucesso!");
            return redirect()->route('config-api.index');

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $item = ApiConfig::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $item->delete();
            session()->flash("flash_success", "Token removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function logs(Request $request){

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $status = $request->get('status');
        $tipo = $request->get('tipo');
        $prefixo = $request->get('prefixo');

        $data = ApiLog::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when(!empty($tipo), function ($query) use ($tipo) {
            return $query->where('tipo', $tipo);
        })
        ->when(!empty($prefixo), function ($query) use ($prefixo) {
            return $query->where('prefixo', $prefixo);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        return view('api_config.logs', compact('data'));
    }

}
