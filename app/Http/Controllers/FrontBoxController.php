<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\CategoriaProduto;
use App\Models\Marca;
use App\Models\Empresa;
use App\Models\Nfce;
use App\Models\ConfigGeral;
use App\Models\Cliente;
use App\Models\ListaPreco;
use App\Models\PdvLog;
use App\Models\Produto;
use App\Models\Nfe;
use App\Models\VendaSuspensa;
use App\Models\Localizacao;
use App\Models\TefMultiPlusCard;
use App\Models\Mesa;
use App\Models\Garantia;
use App\Models\ConfiguracaoCardapio;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Contigencia;
use App\Models\UsuarioEmissao;
use App\Models\UsuarioEmpresa;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use NFePHP\DA\NFe\CupomNaoFiscal;
use App\Utils\EstoqueUtil;
use Illuminate\Support\Facades\Auth;
use App\Models\ComissaoVenda;
use App\Models\ItemNfce;
use Dompdf\Dompdf;

class FrontBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:pdv_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:pdv_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pdv_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:pdv_delete', ['only' => ['destroy']]);
    }

    private function setNumeroSequencial(){
        $docs = Nfce::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $last = Nfce::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        foreach($docs as $d){
            $d->numero_sequencial = $numero;
            $d->save();
            $numero++;
        }
    }

    private function getContigencia($empresa_id){
        $active = Contigencia::
        where('empresa_id', $empresa_id)
        ->where('status', 1)
        ->where('documento', 'NFCe')
        ->first();
        return $active;
    }

    private function corrigeNumeros($empresa_id){

        $config = ConfigGeral::where('empresa_id', $empresa_id)->first();
        if($config != null && $config->corrigir_numeracao_fiscal == 0){
            return;
        }
        
        $item = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
        ->select('usuario_emissaos.*')
        ->where('usuario_emissaos.usuario_id', get_id_user())
        ->first();

        if($item != null){
            return;
        }

        $locais = Localizacao::where('empresa_id', $empresa_id)->where('status', 1)->get();
        foreach($locais as $key => $local){
            $empresa = Empresa::findOrFail($empresa_id);
            $caixa = __isCaixaAberto();
            if($caixa){
                $empresa = __objetoParaEmissao($empresa, $local->id);
            }


            if($empresa->ambiente == 1){
                $numero = $empresa->numero_ultima_nfce_producao;
            }else{
                $numero = $empresa->numero_ultima_nfce_homologacao;
            }

            if($numero){
                Nfce::where(function($q) {
                    $q->where('estado', 'novo')->orWhere('estado', 'rejeitado');
                })
                ->where('empresa_id', $empresa_id)
                ->where('local_id', $local->id)
            // ->where('caixa_id', $caixa->id)
                ->update(['numero' => $numero+1]);
            }
        }
    }

    public function imprimirCarne($id){
        $item = Nfce::findOrFail($id);
        $config = Empresa::findOrFail(request()->empresa_id);

        $p = view('front_box.carne', compact('config', 'item'));
        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Carnê de venda.pdf", array("Attachment" => false));

    }

    public function index(Request $request)
    {
        $this->setNumeroSequencial();
        $this->corrigeNumeros($request->empresa_id);

        $user = User::findOrFail(Auth::user()->id);
        $adm = $user->admin;

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $user_id = $request->get('user_id');
        $estado = $request->get('estado');

        $query = Nfce::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(!empty($user_id), function ($query) use ($user_id) {
            return $query->where('user_id', $user_id);
        })
        ->when($adm == 0, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc');

        $somaGeral = $query->sum('total');
        $data = $query->paginate(__itensPagina());

        $contigencia = $this->getContigencia(request()->empresa_id);

        $usuarios = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
        $envioWppLink = 0;
        if($config != null && $config->status_wpp_link){
            $envioWppLink = 1;
        }

        return view('front_box.index', compact('data', 'contigencia', 'usuarios', 'adm', 'somaGeral', 'envioWppLink'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $ua = $request->header('User-Agent');

        if (preg_match('/Android|iPhone|iPad|iPod|Mobile/i', $ua) && file_exists(public_path('style_pdv_mobo.css'))) {
            return redirect()->route('pdv-mobo.index');
        }
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $caixa = __isCaixaAberto();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();

        $abertura = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($config->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
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

        $item = null;
        $itenSuspensa = [];
        $isVendaSuspensa = 0;
        $isOrcamento = 0;
        $title = 'Nova Venda - PDV';

        if(isset($request->venda_suspensa)){
            $item = VendaSuspensa::findOrfail($request->venda_suspensa);
            $isVendaSuspensa = 1;
            $title = 'Venda Suspensa';
            foreach($item->itens as $i){
                $itenSuspensa[] = [
                    '_id' => rand(1, 10000000000),
                    'produto_id' => $i->produto_id,
                    'produto_nome' => $i->produto->nome,
                    'valor_unitario' => $i->valor_unitario,
                    'sub_total' => $i->sub_total,
                    'quantidade' => $i->quantidade,
                ];
            }
        }

        if(isset($request->orcamento)){
            $item = Nfe::findOrfail($request->orcamento);

            $isOrcamento = 1;
            $title = 'Orçamento';
            foreach($item->itens as $i){
                $itenSuspensa[] = [
                    '_id' => rand(1, 10000000000),
                    'produto_id' => $i->produto_id,
                    'produto_nome' => $i->produto->nome,
                    'valor_unitario' => $i->valor_unitario,
                    'sub_total' => $i->sub_total,
                    'quantidade' => $i->quantidade,
                ];
            }
        }

        $configTef = TefMultiPlusCard::where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', Auth::user()->id)
        ->first();

        $view = 'front_box.create';
        $produtos = [];
        $marcas = [];
        if($config != null && $config->modelo == 'compact'){
            $view = 'front_box.create2';
            $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
            ->where('categoria_id', null)
            ->orderBy('nome', 'asc')
            ->where('status', 1)
            ->paginate(4);

            $marcas = Marca::where('empresa_id', request()->empresa_id)
            ->orderBy('nome', 'asc')
            ->paginate(4);

            $produtos = Produto::select('produtos.*', \DB::raw('sum(quantidade) as quantidade'))
            ->where('empresa_id', request()->empresa_id)
            ->where('produtos.status', 1)
            ->where('status', 1)
            ->leftJoin('item_nfces', 'item_nfces.produto_id', '=', 'produtos.id')
            ->groupBy('produtos.id')
            ->orderBy('quantidade', 'desc')
            ->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $caixa->localizacao->id)
            ->paginate(12);
        }

        $empresa = null;
        $user = null;
        $clientes = null;
        $listasPreco = null;
        $clientePadrao = null;

        if($config != null && $config->modelo == 'quick'){
            $view = 'front_box.create3';

            if($config->cliente_padrao_pdv_off){
                $clientePadrao = Cliente::findOrFail($config->cliente_padrao_pdv_off);
            }
            $empresa = Empresa::findOrFail(request()->empresa_id);
            $user = User::findOrFail(Auth::user()->id);
            $clientes = Cliente::where('empresa_id', $request->empresa_id)
            ->where('status', 1)->orderBy('razao_social')->get();

            $produtos = Produto::where('empresa_id', $request->empresa_id)
            ->select('id', 'numero_sequencial', 'codigo_barras', 'referencia', 'referencia_balanca', 'valor_unitario', 'nome', 'categoria_id', 
                'imagem', 'gerenciar_estoque', 'valor_atacado', 'valor_minimo_venda', 'quantidade_atacado')
            ->where('status', 1)->orderBy('nome')->get();

            foreach($produtos as $p){
                $p['valor_original'] = $p->valor_unitario;
                foreach($p->itemLista as $i){
                    $p['valor_lista_'.$i->lista_id] = $i->valor;
                }

                if($p->precoComPromocao()){
                    $p->valor_unitario = $p->precoComPromocao()->valor;
                    $p->promocao = " <br>promoção <strong class='text-primary'>" . __data_pt($p->precoComPromocao()->data_inicio, 0) . "</strong> até <strong class='text-primary'>" . __data_pt($p->precoComPromocao()->data_fim, 0) . "</strong>";
                }
            }

            $listasPreco = ListaPreco::orderBy('nome', 'desc')
            ->select('lista_precos.id', 'lista_precos.nome')
            ->where('empresa_id', $request->empresa_id)
            ->where('lista_precos.status', 1)
            ->join('lista_preco_usuarios', 'lista_preco_usuarios.lista_preco_id', '=', 'lista_precos.id')
            ->where('lista_preco_usuarios.usuario_id', get_id_user())
            ->get();
        }

        $local_id = $caixa->local_id;

        return view($view, compact('categorias', 'abertura', 
            'funcionarios', 'caixa', 'config', 'tiposPagamento', 'item', 'isVendaSuspensa', 'title', 
            'configTef', 'marcas', 'produtos', 'local_id', 'empresa', 'user', 'clientes', 'itenSuspensa', 'listasPreco', 
            'clientePadrao', 'isOrcamento'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Nfce::findOrFail($id);
        __validaObjetoEmpresa($data);

        return view('front_box.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Nfce::
        with(['itens', 'cliente'])
        ->findOrFail($id);

        __validaObjetoEmpresa($item);

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();

        $abertura = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($config->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }

        $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
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

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $cliente = $item->cliente;
        $funcionario = $item->funcionario;
        $caixa = __isCaixaAberto();
        $isVendaSuspensa = 0;
        $config = Empresa::findOrFail(request()->empresa_id);

        $view = 'front_box.edit';
        $produtos = [];
        $marcas = [];
        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        

        if($config != null && $config->modelo == 'compact'){
            $view = 'front_box.edit2';
            $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
            ->where('categoria_id', null)
            ->orderBy('nome', 'asc')
            ->where('status', 1)
            ->paginate(4);

            $marcas = Marca::where('empresa_id', request()->empresa_id)
            ->orderBy('nome', 'asc')
            ->paginate(4);

            $produtos = Produto::select('produtos.*', \DB::raw('sum(quantidade) as quantidade'))
            ->where('empresa_id', request()->empresa_id)
            ->where('produtos.status', 1)
            ->where('status', 1)
            ->leftJoin('item_nfces', 'item_nfces.produto_id', '=', 'produtos.id')
            ->groupBy('produtos.id')
            ->orderBy('quantidade', 'desc')
            ->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $caixa->localizacao->id)
            ->paginate(12);
        }

        $empresa = null;
        $user = null;
        $clientes = null;
        $listasPreco = null;
        $itensVenda = null;
        $title = 'Editar Venda - PDV';

        if($config != null && $config->modelo == 'quick'){
            $view = 'front_box.create3';

            $empresa = Empresa::findOrFail(request()->empresa_id);
            $user = User::findOrFail(Auth::user()->id);
            $clientes = Cliente::where('empresa_id', request()->empresa_id)
            ->where('status', 1)->orderBy('razao_social')->get();

            $produtos = Produto::where('empresa_id', request()->empresa_id)
            ->select('id', 'numero_sequencial', 'codigo_barras', 'referencia', 'referencia_balanca', 'valor_unitario', 'nome', 'categoria_id', 
                'imagem', 'gerenciar_estoque')
            ->where('status', 1)->orderBy('nome')->get();

            foreach($produtos as $p){
                $p['valor_original'] = $p->valor_unitario;
                foreach($p->itemLista as $i){
                    $p['valor_lista_'.$i->lista_id] = $i->valor;
                }

                if($p->precoComPromocao()){
                    $p->valor_unitario = $p->precoComPromocao()->valor;
                    $p->promocao = " \npromoção " . __data_pt($p->precoComPromocao()->data_inicio, 0) . " até " . __data_pt($p->precoComPromocao()->data_fim, 0);
                }
            }

            $listasPreco = ListaPreco::orderBy('nome', 'desc')
            ->select('lista_precos.id', 'lista_precos.nome')
            ->where('empresa_id', request()->empresa_id)
            ->where('lista_precos.status', 1)
            ->join('lista_preco_usuarios', 'lista_preco_usuarios.lista_preco_id', '=', 'lista_precos.id')
            ->where('lista_preco_usuarios.usuario_id', get_id_user())
            ->get();

            $itenSuspensa = [];
            foreach($item->itens as $i){
                $itensVenda[] = [
                    '_id' => rand(1, 10000000000),
                    'produto_id' => $i->produto_id,
                    'produto_nome' => $i->produto->nome,
                    'valor_unitario' => $i->valor_unitario,
                    'sub_total' => $i->sub_total,
                    'quantidade' => number_format($i->quantidade, $config->casas_decimais_quantidade, ',', ''),
                ];
            }
        }

        $local_id = $caixa->local_id;
        $isOrcamento = 0;

        return view($view, compact('categorias', 'abertura', 'funcionarios', 'item', 'cliente', 'funcionario', 
            'caixa', 'isVendaSuspensa', 'tiposPagamento', 'config', 'produtos', 'categorias', 'marcas', 'local_id', 'empresa',
            'user', 'clientes', 'listasPreco', 'itensVenda', 'title', 'isOrcamento'));
    }

    public function logs(Request $request){
        if(!__isAdmin()){
            abort(403);
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acao = $request->acao;
        $usuario_id = $request->usuario_id;
        $start_time = $request->start_time;
        $end_time = $request->end_time;

        if($start_date){
            if($start_time){
                $start_date .= " $start_time:59";
            }else{
                $start_date .= " 00:00:00";
            }
        }

        if($end_date){
            if($end_time){
                $end_date .= " $end_time:59";
            }else{
                $end_date .= " 23:59:59";
            }
        }

        $data = PdvLog::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($usuario_id), function ($query) use ($usuario_id) {
            return $query->where('usuario_id', $usuario_id);
        })
        ->when(!empty($acao), function ($query) use ($acao) {
            return $query->where('acao', $acao);
        })
        ->orderBy('id', 'desc')->paginate(__itensPagina());

        return view('front_box.logs', compact('data'));
    }
    
    public function destroy(string $id)
    {
        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = "#$item->numero_sequencial - R$ " . __moeda($item->total);
            foreach ($item->itens as $i) {
                if ($i->produto && $i->produto->gerenciar_estoque) {
                    $this->util->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
                }

                $i->adicionais()->delete();
                $i->pizzas()->delete();
            }

            $comissao = ComissaoVenda::where('empresa_id', $item->empresa_id)
            ->where('nfce_id', $item->id)->first();
            if($comissao){
                $comissao->delete();
            }

            $garantia = Garantia::where('empresa_id', $item->empresa_id)
            ->where('nfce_id', $item->id)->first();

            if($garantia){
                $garantia->delete();
            }

            $item->itens()->delete();
            $item->fatura()->delete();
            $item->contaReceber()->delete();
            $item->delete();


            __createLog(request()->empresa_id, 'PDV', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Venda removida!");
        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            __createLog(request()->empresa_id, 'PDV', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroySuspensa(string $id)
    {
        $item = VendaSuspensa::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $item->itens()->delete();
            $item->delete();
            session()->flash("flash_success", "Registro removido!");
        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->back();
    }

    // public function imprimirNaoFiscal($id)
    // {
    //     $item = Nfce::findOrFail($id);
    //     __validaObjetoEmpresa($item);

    //     $config = Empresa::where('id', $item->empresa_id)
    //     ->first();

    //     $config = __objetoParaEmissao($config, $item->local_id);

    //     $usuario = UsuarioEmpresa::find(get_id_user());
    //     $cupom = new CupomNaoFiscal($item, $config, 0);

    //     $logo = null;
    //     if($config->logo && file_exists(public_path('/uploads/logos/') . $config->logo)){
    //         $logo = public_path('/uploads/logos/') . $config->logo;
    //     }
    //     $pdf = $cupom->render($logo);
    //     header("Content-Disposition: ; filename=CUPOM.pdf");
    //     return response($pdf)
    //     ->header('Content-Type', 'application/pdf');
    // }

    public function imprimirNaoFiscal($id)
    {
        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresa($item);

        $config = Empresa::where('id', $item->empresa_id)
        ->first();

        $config = __objetoParaEmissao($config, $item->local_id);
        
        $usuario = UsuarioEmpresa::find(get_id_user());
        // $cupom = new CupomNaoFiscal($item, $config, 0);

        $logo = null;
        if($config->logo && file_exists(public_path('/uploads/logos/') . $config->logo)){
            $logo = public_path('/uploads/logos/') . $config->logo;
        }

        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();
        $p = view('front_box.cupom_nao_fiscal', compact('config', 'item', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $height = 360;

        $height += sizeof($item->itens)*11;
        foreach($item->itens as $it){
            if(strlen($it->descricao()) > 10){
                $height += 10;
            }
        }

        if($item->observacao != ''){
            $height += 30;
        }

        if($configGeral && $configGeral->mensagem_padrao_impressao_venda != ''){
            $height += 30;
        }

        $height += sizeof($item->fatura)*5;

        $domPdf->setPaper([0,0,244,$height]);
        $pdf = $domPdf->render();

        $domPdf->stream("Doc. Auxiliar $item->numero_sequencial.pdf", array("Attachment" => false));
    }

    public function imprimirNaoFiscalHtml($id)
    {
        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresa($item);

        $config = Empresa::where('id', $item->empresa_id)
        ->first();

        $config = __objetoParaEmissao($config, $item->local_id);
        
        $usuario = UsuarioEmpresa::find(get_id_user());

        $logo = null;
        
        return view('front_box.imprimir', compact('item', 'usuario', 'config'));
    }

    public function imprimirA4($id)
    {

        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresa($item);
        $config = Empresa::where('id', $item->empresa_id)->first();

        $config = __objetoParaEmissao($config, $item->local_id);
        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

        $p = view('front_box.imprimir_a4', compact('config', 'item', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        header("Content-Disposition: ; filename=Pedido.pdf");
        $domPdf->stream("Venda #$item->numero_sequencial.pdf", array("Attachment" => false));
    }

    public function mesas(Request $request){
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $comanda = null;
        $numeroComanda = $request->comanda;
        if($numeroComanda){
            $comanda = Pedido::where('comanda', (int)$numeroComanda)
            ->where('status', 1)
            ->where('empresa_id', $request->empresa_id)->first();
            
            if($comanda){
                $comanda->total = $comanda->itens->sum('sub_total');
                $comanda->save();
            }
        }

        $caixa = __isCaixaAberto();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)->get();

        $abertura = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $empresa = Empresa::findOrFail(request()->empresa_id);
        if($empresa == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($empresa->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
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
        if($config == null || $config->numero_inicial_comanda == null || $config->numero_final_comanda == null){
            session()->flash("flash_warning", "Defina a numeração inicial e final para as comandas!");
            return redirect()->route('config-geral.create');
        }
        $comandas = $this->estadosComanda($config);

        $produtos = Produto::select('produtos.*', \DB::raw('sum(quantidade) as quantidade'))
        ->where('empresa_id', request()->empresa_id)
        ->where('produtos.status', 1)
        ->where('status', 1)
        ->leftJoin('item_nfces', 'item_nfces.produto_id', '=', 'produtos.id')
        ->groupBy('produtos.id')
        ->orderBy('quantidade', 'desc')
        ->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
        ->where('produto_localizacaos.localizacao_id', $caixa->localizacao->id)
        ->paginate(12);
        $local_id = $caixa->local_id;

        $mesas = Mesa::where('status', 1)
        ->where('empresa_id', $request->empresa_id)
        ->orderBy('nome')
        ->get();

        $configCardapio = ConfiguracaoCardapio::where('empresa_id', request()->empresa_id)->first();

        return view('front_box.mesas', compact('categorias', 'empresa', 'config', 'funcionarios', 'tiposPagamento', 'comandas', 
            'produtos', 'local_id', 'abertura', 'caixa', 'comanda', 'numeroComanda', 'mesas', 'configCardapio'));
    }

    public function definirMesa(Request $request){
        $pedido = Pedido::findOrfail($request->comanda_id);
        if($pedido){
            $pedido->mesa_id = $request->mesa_id;
            $pedido->cliente_id = $request->cliente_id ?? null;
            $pedido->cliente_nome = $request->cliente_nome ?? null;
            $pedido->cliente_fone = $request->cliente_fone ?? null;
            $pedido->save();
            session()->flash("flash_success", "Mesa definida");
        }else{
            session()->flash("flash_warning", "Algo deu errado!");
        }
        return redirect()->back();
    }

    private function estadosComanda($config){
        $comandas = [];
        for($i=$config->numero_inicial_comanda; $i<=$config->numero_final_comanda; $i++){
            $pedido = Pedido::where('empresa_id', request()->empresa_id)
            ->where('status', 1)->where('comanda', $i)->first();
            if($i < 10){
                $com = "00$i";
            }elseif($i >= 10 && $i < 99){
                $com = "0$i";
            }else{
                $com = "$i";
            }

            $comandas[] = [
                'comanda' => $com,
                'mesa' => ($pedido != null && $pedido->_mesa) ? $pedido->_mesa->nome : '',
                'status' => $pedido != null ? 1 : 0,
                'total' => $pedido != null ? $pedido->total + $pedido->acrescimo - $pedido->desconto : 0,
            ];
        }
        return $comandas;
    }

}
