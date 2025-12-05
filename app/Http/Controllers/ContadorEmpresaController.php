<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContadorEmpresa;
use App\Models\Empresa;
use App\Models\User;
use App\Models\UsuarioEmpresa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Utils\EmpresaUtil;
use App\Utils\UploadUtil;
use NFePHP\Common\Certificate;

class ContadorEmpresaController extends Controller
{
    protected $empresaUtil;
    protected $uploadUtil;

    public function __construct(EmpresaUtil $empresaUtil, UploadUtil $uploadUtil)
    {
        $this->empresaUtil = $empresaUtil;
        $this->uploadUtil = $uploadUtil;
    }

    public function index(Request $request){
        $data = Empresa::select('empresas.*')
        ->join('contador_empresas', 'contador_empresas.empresa_id', '=', 'empresas.id')
        ->where('contador_empresas.contador_id', $request->empresa_id)->get();

        return view('contador_empresas.index', compact('data'));
    }

    public function create(){
        $contador = Empresa::findOrFail(request()->empresa_id);

        if(sizeof(__empresasDoContador()) >= $contador->limite_cadastro_empresas){
            session()->flash("flash_error", "Você atingiu o limite de cadastro de empresas");
            return redirect()->back();
        }

        return view('contador_empresas.create', compact('contador'));
    }

