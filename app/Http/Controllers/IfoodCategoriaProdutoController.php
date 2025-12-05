<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\IfoodUtil;
use App\Models\IfoodConfig;
use App\Models\CategoriaProdutoIfood;

class IfoodCategoriaProdutoController extends Controller
{
    protected $util;

    public function __construct(IfoodUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $config = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        if($config == null){
            session()->flash("flash_error", "Configure o App");
            return redirect()->route('ifood-config.index');
        }
        $result = $this->util->getCategories($config);

        if(isset($result->message)){
            if($result->message == "token expired"){
                $result = $this->util->oAuthToken($config);
                if(isset($result['success']) && $result['success'] == 1){
                    return redirect()->route('ifood-categoria-produtos.index');
                }else{
                    return redirect()->route('ifood-config.index');
                }
            }
        }
        // dd($result);
        foreach($result as $c){
            CategoriaProdutoIfood::updateOrCreate([
                'empresa_id' => $config->empresa_id,
                'ifood_id' => $c->id,
                'nome' => $c->name,
                'status' => $c->status,
                'template' => $c->template,
            ]);
        }

        $data = CategoriaProdutoIfood::where('empresa_id', $request->empresa_id)->get();
        return view('ifood_categorias.index', compact('data'));
    }

    public function create(){
        return view('ifood_categorias.create');
    }

    public function store(Request $request){
        $config = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        $data = [
            'name' => $request->nome,
            'status' => $request->status,
            'template' => $request->template,
        ];

        $result = $this->util->storeCategory($config, $data);
        if(isset($result->id)){
            CategoriaProdutoIfood::create([
                'empresa_id' => $config->empresa_id,
                'ifood_id' => $result->id,
                'nome' => $result->name,
                'status' => $result->status,
                'template' => $result->template,
            ]);
            session()->flash("flash_success", "Categoria cadastrada!");
            return redirect()->route('ifood-categoria-produtos.index');
        }else{
            session()->flash("flash_error", "Algo deu errado: " . $result->error->message);
            // dd($result);
            return redirect()->back();
        }
    }

    public function edit($id){
        $item = CategoriaProdutoIfood::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('ifood_categorias.edit', compact('item'));
    }

    public function update(Request $request, $id){
        $item = CategoriaProdutoIfood::findOrFail($id);

        $config = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        $data = [
            'name' => $request->nome,
            'status' => $request->status,
            'template' => $request->template,
        ];

        $result = $this->util->updateCategory($config, $data, $item->ifood_id);
        // dd($result);
        if(isset($result->name)){

            $item->nome = $result->name;
            $item->status = $result->status;
            // $item->template = $result->template;
            $item->save();
            session()->flash("flash_success", "Categoria atualizada!");
            return redirect()->route('ifood-categoria-produtos.index');
        }else{
            session()->flash("flash_error", "Algo deu errado: " . $result->error->message);
            // dd($result);
            return redirect()->back();
        }
    }

    public function destroy($id){
        $item = CategoriaProdutoIfood::findOrFail($id);
        __validaObjetoEmpresa($item);
        $config = IfoodConfig::
        where('empresa_id', request()->empresa_id)
        ->first();

        $result = $this->util->destroyCategory($config, $item->ifood_id);
        if($result == null){
            $item->delete();
            session()->flash("flash_success", "Categoria removida!");
        }else{
            session()->flash("flash_error", "Algo deu errado: " . $result->error->message);
        }
        return redirect()->back();
    }

}
