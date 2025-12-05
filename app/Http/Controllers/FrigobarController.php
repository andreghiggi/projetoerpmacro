<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Frigobar;
use App\Models\Acomodacao;
use App\Models\PadraoFrigobar;

class FrigobarController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:frigobar_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:frigobar_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:frigobar_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:frigobar_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Frigobar::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->modelo), function ($q) use ($request) {
            return $q->where('modelo', 'LIKE', "%$request->modelo%");
        })
        ->orderBy('modelo', 'asc')
        ->paginate(__itensPagina());
        return view('frigobar.index', compact('data'));
    }

    public function create()
    {
        $acomodacoes = Acomodacao::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        if(sizeof($acomodacoes) == 0){
            session()->flash("flash_warning", 'Cadastre uma acomodação!');
            return redirect()->route('acomodacao.create');
        }
        return view('frigobar.create', compact('acomodacoes'));
    }

    public function edit($id)
    {
        $item = Frigobar::findOrFail($id);
        __validaObjetoEmpresa($item);

        $acomodacoes = Acomodacao::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        if(sizeof($acomodacoes) == 0){
            session()->flash("flash_warning", 'Cadastre uma acomodação!');
            return redirect()->route('acomodacao.create');
        }
        return view('frigobar.edit', compact('item', 'acomodacoes'));
    }

    public function store(Request $request)
    {
        try {

            Frigobar::create($request->all());
            session()->flash("flash_success", "Frigobar criado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('frigobar.index');
    }

    public function update(Request $request, $id)
    {
        $item = Frigobar::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Frigobar alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('frigobar.index');
    }

    public function destroy($id)
    {
        $item = Frigobar::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash("flash_success", "Frigobar removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('frigobar.index');
    }

    public function show($id)
    {
        $item = Frigobar::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('frigobar.show', compact('item'));

    }

    public function storeDefault(Request $request, $id)
    {
        $item = Frigobar::findOrFail($id);
        try{

            __validaObjetoEmpresa($item);
            $item->padraoProdutos()->delete();
            for($i=0; $i<sizeof($request->produto_id); $i++){
                PadraoFrigobar::create([
                    'frigobar_id' => $item->id,
                    'produto_id' => $request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade[$i])
                ]);
            }
            session()->flash("flash_success", "Padrão definido!");

        }catch(\Exception $e){
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->back();
    }

}
