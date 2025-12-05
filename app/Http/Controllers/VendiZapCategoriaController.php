<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendiZapConfig;
use App\Models\CategoriaVendiZap;
use Illuminate\Support\Str;

class VendiZapCategoriaController extends Controller
{

    protected $url = "https://app.vendizap.com/api";

    public function index(Request $request){

        $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();
        if($config == null){
            session()->flash("flash_error", "Configure as credenciais!");
            return redirect()->route('vendizap-config.index');
        }

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        curl_setopt($ch, CURLOPT_URL, $this->url . '/categorias');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);

        if(!is_array($data)){
            session()->flash("flash_error", $data);
            return redirect()->route('vendizap-config.index');
        }
        foreach($data as $c){
            $item = CategoriaVendiZap::where('empresa_id', $request->empresa_id)
            ->where('_id', $c->id)->first();

            if($item == null){
                CategoriaVendiZap::create([
                    '_id' => $c->id,
                    'empresa_id' => $request->empresa_id,
                    'nome' => $c->nome,
                ]);
            }

            $c->data_cadastro = $item->created_at ? __data_pt($item->created_at) : null;
        }
        
        return view('vendizap_categorias.index', compact('data'));

    }

    public function create(){
        return view('vendizap_categorias.create');
    }

    public function edit($id){
        $item = CategoriaVendiZap::where('_id', $id)->first();

        $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();
        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        curl_setopt($ch, CURLOPT_URL, $this->url . '/categorias/'.$item->_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        if($data && isset($data->imagem)){
            $item->imagem = $data->imagem;
        }
        return view('vendizap_categorias.edit', compact('item'));
    }

    public function store(Request $request){
        $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();

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
            'nome' => $request->nome,
        ];

        if($image != null){
            $data['imagem'] = $image;
        }

        try{

            $ch = curl_init();
            $headers = [
                "X-Auth-Id: " . $config->auth_id,
                "X-Auth-Secret: " . $config->auth_secret,
                'Content-Type: application/json'
            ];

            curl_setopt($ch, CURLOPT_URL, $this->url . '/categorias');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_POST, true);

            $data = json_decode(curl_exec($ch));
            curl_close($ch);

            if($data->id){
                CategoriaVendiZap::create([
                    '_id' => $data->id,
                    'empresa_id' => $request->empresa_id,
                    'nome' => $request->nome,
                ]);
                session()->flash("flash_success", "Categoria cadastrada!");
                return redirect()->route('vendizap-categorias.index');
            }
        }catch(\Exception $e){
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado ao cadastrar!");
            return redirect()->back();
        }
    }

    public function update(Request $request, $id){
        $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();

        $item = CategoriaVendiZap::where('empresa_id', request()->empresa_id)
        ->where('id', $id)->first();

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
                'nome' => $request->nome,
            ];
            if($image != null){
                $data['imagem'] = $image;
            }

            $ch = curl_init();
            $headers = [
                "X-Auth-Id: " . $config->auth_id,
                "X-Auth-Secret: " . $config->auth_secret,
                'Content-Type: application/json'
            ];

            curl_setopt($ch, CURLOPT_URL, $this->url . '/categorias/'.$item->_id);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            $data = json_decode(curl_exec($ch));
            curl_close($ch);
            // dd($data);
            if($data){
                $item->fill($request->all())->save();
                session()->flash("flash_success", "Categoria atualizada!");
            }else{
                session()->flash("flash_error", "Algo deu errado!");
            }
            return redirect()->route('vendizap-categorias.index');
        }catch(\Exception $e){
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

    private function clearFolder($destino){
        $files = glob($destino."/*");
        foreach($files as $file){ 
            if(is_file($file)) unlink($file); 
        }
    }

}
