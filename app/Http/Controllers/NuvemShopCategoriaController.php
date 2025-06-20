<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\NuvemShopUtil;
use App\Models\CategoriaNuvemShop;

class NuvemShopCategoriaController extends Controller
{

    protected $util;

    public function __construct(NuvemShopUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $store_info = session('store_info');

        if(!$store_info){
            return redirect()->route('nuvem-shop-auth.index');
        }
        $data = [];
        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
        try{
            $categorias = (array)$api->get("categories");
            $data = $categorias['body'];
        }catch(\Exception $e){
            echo $e->getMessage();
            die;
        }
        
        CategoriaNuvemShop::where('empresa_id', $request->empresa_id)->delete();
        foreach($data as $c){
            $categoria = [
                'empresa_id' => $request->empresa_id,
                'nome' => $c->name->pt,
                '_id' => $c->id
            ];

            CategoriaNuvemShop::create($categoria);
        }
        // dd($data);
        return view('nuvem_shop_categorias.index', compact('data'));
    }

    public function create(){
        $store_info = session('store_info');
        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');

        $categorias = (array)$api->get("categories");
        $body = $categorias['body'];

        $categorias = [];
        foreach($body as $c){
            $categorias[$c->id] = $c->name->pt;
        }

        return view('nuvem_shop_categorias.create', compact('categorias'));

    }

    public function edit($id){
        $store_info = session('store_info');
        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
        $categoria = (array)$api->get("categories/".$id);
        $item = $categoria['body'];

        $categorias = (array)$api->get("categories");
        $body = $categorias['body'];

        $categorias = [];
        foreach($body as $c){
            $categorias[$c->id] = $c->name->pt;
        }

        return view('nuvem_shop_categorias.edit', compact('categorias', 'item'));
    }

    public function store(Request $request){
        try{
            $store_info = session('store_info');
            $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
            $response = $api->post("categories", [
                'name' => $request->nome,
                'parent' => $request->categoria_id,
                'description' => $request->descricao
            ]);
            if($response){
                session()->flash("flash_success", "Categoria cadastrada!");
            }else{
                session()->flash("flash_error", "Algo deu errado ao cadastrar!");
            }
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
        return redirect()->route('nuvem-shop-categorias.index');
    }

    public function update(Request $request, $id){
        try{
            $store_info = session('store_info');
            $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');

            $dataUpdate = [
                'name' => $request->nome,
                'description' => $request->descricao
            ];
            if($request->categoria_id){
                $dataUpdate['parent'] = $request->categoria_id;
            }
            $response = $api->put("categories/$id", $dataUpdate);
            if($response){
                session()->flash("flash_success", "Categoria atualizada!");
            }else{
                session()->flash("flash_error", "Algo deu errado ao atualizar!");
            }
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
        return redirect()->route('nuvem-shop-categorias.index');
    }

    public function destroy($id){
        $store_info = session('store_info');
        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
        try{
            $response = $api->delete("categories/$id");
            session()->flash("flash_success", "Categoria removida!");

        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());

        }
        return redirect()->route('nuvem-shop-categorias.index');
    }

}
