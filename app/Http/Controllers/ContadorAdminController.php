<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Produto;
use App\Models\TributacaoCliente;
use App\Models\Cliente;
use App\Models\PlanoPendente;
use App\Models\Plano;
use App\Models\Fornecedor;
use App\Models\ListaPrecoUsuario;
use App\Models\Nfce;
use App\Models\PadraoTributacaoProduto;
use App\Models\ProdutoVariacao;
use App\Models\CategoriaProduto;
use App\Models\Marca;
use App\Models\VariacaoModelo;
use App\Models\MercadoLivreConfig;
use App\Models\Nfe;
use App\Models\NaturezaOperacao;
use App\Models\ContadorEmpresa;
use App\Models\User;
use App\Models\FaturaCliente;
use App\Models\ConfigGeral;
use App\Models\UsuarioEmpresa;
use NFePHP\Common\Certificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Utils\EmpresaUtil;
use App\Utils\UploadUtil;
use App\Models\UnidadeMedida;

class ContadorAdminController extends Controller
{
    protected $empresaUtil;
    protected $uploadUtil;

    public function __construct(EmpresaUtil $empresaUtil, UploadUtil $uploadUtil)
    {
        $this->empresaUtil = $empresaUtil;
        $this->uploadUtil = $uploadUtil;
    }
    
    public function setEmpresa($id){

        $contador = Empresa::findOrFail(request()->empresa_id);
        $contador->empresa_selecionada = $id;
        $contador->save();

        return redirect()->back();
    }

    public function profile(Request $request){
        $contador = Empresa::findOrFail(request()->empresa_id);

        if($contador->usuarios[0] == null){
            die;
        }

        $item = $contador->usuarios[0]->usuario;
        return view('contador.profile', compact('contador', 'item'));
    }