    public function edit($id){
        $item = Empresa::findOrFail($id);

        $infoCertificado = null;
        if ($item != null && $item->arquivo != null) {
            $infoCertificado = $this->getInfoCertificado($item);
        }

        return view('contador_empresas.edit', compact('item', 'infoCertificado'));
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
            // echo $e->getMessage();
            // die;
            return [];
        }
    }

    public function store(Request $request){
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
        return redirect()->route('contador-empresas.index');

    }

    public function update(Request $request, $id)
    {
        $item = Empresa::findOrFail($id);

        try {
            // $request->merge([
            //     'cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->cpf_cnpj)
            // ]);
            if ($request->hasFile('certificado')) {

                $file = $request->file('certificado');
                $fileTemp = file_get_contents($file);
                $request->merge([
                    'arquivo' => $fileTemp,
                    'senha' => $request->senha_certificado
                ]);
            }

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Empresa atualizada!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('contador-empresas.index');

    }

    private function __validate(Request $request)
    {
        $rules = [
            'nome' => 'required',
            'cpf_cnpj' => 'required',
            'ie' => 'required',
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
            // 'numero_serie_nfce' => 'required',
            // 'numero_ultima_cte_producao' => 'required',
            // 'numero_ultima_cte_homologacao' => 'required',
            // 'numero_serie_cte' => 'required',
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

    public function destroy($id)
    {

        $item = Empresa::findOrFail($id);
        try {

            foreach($item->usuarios as $u){
                $u->usuario->acessos()->delete();
            }
            $item->usuarios()->delete();
            // $item->user()->delete();
            $item->plano()->delete();
            $this->deleteRegistros($item->id);

            $item->delete();
            session()->flash("flash_success", "Empresa removida!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    private function deleteRegistros($empresa_id){

        \App\Models\ContaPagar::where('empresa_id', $empresa_id)->delete();

        \App\Models\ContaReceber::where('empresa_id', $empresa_id)->delete();
        \App\Models\MotoboyComissao::where('empresa_id', $empresa_id)->delete();
        \App\Models\ComissaoVenda::where('empresa_id', $empresa_id)->delete();
        \App\Models\EscritorioContabil::where('empresa_id', $empresa_id)->delete();
        \App\Models\Sped::where('empresa_id', $empresa_id)->delete();
        \App\Models\CarrosselCardapio::where('empresa_id', $empresa_id)->delete();
        \App\Models\SpedConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\ConfiguracaoCardapio::where('empresa_id', $empresa_id)->delete();
        \App\Models\Pagamento::where('empresa_id', $empresa_id)->delete();
        \App\Models\MercadoLivreConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\MercadoLivrePergunta::where('empresa_id', $empresa_id)->delete();
        \App\Models\ContratoEmpresa::where('empresa_id', $empresa_id)->delete();
        \App\Models\Contigencia::where('empresa_id', $empresa_id)->delete();
        \App\Models\FuncionamentoDelivery::where('empresa_id', $empresa_id)->delete();
        \App\Models\MetaResultado::where('empresa_id', $empresa_id)->delete();

        $prevendas = \App\Models\PreVenda::where('empresa_id', $empresa_id)->get();
        foreach($prevendas as $n){
            $n->itens()->delete();
            $n->fatura()->delete();
            $n->delete();
        }

        $data = \App\Models\PedidoMercadoLivre::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }

        $data = \App\Models\ContaEmpresa::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }
        
        $data = \App\Models\WoocommercePedido::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }

        $data = \App\Models\EnderecoDelivery::where('clientes.empresa_id', $empresa_id)
        ->select('endereco_deliveries.*')
        ->join('clientes', 'endereco_deliveries.cliente_id', '=', 'clientes.id')->get();
        foreach($data as $t){
            $t->delete();
        }

        $data = \App\Models\PedidoDelivery::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            foreach($t->itens as $it){
                $it->adicionais()->delete();
                $it->pizzas()->delete();
                $it->delete();
            }
            $t->delete();
        }

        $data = \App\Models\CarrinhoDelivery::where('empresa_id', $empresa_id)->get();
        // $data = \App\Models\CarrinhoDelivery::all();
        foreach($data as $t){
            foreach($t->itens as $it){
                $it->adicionais()->delete();
                $it->sabores()->delete();
                $it->pizzas()->delete();
                $it->delete();
            }
            $t->delete();
        }

        $data = \App\Models\EnderecoEcommerce::where('clientes.empresa_id', $empresa_id)
        ->select('endereco_ecommerces.*')
        ->join('clientes', 'endereco_ecommerces.cliente_id', '=', 'clientes.id')->get();
        foreach($data as $t){
            $t->delete();
        }

        $data = \App\Models\Troca::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }

        $data = \App\Models\ListaPreco::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->usuarios()->delete();
            $t->delete();
        }

        $data = \App\Models\Agendamento::where('empresa_id', $empresa_id)->get();
        foreach($data as $a){
            $a->itens()->delete();
            $a->delete();
        }

        $data = \App\Models\ApuracaoMensal::where('funcionarios.empresa_id', $empresa_id)
        ->select('apuracao_mensals.*')
        ->join('funcionarios', 'apuracao_mensals.funcionario_id', '=', 'funcionarios.id')->get();
        foreach($data as $a){
            $a->eventos()->delete();
            $a->delete();
        }

        $data = \App\Models\NotaServico::where('empresa_id', $empresa_id)->get();
        foreach($data as $a){
            $a->servico()->delete();
            $a->delete();
        }

        $data = \App\Models\Cte::where('empresa_id', $empresa_id)->get();
        foreach($data as $n){
            $n->chaves_nfe()->delete();
            $n->componentes()->delete();
            $n->medidas()->delete();
            $n->medidas()->delete();
            $n->delete();
        }

        $data = \App\Models\CrmAnotacao::where('empresa_id', $empresa_id)->get();
        foreach($data as $n){
            $n->notas()->delete();

            $n->delete();
        }

        $data = \App\Models\CteOs::where('empresa_id', $empresa_id)->get();
        foreach($data as $n){
            $n->percurso()->delete();
            $n->delete();
        }

        $data = \App\Models\Mdfe::where('empresa_id', $empresa_id)->get();
        foreach($data as $n){
            $n->municipiosCarregamento()->delete();
            $n->ciots()->delete();
            $n->percurso()->delete();
            $n->valesPedagio()->delete();
            $n->infoDescarga()->delete();
            $n->delete();
        }

        $nfe = \App\Models\Nfe::where('empresa_id', $empresa_id)->get();
        foreach($nfe as $n){
            $n->itens()->delete();
            $n->fatura()->delete();
            $n->delete();
        }

        $nfce = \App\Models\Nfce::where('empresa_id', $empresa_id)->get();
        foreach($nfce as $n){
            $n->itens()->delete();
            $n->itensServico()->delete();
            $n->fatura()->delete();
            $n->delete();
        }

        $carrinhos = \App\Models\Carrinho::where('empresa_id', $empresa_id)->get();
        foreach($carrinhos as $c){
            $c->itens()->delete();
            $c->delete();
        }

        $data = \App\Models\OrdemServico::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->servicos()->delete();
            $t->relatorios()->delete();
            $t->funcionarios()->delete();
            $t->delete();
        }

        \App\Models\DiaSemana::where('empresa_id', $empresa_id)->delete();
        \App\Models\FuncionarioServico::where('empresa_id', $empresa_id)->delete();
        \App\Models\Servico::where('empresa_id', $empresa_id)->delete();
        \App\Models\Veiculo::where('empresa_id', $empresa_id)->delete();

        $funcionarios = \App\Models\Funcionario::where('empresa_id', $empresa_id)->get();
        foreach($funcionarios as $f){
            $f->eventos()->delete();
            $f->funcionamento()->delete();
            $f->interrupcoes()->delete();
            $f->delete();
        }

        $data = \App\Models\Reserva::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->consumoProdutos()->delete();
            $t->consumoServicos()->delete();
            $t->notas()->delete();
            $t->hospedes()->delete();
            $t->fatura()->delete();
            $t->delete();
        }

        $data = \App\Models\Ticket::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->mensagens()->delete();
            $t->delete();
        }

        $data = \App\Models\TransferenciaEstoque::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }

        $data = \App\Models\Cotacao::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->fatura()->delete();
            $t->delete();
        }

        $data = \App\Models\Inventario::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }
        
        $data = \App\Models\Pedido::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }

        $data = \App\Models\PedidoEcommerce::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }
        
        \App\Models\CashBackConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\CashBackCliente::where('empresa_id', $empresa_id)->delete();

        $data = \App\Models\Cliente::where('empresa_id', $empresa_id)->get();
        foreach($data as $c){
            $c->enderecos()->delete();
            $c->enderecosEcommerce()->delete();
            $c->enderecosDelivery()->delete();
            $c->tributacao()->delete();
            $c->delete();
        }

        $data = \App\Models\Caixa::where('empresa_id', $empresa_id)->get();
        foreach($data as $c){
            $c->suprimentos()->delete();
            $c->sangrias()->delete();
            $c->delete();
        }
        
        \App\Models\MotivoInterrupcao::where('empresa_id', $empresa_id)->delete();
        \App\Models\Notificacao::where('empresa_id', $empresa_id)->delete();
        \App\Models\EcommerceConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\MarketPlaceConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\ModeloEtiqueta::where('empresa_id', $empresa_id)->delete();
        $data = \App\Models\VariacaoModelo::where('empresa_id', $empresa_id)->get();
        foreach($data as $c){
            $c->itens()->delete();
            $c->delete();
        }

        $data = \App\Models\VendaSuspensa::where('empresa_id', $empresa_id)->get();
        foreach($data as $c){
            $c->itens()->delete();
            $c->delete();
        }

        $produtos = \App\Models\Produto::where('empresa_id', $empresa_id)->get();
        \App\Models\ProdutoCombo::
        select('produto_combos.*')
        ->join('produtos', 'produtos.id', '=', 'produto_combos.produto_id')
        ->where('produtos.empresa_id', $empresa_id)->delete();

        foreach($produtos as $p){
            $p->movimentacoes()->delete();
            $p->locais()->delete();
            $p->locais()->delete();
            $p->variacoes()->delete();
            $p->adicionais()->delete();
            $p->composicao()->delete();
            $p->ingredientes()->delete();
            $p->pizzaValores()->delete();
            $p->fornecedores()->delete();

            if($p->estoque){
                $p->estoque->delete();
            }
            $p->delete();
        }

        \App\Models\ConfigGeral::where('empresa_id', $empresa_id)->delete();
        \App\Models\AcaoLog::where('empresa_id', $empresa_id)->delete();
        \App\Models\PlanoPendente::where('empresa_id', $empresa_id)->delete();
        \App\Models\Marca::where('empresa_id', $empresa_id)->delete();
        
        \App\Models\CategoriaProduto::where('empresa_id', $empresa_id)->delete();

        \App\Models\Fornecedor::where('empresa_id', $empresa_id)->delete();
        \App\Models\NuvemShopConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\NaturezaOperacao::where('empresa_id', $empresa_id)->delete();
        \App\Models\PadraoTributacaoProduto::where('empresa_id', $empresa_id)->delete();
        \App\Models\Role::where('empresa_id', $empresa_id)->delete();
        \App\Models\FinanceiroPlano::where('empresa_id', $empresa_id)->delete();
        \App\Models\ContadorEmpresa::where('empresa_id', $empresa_id)->delete();
        \App\Models\DiaSemana::where('empresa_id', $empresa_id)->delete();

        $usuarios = UsuarioEmpresa::where('empresa_id', $empresa_id)->get();
        // echo $usuarios;
        // die;
        
        \App\Models\UsuarioLocalizacao::
        select('usuario_localizacaos.*')
        ->join('localizacaos', 'localizacaos.id', '=', 'usuario_localizacaos.localizacao_id')
        ->where('localizacaos.empresa_id', $empresa_id)->delete();

        \App\Models\Localizacao::where('empresa_id', $empresa_id)->delete();
        $planos = \App\Models\PlanoConta::where('empresa_id', $empresa_id)
        ->orderBy('descricao', 'desc')
        ->get();

        foreach($planos as $t){
            $t->delete();
        }

    }
}
