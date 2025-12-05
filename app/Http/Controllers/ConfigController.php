<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa;
use App\Models\NaturezaOperacao;
use App\Models\User;
use App\Models\Plano;
use App\Models\PlanoEmpresa;
use App\Models\UsuarioEmpresa;
use App\Models\ConfigGeral;
use App\Models\ConfiguracaoSuper;
use App\Utils\UploadUtil;
use App\Utils\EmpresaUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use NFePHP\Common\Certificate;
use Mail;

class ConfigController extends Controller
{
    protected $util;
    protected $empresaUtil;

    public function __construct(UploadUtil $util, EmpresaUtil $empresaUtil)
    {
        $this->util = $util;
        $this->empresaUtil = $empresaUtil;
    }

    public function index()
    {
        $item = null;
        $empresa = auth::user()->empresa;

        $usuario = auth::user();
        if ($empresa != null) {
            $item = $empresa->empresa;
        }
        $dadosCertificado = null;

        if ($item != null && $item->arquivo) {
            $dadosCertificado = $this->getInfoCertificado($item);
        }

        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();

        return view('config.index', compact('empresa', 'usuario', 'item', 'dadosCertificado', 'naturezas'));
    }

    private function getInfoCertificado($item)
    {
        try {
            $infoCertificado = Certificate::readPfx($item->arquivo, $item->senha);
            $publicKey = $infoCertificado->publicKey;
            $inicio =  $publicKey->validFrom->format('Y-m-d H:i:s');
            $expiracao =  $publicKey->validTo->format('Y-m-d H:i:s');
            return [
                'serial' => $publicKey->serialNumber,
                'inicio' => \Carbon\Carbon::parse($inicio)->format('d-m-Y H:i'),
                'expiracao' => \Carbon\Carbon::parse($expiracao)->format('d-m-Y H:i'),
                'id' => $publicKey->commonName
            ];
        } catch (\Exception $e) {

            return [
                'erro' => 1,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        $plano = Plano::where('auto_cadastro', 1)->first();

        $usuario = auth::user();

        if($usuario->plano_auto_cadastro){
            $planoVinculado = Plano::find($usuario->plano_auto_cadastro);
            if($planoVinculado){
                $plano = $planoVinculado;
            }
        }

        try {

            $empresa = DB::transaction(function () use ($request, $plano) {
                $file_name = '';

                if ($request->hasFile('image')) {
                    $file_name = $this->util->uploadImage($request, '/logos');
                }
                $usuario = auth::user();
                if ($request->hasFile('certificado')) {
                    $file = $request->file('certificado');
                    $fileTemp = file_get_contents($file);
                    $request->merge([
                        'arquivo' => $fileTemp ?? '',
                        'cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->cpf_cnpj),
                        'usuario_id' => $request->usuario_id ?? '',
                        'senha' => $request['senha_certificado'] ?? '',
                        'token' => $request->token ?? ''
                    ]);
                }
                $email = $request->email;
                $request->merge([
                    'email' => $request->email_empresa,
                    'logo' => $file_name,
                ]);
                $empresa = Empresa::create($request->all());

                ConfigGeral::create([
                    'empresa_id' => $empresa->id,
                    'tipo_menu' => env('MENU_PADRAO'),
                    'balanca_valor_peso' => 'valor',
                    'notificacoes' => '[]',
                    'tipos_pagamento_pdv' => '[]',
                ]);
                
                if ($request->usuario) {
                    $usuario = User::create([
                        'name' => $request->usuario ?? null,
                        'email' => $email ?? null,
                        'password' => Hash::make($request['password']) ?? '',
                        'remember_token' => Hash::make($request['remember_token']) ?? ''
                    ]);
                }
                UsuarioEmpresa::create([
                    'empresa_id' => $empresa->id,
                    'usuario_id' => $usuario->id
                ]);

                $config = ConfiguracaoSuper::first();

                if($config != null && $config->cobrar_apos_auto_cadastro == 1 && $plano->dias_teste < 1){
                    $exp = date('Y-m-d', strtotime(date('Y-m-d') . "- 1 days"));
                    PlanoEmpresa::create([
                        'empresa_id' => $empresa->id,
                        'plano_id' => $plano->id,
                        'data_expiracao' => $exp,
                        'valor' => 0,
                        'forma_pagamento' => ''
                    ]);
                }else{

                    if($plano != null){
                        $intervalo = $plano->intervalo_dias;
                        if($plano->dias_teste){
                            $intervalo = $plano->dias_teste;
                        }
                        $exp = date('Y-m-d', strtotime(date('Y-m-d') . "+ $intervalo days"));
                        PlanoEmpresa::create([
                            'empresa_id' => $empresa->id,
                            'plano_id' => $plano->id,
                            'data_expiracao' => $exp,
                            'valor' => 0,
                            'forma_pagamento' => ''
                        ]);
                    }
                }
                $this->empresaUtil->initLocation($empresa);
                $this->empresaUtil->initNaturezaTributacao($empresa);
                try{
                    $this->emailNovaEmpresa($empresa);
                }catch(\Exception $e){

                }

                return $empresa;
            });

            session()->flash("flash_success", "Empresa cadastrada!");

            if(!$empresa->plano){
                return redirect()->route('payment.index');
            }
            if(strtotime($empresa->plano->data_expiracao) < strtotime(date('Y-m-d'))){
                session()->flash("flash_warning", "Efetue o pagamento para utilizar o sistema!");
                return redirect()->route('payment.index');
            }

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('config.index');
    }

    private function emailNovaEmpresa($empresa){
        $config = ConfiguracaoSuper::first();
        if($config && $config->email_aviso_novo_cadastro){
            Mail::send('mail.nova_empresa', ['empresa' => $empresa], function($m) use ($config){
                $nomeEmail = env('MAIL_FROM_NAME');
                $m->from(env('MAIL_USERNAME'), $nomeEmail);
                $m->subject('Nova empresa cadastrada');
                $m->to($config->email_aviso_novo_cadastro);
            });
        }
    }

    public function update(Request $request, $id)
    {
        $item = Empresa::findOrFail($id);
        try {
            $file_name = $item->logo;
            $doc = preg_replace('/[^0-9]/', '', $request->cpf_cnpj);
            if(strlen($doc) != 11 && strlen($doc) != 14){
                session()->flash("flash_error", "Configure CPF/CNPJ corretamente");
                return redirect()->back();
            }

            if ($request->hasFile('image')) {
                $this->util->unlinkImage($item, '/logos');
                $file_name = $this->util->uploadImage($request, '/logos');
            }

            $request->merge([
                'cpf_cnpj' => $doc,
                'logo' => $file_name,

            ]);
            if ($request->hasFile('certificado')) {
                $file = $request->file('certificado');
                $fileTemp = file_get_contents($file);
                $request->merge([
                    'arquivo' => $fileTemp
                ]);
            }
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Empresa atualizada!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('config.index');
    }

    public function show($id)
    {
        $item = Empresa::findOrFail($id);
        return view('empresas.painel', compact('item'));
    }

    private function __validate(Request $request)
    {
        $rules = [
            'nome' => 'required',
            'cpf_cnpj' => 'required',
            'ie' => 'required',
            // 'email' => 'required',
            'celular' => 'required',
            // 'csc' => 'required',
            // 'csc_id' => 'required',
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade_id' => 'required',
            // 'numero_ultima_nfe_producao' => 'required',
            // 'numero_ultima_nfe_homologacao' => 'required',
            // 'numero_serie_nfe' => 'required',
            // 'numero_ultima_nfce_producao' => 'required',
            // 'numero_ultima_nfce_homologacao' => 'required',
            // 'numero_serie_nfce' => 'required'
        ];
        $messages = [
            'nome.required' => 'Campo Obrigatório',
            'cpf_cnpj.required' => 'Campo Obrigatório',
            'ie.required' => 'Campo Obrigatório',
            'email.required' => 'Campo Obrigatório',
            'celular.required' => 'Campo Obrigatório',
            'csc.required' => 'Campo Obrigatório',
            'csc_id.required' => 'Campo Obrigatório',
            'cep.required' => 'Campo Obrigatório',
            'rua.required' => 'Campo Obrigatório',
            'numero.required' => 'Campo Obrigatório',
            'bairro.required' => 'Campo Obrigatório',
            'cidade_id.required' => 'Campo Obrigatório',
            'numero_ultima_nfe_producao.required' => 'Campo Obrigatório',
            'numero_ultima_nfe_homologacao.required' => 'Campo Obrigatório',
            'numero_serie_nfe.required' => 'Campo Obrigatório',
            'numero_ultima_nfce_producao.required' => 'Campo Obrigatório',
            'numero_ultima_nfce_homologacao.required' => 'Campo Obrigatório',
            'numero_serie_nfce.required' => 'Campo Obrigatório'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function removerLogo(Request $request){
        try{
            $item = Empresa::findOrFail($request->empresa_id);
            $this->util->unlinkImage($item, '/logos');
            $item->logo = '';
            $item->save();
            session()->flash("flash_success", "Logo removida!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('config.index');
    }
}
