<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracaoSuper;
use App\Models\NotaServicoConfig;
use App\Models\Empresa;
use App\Models\Cidade;
use App\Utils\UploadUtil;
use CloudDfe\SdkPHP\Softhouse;
use CloudDfe\SdkPHP\Emitente;
use Illuminate\Support\Str;
use CloudDfe\SdkPHP\Certificado;

class NotaServicoConfigController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $config = ConfiguracaoSuper::first();

        if($config == null){
            session()->flash('flash_error', 'Sem dados de configuração superadmin!');
            return redirect()->back();
        }

        if($config->token_auth_nfse == null){
            session()->flash('flash_error', 'Sem dados do token integra notas de configuração superadmin!');
            return redirect()->back();
        }

        $item = NotaServicoConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $empresa = Empresa::findOrFail($request->empresa_id);
        $tokenNfse = $empresa->token_nfse;
        return view('nota_servico_config.index', compact('item', 'tokenNfse'));

    }

    public function store(Request $request){
        try{
            $file_name = null;
            if($request->hasFile('image')){
                $file_name = $this->util->uploadImage($request, '/logos');
            }
            $request->merge([
                'logo' => $file_name
            ]);

            $item = NotaServicoConfig::create($request->all());
            $resp = $this->storeSofthouse($request);

            if($resp->codigo == 200){
                $item->token = $resp->token;
                $item->save();

                $empresa = Empresa::findOrFail($request->empresa_id);
                $empresa->token_nfse = $resp->token;
                $empresa->save();

                session()->flash("flash_success", "Configurado com sucesso!");
            }else{
                session()->flash('flash_error', $resp->mensagem);
            }

        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    private function storeSofthouse($request){
        try {
            $config = ConfiguracaoSuper::first();

            $params = [
                'token' => $config->token_auth_nfse,
                'ambiente' => Softhouse::AMBIENTE_PRODUCAO,
                'options' => [
                    'debug' => false,
                    'timeout' => 60,
                    'port' => 443,
                    'http_version' => CURL_HTTP_VERSION_NONE
                ]
            ];
            $softhouse = new Softhouse($params);
            $documento = preg_replace('/[^0-9]/', '', $request->documento);
            $telefone = preg_replace('/[^0-9]/', '', $request->telefone);
            $cep = preg_replace('/[^0-9]/', '', $request->cep);

            $cidade = Cidade::findOrFail($request->cidade_id);

            $payload = [
                "nome" => $request->nome,
                "razao" => $request->razao_social,
                "cnae" => $request->cnae,
                "crt" => $request->regime == 'simples' ? 1 : 3,
                "ie" => $request->ie,
                "im" => $request->im,
                "login_prefeitura" => $request->login_prefeitura,
                "senha_prefeitura" => $request->senha_prefeitura,
                "telefone" => $telefone,
                "email" => $request->email,
                "rua" => $request->rua,
                "numero" => $request->numero,
                "complemento" => $request->complemento,
                "bairro" => $request->bairro,
                "municipio" => $cidade->nome, 
                "cmun" => $cidade->codigo, 
                "uf" => $cidade->uf, 
                "cep" => $cep,
                "plano" => 'Emitente',
                "documentos" => [
                    "nfse" => true,
                ]
            ];

            if(strlen($documento) == 11){
                $payload['cpf'] = $documento;
            }else{
                $payload['cnpj'] = $documento;
            }
            // dd($payload);
            $resp = $softhouse->criaEmitente($payload);

            return $resp;
        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id){
        try{
            $item = NotaServicoConfig::findOrFail($id);

            $file_name = $item->logo;
            if($request->hasFile('image')){
                $file_name = $this->util->uploadImage($request, '/logos');
            }
            $request->merge([
                'logo' => $file_name
            ]);

            $item->fill($request->all())->save();
            $resp = $this->atualizaSofthouse($request, $item);

            if($resp->codigo == 200){
                session()->flash('flash_success', 'Configuração atualizada com sucesso!');
            }else{
                $erros = "";
                if(isset($resp->erros)){
                    foreach($resp->erros as $e){
                        $erros .= $e->erro . " ";
                    }
                }
                session()->flash('flash_error', $resp->mensagem . " " . $erros);
            } 

        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    private function atualizaSofthouse($request, $item){
        try {
            $config = ConfiguracaoSuper::first();

            $params = [
                'token' => $item->token,
                'ambiente' => Softhouse::AMBIENTE_PRODUCAO,
                'options' => [
                    'debug' => false,
                    'timeout' => 60,
                    'port' => 443,
                    'http_version' => CURL_HTTP_VERSION_NONE
                ]
            ];

            $softhouse = new Emitente($params);
            $documento = preg_replace('/[^0-9]/', '', $request->documento);
            $telefone = preg_replace('/[^0-9]/', '', $request->telefone);
            $cep = preg_replace('/[^0-9]/', '', $request->cep);

            $cidade = Cidade::findOrFail($request->cidade_id);  
            $payload = [
                "nome" => $request->nome,
                "razao" => $request->razao_social,
                "cnae" => $request->cnae,
                "crt" => $request->regime == 'simples' ? 1 : 3,
                "ie" => $request->ie,
                "im" => $request->im,
                "login_prefeitura" => $request->login_prefeitura,
                "senha_prefeitura" => $request->senha_prefeitura,
                "telefone" => $telefone,
                "email" => $request->email,
                "rua" => $request->rua,
                "numero" => $request->numero,
                "complemento" => $request->complemento,
                "bairro" => $request->bairro,
                "municipio" => $cidade->nome, 
                "cmun" => $cidade->codigo, 
                "uf" => $cidade->uf, 
                "cep" => $cep,
                "plano" => 'Emitente',
                "documentos" => [
                    "nfse" => true,
                ]
            ];

            if(strlen($documento) == 11){
                $payload['cpf'] = $documento;
            }else{
                $payload['cnpj'] = $documento;
            }

            if($item->logo != null){
                if(file_exists(public_path('uploads/logos/').$item->logo)){
                    $file = file_get_contents(public_path('uploads/logos/').$item->logo);
                    $payload['logo'] = base64_encode($file);
                }
            }
            // dd($payload);
            $resp = $softhouse->atualiza($payload);
            return $resp;
        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
    }

    public function certificado(){
        $config = ConfiguracaoSuper::first();
        if($config == null){
            session()->flash('flash_error', 'Sem dados de configuração superadmin!');
            return redirect()->back();
        }

        $certificadoApi = $this->getCertificado();
        return view('nota_servico_config.certificado', compact('certificadoApi'));
    }

    private function getCertificado(){
        $item = NotaServicoConfig::where('empresa_id', request()->empresa_id)
        ->first();
        $params = [
            'token' => $item->token,
            'ambiente' => Certificado::AMBIENTE_PRODUCAO,
            'options' => [
                'debug' => false,
                'timeout' => 60,
                'port' => 443,
                'http_version' => CURL_HTTP_VERSION_NONE
            ]
        ];
        $certificado = new Certificado($params);
        $resp = $certificado->mostra();

        return $resp;
    }

    public function uploadCertificado(Request $request){
        // if(!is_dir(public_path('certificado_temp'))){
        //     mkdir(public_path('certificado_temp'), 0777, true);
        // }

        if(!$request->hasFile('file')){
            session()->flash('flash_error', 'Selecione o Certificado!');
            return redirect()->back();
        }

        $file = base64_encode(file_get_contents($request->file('file')->path()));
        // dd($file);
        $senha = $request->senha;

        try {
            $config = ConfiguracaoSuper::first();
            $item = NotaServicoConfig::where('empresa_id', $request->empresa_id)
            ->first();
            $params = [
                'token' => $item->token,
                'ambiente' => Certificado::AMBIENTE_PRODUCAO,
                'options' => [
                    'debug' => false,
                    'timeout' => 60,
                    'port' => 443,
                    'http_version' => CURL_HTTP_VERSION_NONE
                ]
            ];
            $certificado = new Certificado($params);

            $payload = [
                'certificado' => $file,
                'senha' => $senha
            ];

            $resp = $certificado->atualiza($payload);
            if($resp->codigo == 200){
                session()->flash('flash_success', 'Upload realizado com sucesso!');
            }else{
                session()->flash('flash_error', $resp->mensagem);
            }
        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

}
