<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\PlanoPendente;
use App\Models\Plano;
use App\Models\Fornecedor;
use App\Models\PadraoTributacaoProduto;
use App\Models\CategoriaProduto;
use App\Models\Marca;
use App\Models\VariacaoModelo;
use App\Models\MercadoLivreConfig;
use App\Models\Nfe;
use App\Models\NaturezaOperacao;
use App\Models\ContadorEmpresa;
use App\Models\User;
use App\Models\UsuarioEmpresa;
use NFePHP\Common\Certificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Utils\EmpresaUtil;
use App\Models\UnidadeMedida;

class ContadorAdminController extends Controller
{
    protected $empresaUtil;

    public function __construct(EmpresaUtil $empresaUtil)
    {
        $this->empresaUtil = $empresaUtil;
    }
    
    public function setEmpresa($id){

        $contador = Empresa::findOrFail(request()->empresa_id);
        $contador->empresa_selecionada = $id;
        $contador->save();

        return redirect()->back();
    }

    public function show(){
        $contador = Empresa::findOrFail(request()->empresa_id);
        $item = Empresa::findOrFail($contador->empresa_selecionada);
        $dadosCertificado = null;

        if ($item != null && $item->arquivo) {
            $dadosCertificado = $this->getInfoCertificado($item);
        }

        $naturezas = NaturezaOperacao::where('empresa_id', $item->id)->get();
        
        // return view('config.index', compact('empresa', 'usuario', 'item', 'dadosCertificado', 'naturezas'));
        return view('contador.show', compact('item', 'naturezas', 'dadosCertificado'));
    }

    public function update(Request $request, $id)
    {
        $item = Empresa::findOrFail($id);
        try {
            $file_name = $item->logo;

            if ($request->hasFile('image')) {
                $this->util->unlinkImage($item, '/logos');
                $file_name = $this->util->uploadImage($request, '/logos');
            }

            $request->merge([
                # 'cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->cpf_cnpj),
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
        return redirect()->back();
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
            return [];
        }
    }

    public function produtos(Request $request){
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        $data = Produto::where('empresa_id', $empresaSelecionada)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where(function ($quer) use ($request) {
                return $quer->where('nome', 'LIKE', "%$request->nome%");
            });
        })
        ->when(!empty($request->codigo_barras), function ($q) use ($request) {
            return $q->where(function ($quer) use ($request) {
                return $quer->where('codigo_barras', 'LIKE', "%$request->codigo_barras%");
            });
        })
        ->paginate(env("PAGINACAO"));

        return view('contador.produtos', compact('data'));
    }

    public function produtoShow($id){

        $item = Produto::findOrFail($id);
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)->get();
        $cardapio = 0;
        if (isset($request->cardapio)) {
            $cardapio = 1;
        }
        $marcas = Marca::where('empresa_id', request()->empresa_id)->get();
        $variacoes = VariacaoModelo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $configMercadoLivre = MercadoLivreConfig::where('empresa_id', request()->empresa_id)
        ->first();

        $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        return view('contador.produtos_show', 
            compact('item', 'listaCTSCSOSN', 'padroes', 'categorias', 'cardapio', 'marcas', 'variacoes', 'configMercadoLivre',
                'unidades'));
    }

    public function clientes(Request $request){
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        $data = Cliente::where('empresa_id', $empresaSelecionada)
        ->when(!empty($request->razao_social), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('razao_social', 'LIKE', "%$request->razao_social%");
            });
        })
        ->when(!empty($request->cpf_cnpj), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('cpf_cnpj', 'LIKE', "%$request->cpf_cnpj%");
            });
        })
        ->paginate(env("PAGINACAO"));
        return view('contador.clientes', compact('data'));

    }

    public function fornecedores(Request $request){
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        $data = Fornecedor::where('empresa_id', $empresaSelecionada)
        ->when(!empty($request->razao_social), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('razao_social', 'LIKE', "%$request->razao_social%");
            });
        })
        ->when(!empty($request->cpf_cnpj), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('cpf_cnpj', 'LIKE', "%$request->cpf_cnpj%");
            });
        })
        ->paginate(env("PAGINACAO"));
        return view('contador.fornecedores', compact('data'));

    }

    public function empresaCreate(){
        $contador = Empresa::findOrFail(request()->empresa_id);

        if(sizeof(__empresasDoContador()) >= $contador->limite_cadastro_empresas){
            session()->flash("flash_error", "Você atingiu o limite de cadastro de empresas");
            return redirect()->back();
        }

        return view('contador.create_empresa', compact('contador'));
    }

    public function empresaStore(Request $request){
        $this->__validate($request);

        try{
            DB::transaction(function () use ($request) {
                $contador = Empresa::findOrFail(request()->empresa_id);

                if ($request->hasFile('certificado')) {
                    $file = $request->file('certificado');
                    $fileTemp = file_get_contents($file);
                    $request->merge([
                        'arquivo' => $fileTemp ?? '',
                    // 'cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->cpf_cnpj),
                        'senha' => $request->senha_certificado,
                        'token' => $request->token ?? '',
                    ]);
                }

                $email = $request->email;
                $request->merge([
                    'email' => $request->email_empresa
                ]);

                $empresa = Empresa::create($request->all());
                $this->empresaUtil->initLocation($empresa);
                
                if ($request->usuario) {

                    $usuario = User::create([
                        'name' => $request->usuario ?? null,
                        'email' => $email ?? null,
                        'password' => Hash::make($request['password']) ?? '',
                        'remember_token' => Hash::make($request['remember_token']) ?? ''
                    ]);

                    UsuarioEmpresa::create([
                        'empresa_id' => $empresa->id,
                        'usuario_id' => $usuario->id ?? null
                    ]);
                }

                ContadorEmpresa::create([
                    'empresa_id' => $empresa->id,
                    'contador_id' => $contador->id
                ]);

                return true;
            });
            session()->flash("flash_success", "Empresa cadastrada!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('home');

    }

    private function __validate(Request $request)
    {
        $rules = [
            'nome' => 'required',
            'cpf_cnpj' => 'required',
            'ie' => 'required',
            'celular' => 'required',
            'csc' => 'required',
            'csc_id' => 'required',
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade_id' => 'required',
            'numero_ultima_nfe_producao' => 'required',
            'numero_ultima_nfe_homologacao' => 'required',
            'numero_serie_nfe' => 'required',
            'numero_ultima_nfce_producao' => 'required',
            'numero_ultima_nfce_homologacao' => 'required',
            'numero_serie_nfce' => 'required',
            'numero_ultima_cte_producao' => 'required',
            'numero_ultima_cte_homologacao' => 'required',
            'numero_serie_cte' => 'required',
            'email' => 'unique:users',
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
            'numero_serie_nfce.required' => 'Campo Obrigatório',
            'email.unique' => 'Já existe um usuário com este email',
        ];
        $this->validate($request, $rules, $messages);
    }

    public function plano($id){
        $item = Empresa::findOrFail($id);
        $planos = Plano::where('visivel_contadores', 1)->get();

        return view('contador.plano', compact('item', 'planos'));
    }

    public function setPlano(Request $request, $id){
        try{

            PlanoPendente::create([
                'plano_id' => $request->plano_id,
                'valor' => __convert_value_bd($request->valor),
                'empresa_id' => $id,
                'contador_id' => request()->empresa_id
            ]);
            session()->flash("flash_success", "Plano solicitado, aguarde a liberação do administrador!");

        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('home');
    }

}
