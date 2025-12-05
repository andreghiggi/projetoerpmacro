<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MargemComissao;
use App\Models\Empresa;
use App\Models\SystemUpdate;

class SugestaoController extends Controller
{

    protected $endpoint = 'http://api.slym.app.br';
    // protected $endpoint = 'http://localhost:8001';
    public function index(Request $request){
        if(!file_exists('empresa.ini')){
            return view('sugestao.start');
        }else{
            $token = $this->__getToken();
            if($token == null){
                session()->flash("flash_error", "Token vazio");
                return redirect()->route('sugestao.index');
            }

            $url = $_SERVER['HTTP_HOST'];

            $data = [
                'url' => $url,
                'total_empresas' => Empresa::count(),
            ];

            $systemUpdate = SystemUpdate::first();
            if($systemUpdate){
                $data['atualizacao'] = $systemUpdate->versao;
                $data['data_atualizacao'] = __data_pt($systemUpdate->updated_at);
            }

            $defaults = array(
                CURLOPT_URL => $this->endpoint . '/api/sugestoes?'.http_build_query($data),
                CURLOPT_TIMEOUT => 3000,
                CURLOPT_RETURNTRANSFER => true
            );

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $token,
                'Content-Type: application/json'
            ]);

            curl_setopt_array($curl, $defaults);
            $error = curl_error($curl);
            $response = curl_exec($curl);

            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($http_status == 403){
                unlink('empresa.ini');
                session()->flash("flash_error", $response);
                return redirect()->route('sugestao.index');
            }

            if($http_status == 200){
                $data = json_decode($response);
                // dd($data[0]);
                return view('sugestao.index', compact('data'));
            }else{
                dd($http_status . " " . $response);
            }
        }
    }

    private function __getToken(){
        $file = file_get_contents('empresa.ini');
        $token = explode(";", $file);
        if(isset($token[2])){
            $token = trim($token[2]);
            return $token;
        }
        unlink('empresa.ini');
        return null;
    }

    public function auth(Request $request){

        $data = [
            'token' => $request->token,
        ];

        try{
            $defaults = array(
                CURLOPT_URL => $this->endpoint . '/api/auth',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_TIMEOUT => 3000,
                CURLOPT_RETURNTRANSFER => true
            );

            $curl = curl_init();
            curl_setopt_array($curl, $defaults);
            $error = curl_error($curl);
            $response = curl_exec($curl);

            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($http_status == 200){
                //salva arquivo
                $item = json_decode($response);
                $write = "$item->nome;$item->telefone;$item->token";
                file_put_contents("empresa.ini", $write);

                session()->flash("flash_success", "Empresa credenciada!");
                return redirect()->route('sugestao.index');
            }

            $msg = json_decode($response);
            session()->flash("flash_error", $msg);
            return redirect()->back();
        }catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function create(){
        return view('sugestao.create');
    }

    public function store(Request $request){

        $token = $this->__getToken();
        if($token == null){
            session()->flash("flash_error", "Token vazio");
            return redirect()->route('sugestao.index');
        }

        if($request->texto == ""){
            session()->flash("flash_warning", "Informe o texto!");
            return redirect()->back();
        }

        $data = [
            'titulo' => $request->titulo,
            'texto' => $request->texto
        ];
        // dd($token);
        try{

            $defaults = array(
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_URL => $this->endpoint . '/api/sugestoes-store',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $token,
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            );

            // dd(http_build_query($data));

            $curl = curl_init();
            
            curl_setopt_array($curl, $defaults);
            $error = curl_error($curl);
            $response = curl_exec($curl);

            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($http_status == 200){
                //salva arquivo
                session()->flash("flash_success", "Sugestão cadastrada, obrigado!");
                return redirect()->route('sugestao.index');
            }

            $msg = json_decode($response);
            session()->flash("flash_error", $msg);
            return redirect()->back();
        }catch (\Exception $e) {
            session()->flash("flash_warning", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id)
    {

        $token = $this->__getToken();
        if($token == null){
            session()->flash("flash_error", "Token vazio");
            return redirect()->route('sugestao.index');
        }

        $defaults = array(
            CURLOPT_URL => $this->endpoint . '/api/sugestao/'.$id,
            CURLOPT_TIMEOUT => 3000,
            CURLOPT_RETURNTRANSFER => true
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $token,
            'Content-Type: application/json'
        ]);

        curl_setopt_array($curl, $defaults);
        $error = curl_error($curl);
        $response = curl_exec($curl);

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if($http_status == 200){
            $item = json_decode($response);
                // dd($data[0]);
            return view('sugestao.show', compact('item'));
        }else{
            dd($http_status . " " . $response);
        } 
    }

    public function like($id)
    {

        $token = $this->__getToken();
        if($token == null){
            session()->flash("flash_error", "Token vazio");
            return redirect()->route('sugestao.index');
        }

        $defaults = array(
            CURLOPT_URL => $this->endpoint . '/api/sugestao-like/'.$id,
            CURLOPT_TIMEOUT => 3000,
            CURLOPT_RETURNTRANSFER => true
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $token,
            'Content-Type: application/json'
        ]);

        curl_setopt_array($curl, $defaults);
        $error = curl_error($curl);
        $response = curl_exec($curl);

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if($http_status != 200){
            session()->flash("flash_warning", "Algo deu errado: " . $http_status . " " . $response);
        } 
        return redirect()->back();

    }

    public function response(Request $request, $id){
        $token = $this->__getToken();
        if($token == null){
            session()->flash("flash_error", "Token vazio");
            return redirect()->route('sugestao.index');
        }

        if($request->texto == ""){
            session()->flash("flash_warning", "Informe o texto!");
            return redirect()->back();
        }

        try{
            $data = [
                'texto' => $request->texto
            ];
            $defaults = array(
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_URL => $this->endpoint . '/api/sugestoes-store-comment/'.$id,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $token,
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            );

            // dd(http_build_query($data));

            $curl = curl_init();
            
            curl_setopt_array($curl, $defaults);
            $error = curl_error($curl);
            $response = curl_exec($curl);

            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($http_status == 200){
                //salva arquivo
                session()->flash("flash_success", "Comentario adicionado!");
                return redirect()->back();
            }

            $msg = json_decode($response);
            session()->flash("flash_error", $msg);
            return redirect()->back();
        }catch (\Exception $e) {
            session()->flash("flash_warning", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

}
