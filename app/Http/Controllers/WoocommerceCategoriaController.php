<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\WoocommerceUtil;
use App\Models\CategoriaWoocommerce;
use Illuminate\Support\Str;

class WoocommerceCategoriaController extends Controller
{

    protected $util;
    protected $endpoint = 'products/categories';
    public function __construct(WoocommerceUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $woocommerceClient = $this->util->getConfig($request->empresa_id);
        if($woocommerceClient == null){
            session()->flash("flash_warning", 'Defina a configuração!');
            return redirect()->route('woocommerce-config.index');
        }

        $data = $woocommerceClient->get($this->endpoint);

        foreach($data as $c){
            $item = CategoriaWoocommerce::where('empresa_id', $request->empresa_id)
            ->where('_id', $c->id)->first();

            if($item == null){
                CategoriaWoocommerce::create([
                    '_id' => $c->id,
                    'empresa_id' => $request->empresa_id,
                    'nome' => $c->name,
                    'slug' => $c->slug,
                    'descricao' => $c->description
                ]);
            }
        }

        return view('woocommerce_categorias.index', compact('data'));
    }

    public function create(){
        return view('woocommerce_categorias.create');
    }

    public function edit($id){
        $categoria = CategoriaWoocommerce::where('empresa_id', request()->empresa_id)
        ->where('_id', $id)->first();

        if($categoria == null){
            session()->flash("flash_error", 'Categoria não encontrada!');
            return redirect()->back();
        }
        $woocommerceClient = $this->util->getConfig(request()->empresa_id);
        $item = $woocommerceClient->get($this->endpoint . "/$id");
        if($item == null){
            session()->flash("flash_error", 'Categoria não encontrada!');
            return redirect()->back();
        }
        return view('woocommerce_categorias.edit', compact('item'));
    }

    public function store(Request $request){
        $woocommerceClient = $this->util->getConfig($request->empresa_id);

        $image = null;

        if ($request->hasFile('image')) {
            if (!is_dir(public_path('uploads') . 'image_temp')) {
                mkdir(public_path('uploads') . 'image_temp', 0777, true);
            }

            $this->clearFolder(public_path('uploads'). '/image_temp');

            $file = $request->image;
            $ext = $file->getClientOriginalExtension();
            $file_name = Str::random(20) . ".$ext";

            $file->move(public_path('uploads'). '/image_temp', $file_name);
            $image = env('APP_URL') . '/uploads/image_temp/'.$file_name;
        }
        $data = [
            'name' => $request->nome,
            'slug' => $request->slug,
            'description' => $request->descricao ?? '',
        ];

        if($image != null){
            $data['image']['src'] = $image;
        }
        try{
            $categoria = $woocommerceClient->post($this->endpoint, $data);

            if($categoria->id){
                CategoriaWoocommerce::create([
                    '_id' => $categoria->id,
                    'empresa_id' => $request->empresa_id,
                    'nome' => $categoria->name,
                    'slug' => $categoria->slug,
                    'descricao' => $categoria->description
                ]);
                session()->flash("flash_success", "Categoria cadastrada!");
                return redirect()->route('woocommerce-categorias.index');

            }
        }catch(\Exception $e){
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado ao cadastrar!");
            return redirect()->back();
        }
    }

    public function update(Request $request, $id){
        $item = CategoriaWoocommerce::where('empresa_id', request()->empresa_id)
        ->where('_id', $id)->first();
        $woocommerceClient = $this->util->getConfig($request->empresa_id);

        $image = null;

        try{
            if ($request->hasFile('image')) {
                if (!is_dir(public_path('uploads') . 'image_temp')) {
                    mkdir(public_path('uploads') . 'image_temp', 0777, true);
                }

                $this->clearFolder(public_path('uploads'). '/image_temp');

                $file = $request->image;
                $ext = $file->getClientOriginalExtension();
                $file_name = Str::random(20) . ".$ext";

                $file->move(public_path('uploads'). '/image_temp', $file_name);
                $image = env('APP_URL') . '/uploads/image_temp/'.$file_name;
            }

            $data = [
                'name' => $request->nome,
                'description' => $request->descricao,
                'slug' => $request->slug,
            ];
            if($image != null){
                $data['image']['src'] = $image;
            }

            $categoria = $woocommerceClient->put($this->endpoint."/$id", $data);
            if($categoria->id){
                $item->fill($request->all())->save();
                session()->flash("flash_success", "Categoria atualizada!");
                return redirect()->route('woocommerce-categorias.index');
            }
        }catch(\Exception $e){
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado ao atualizar!");
            return redirect()->back();
        }
    }

    private function clearFolder($destino){
        $files = glob($destino."/*");
        foreach($files as $file){ 
            if(is_file($file)) unlink($file); 
        }
    }

    public function destroy($id){
        $item = CategoriaWoocommerce::where('empresa_id', request()->empresa_id)
        ->where('_id', $id)->first();

        if($item){
            $item->delete();
        }
        $woocommerceClient = $this->util->getConfig(request()->empresa_id);

        try{
            $deleteResult = $woocommerceClient->delete($this->endpoint."/$id", ['force' => true]);

            session()->flash("flash_success", "Categoria removida!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());

        }
        return redirect()->back();
    }
    
}