    public function profileUpdate(Request $request, $id){
        $usuario = User::findOrFail($id);
        try {

            $file_name = $usuario->imagem;

            if ($request->hasFile('image')) {
                $this->uploadUtil->unlinkImage($usuario, '/usuarios');
                $file_name = $this->uploadUtil->uploadImage($request, '/usuarios');
            }

            if ($request->password) {
                $request->merge([
                    'password' => Hash::make($request->password),
                    'imagem' => $file_name
                ]);
            } else {
                $request->merge([
                    'password' => $usuario->password,
                    'imagem' => $file_name
                ]);
            }

            $usuario->fill($request->all())->save();
            session()->flash("flash_success", "Usuário alterado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('home');
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
        ->paginate(__itensPagina());

        return view('contador.produtos', compact('data'));
    }

    public function produtoShow($id){

        $item = Produto::findOrFail($id);
        $empresa = Empresa::findOrFail($item->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        $padroes = PadraoTributacaoProduto::where('empresa_id', $item->empresa_id)->get();
        $categorias = CategoriaProduto::where('empresa_id', $item->empresa_id)->where('status', 1)->get();
        $cardapio = 0;
        if (isset($request->cardapio)) {
            $cardapio = 1;
        }
        $marcas = Marca::where('empresa_id', $item->empresa_id)->get();
        $variacoes = VariacaoModelo::where('empresa_id', $item->empresa_id)
        ->where('status', 1)->get();

        $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $item->empresa_id)
        ->first();

        $unidades = UnidadeMedida::where('empresa_id', $item->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)
        ->first();

        return view('contador.produtos_show', 
            compact('item', 'listaCTSCSOSN', 'padroes', 'categorias', 'cardapio', 'marcas', 'variacoes', 'configMercadoLivre',
                'unidades', 'configGeral'));
    }

    public function produtoEdit($id){

        $item = Produto::findOrFail($id);

        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);
        $empresa = Empresa::findOrFail($item->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        $padroes = PadraoTributacaoProduto::where('empresa_id', $item->empresa_id)->get();
        $categorias = CategoriaProduto::where('empresa_id', $item->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();
        $cardapio = 0;
        if (isset($request->cardapio)) {
            $cardapio = 1;
        }
        $marcas = Marca::where('empresa_id', $item->empresa_id)->get();
        $variacoes = VariacaoModelo::where('empresa_id', $item->empresa_id)
        ->where('status', 1)->get();

        $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $item->empresa_id)
        ->first();

        $unidades = UnidadeMedida::where('empresa_id', $item->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)
        ->first();

        return view('contador.produtos_edit', 
            compact('item', 'listaCTSCSOSN', 'padroes', 'categorias', 'cardapio', 'marcas', 'variacoes', 'configMercadoLivre',
                'unidades', 'configGeral'));
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
        ->paginate(__itensPagina());
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
            return $q->where(function ($quer) use ($request) {
                return $quer->where('cpf_cnpj', 'LIKE', "%". preg_replace('/[^0-9]/', '', ($request->cpf_cnpj)) ."%");
            });
        })
        ->paginate(__itensPagina());
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
                $this->empresaUtil->initNaturezaTributacao($empresa);
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

    public function produtoUpdate(Request $request, $id){
        $item = Produto::findOrFail($id);
        try {
            $file_name = $item->imagem;

            if ($request->hasFile('image')) {
                $this->util->unlinkImage($item, '/produtos');
                $file_name = $this->uploadUtil->uploadImage($request, '/produtos');
            }

            $request->merge([
                'valor_unitario' => __convert_value_bd($request->valor_unitario),
                'valor_prazo' => __convert_value_bd($request->valor_prazo),
                'valor_compra' => $request->valor_compra ? __convert_value_bd($request->valor_compra) : 0,
                'valor_minimo_venda' => $request->valor_minimo_venda ? __convert_value_bd($request->valor_minimo_venda) : 0,
                'imagem' => $file_name,
                'codigo_anp' => $request->codigo_anp ?? '',
                'perc_glp' => $request->perc_glp ? __convert_value_bd($request->perc_glp) : 0,
                'perc_gnn' => $request->perc_gnn ? __convert_value_bd($request->perc_gnn) : 0,
                'perc_gni' => $request->perc_gni ? __convert_value_bd($request->perc_gni) : 0,
                'valor_partida' => $request->valor_partida ? __convert_value_bd($request->valor_partida) : 0,
                'unidade_tributavel' => $request->unidade_tributavel ?? '',
                'quantidade_tributavel' => $request->quantidade_tributavel ? __convert_value_bd($request->quantidade_tributavel) : 0,
                'adRemICMSRet' => $request->adRemICMSRet ? __convert_value_bd($request->adRemICMSRet) : 0,
                'pBio' => $request->pBio ? __convert_value_bd($request->pBio) : 0,
                'pOrig' => $request->pOrig ? __convert_value_bd($request->pOrig) : 0,
                'indImport' => $request->indImport ?? '',
                'cUFOrig' => $request->cUFOrig ?? '',

                'perc_icms' => $request->perc_icms ?? 0,
                'perc_pis' => $request->perc_pis ?? 0,
                'perc_cofins' => $request->perc_cofins ?? 0,
                'perc_ipi' => $request->perc_ipi ?? 0,
                'cfop_estadual' => $request->cfop_estadual ?? '',
                'cfop_outro_estado' => $request->cfop_outro_estado ?? '',
                'valor_combo' => $request->valor_combo ? __convert_value_bd($request->valor_combo) : 0,
                'margem_combo' => $request->margem_combo ? __convert_value_bd($request->margem_combo) : 0,
                'valor_atacado' => $request->valor_atacado ? __convert_value_bd($request->valor_atacado) : 0,
                'empresa_id' => $item->empresa_id
            ]);

            $item->fill($request->all())->save();
            if($request->variavel){

            // $item->variacoes()->delete();
                $variacaoDelete = [];

                for($i=0; $i<sizeof($request->valor_venda_variacao); $i++){
                    $dataVariacao = [
                        'produto_id' => $item->id,
                        'descricao' => $request->descricao_variacao[$i],
                        'valor' => __convert_value_bd($request->valor_venda_variacao[$i]),
                        'codigo_barras' => $request->codigo_barras_variacao[$i],
                        'referencia' => $request->referencia_variacao[$i],        
                    ];
                    if(isset($request->variacao_id[$i])){
                        $variacao = ProdutoVariacao::findOrfail($request->variacao_id[$i]);

                        $file_name = $variacao->imagem;

                        if(isset($request->imagem_variacao[$i])){

                            if($file_name != null){
                                $this->util->unlinkImage($variacao, '/produtos');
                            }
                            $imagem = $request->imagem_variacao[$i];
                            $file_name = $this->util->uploadImageArray($imagem, '/produtos');

                        }
                        $dataVariacao['imagem'] = $file_name;

                        $variacao->fill($dataVariacao)->save();
                        $variacaoDelete[] = $request->variacao_id[$i];
                    }else{
                        $file_name = '';
                        if(isset($request->imagem_variacao[$i])){
                            $imagem = $request->imagem_variacao[$i];
                            $file_name = $this->util->uploadImageArray($imagem, '/produtos');
                        }
                        $dataVariacao['imagem'] = $file_name;
                        $v = ProdutoVariacao::create($dataVariacao);
                        $variacaoDelete[] = $v->id;
                    }
                }
                foreach($item->variacoes as $v){
                    if(!in_array($v->id, $variacaoDelete)){

                        $itemNfce = \App\Models\ItemNfce::where('variacao_id', $v->id)
                        ->first();
                        $itemNfe = \App\Models\ItemNfe::where('variacao_id', $v->id)
                        ->first();
                        if($itemNfce == null && $itemNfe == null){
                            if($v->estoque){
                                $v->estoque->delete();
                            }
                            if($v->movimentacaoProduto){
                                $v->movimentacaoProduto->delete();
                            }
                            $v->delete();
                        }else{
                            session()->flash("flash_error", "Esta variação $v->descricao já possui vendas ou compras não é possivel remover");
                            return redirect()->back();
                        }
                    }
                }
            }else{
                ProdutoVariacao::where('produto_id', $item->id)->delete();
            }

            session()->flash("flash_success", "Produto atualizado!");

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
        return redirect()->route('contador-empresa.produtos');
    }

    public function clienteEdit($id){
        $item = Cliente::findOrFail($id);

        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        $listasPreco = ListaPrecoUsuario::select('lista_precos.*')
        ->join('lista_precos', 'lista_precos.id', '=', 'lista_preco_usuarios.lista_preco_id')
        ->where('lista_preco_usuarios.usuario_id', get_id_user())
        ->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamento();
        if($config != null){
            $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($config->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $config->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }

        return view('contador.clientes_edit', compact('item', 'listasPreco', 'tiposPagamento'));
    }

    public function fornecedorEdit($id){
        $item = Fornecedor::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        return view('contador.fornecedores_edit', compact('item'));
    }

    public function clienteUpdate(Request $request, $id){
        $item = Cliente::findOrFail($id);
        try {
            $request->merge([
                'ie' => $request->ie ?? '',
                'valor_cashback' => $request->valor_cashback ? __convert_value_bd($request->valor_cashback) : 0,
                'valor_credito' => $request->valor_credito ? __convert_value_bd($request->valor_credito) : 0,
                'empresa_id' => $item->empresa_id
            ]);
            $item->fill($request->all())->save();

            $this->cadastraTributacao($item, $request);

            if($request->dias_vencimento[0] != ''){
                $item->fatura()->delete();
                for($i=0; $i<sizeof($request->dias_vencimento); $i++){
                    FaturaCliente::create([
                        'cliente_id' => $item->id,
                        'tipo_pagamento' => $request->tipo_pagamento[$i] ?? null,
                        'dias_vencimento' => $request->dias_vencimento[$i]
                    ]);
                }
            }

            session()->flash("flash_success", "Cliente atualizado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
        return redirect()->route('contador-empresa.clientes');
    }

    private function cadastraTributacao($cliente, Request $request){
        if($cliente->tributacao){
            $cliente->tributacao()->delete();
        }

        $request->merge([
            'cliente_id' => $cliente->id
        ]);

        TributacaoCliente::create($request->all());
    }

    public function fornecedorUpdate(Request $request, $id)
    {
        $item = Fornecedor::findOrFail($id);
        try {
            $request->merge([
                'ie' => $request->ie ?? '',
                'empresa_id' => $item->empresa_id
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Fornecedor atualizado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('contador-empresa.fornecedores');
    }

}
