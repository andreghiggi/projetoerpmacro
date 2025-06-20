<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MargemComissao;

class MargemComissaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:comissao_margem_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:comissao_margem_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:comissao_margem_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:comissao_margem_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $data = MargemComissao::where('empresa_id', request()->empresa_id)
        ->get();

        return view('margem_comissao.index', compact('data'));
    }

    public function create()
    {
        return view('margem_comissao.create');
    }

    public function edit($id)
    {
        $item = MargemComissao::findOrFail($id);
        return view('margem_comissao.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            MargemComissao::create($request->all());
            session()->flash("flash_success", "Margem de comissão criada com sucesso!");
            return redirect()->route('comissao-margem.index');
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();

        }
    }

    public function destroy($id){
        $item = MargemComissao::findOrFail($id);
        try{
            $item->delete();

            session()->flash("flash_success", "Margem de comissão removida");
        }catch(\Exception $e){
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();

    }

    public function update(Request $request, $id){

        try{
            $item = MargemComissao::findOrFail($id);

            
            $item->fill($request->all())->save();

            session()->flash("flash_success", "Margem de comissão atualizada");
            return redirect()->route('comissao-margem.index');

        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }
}
