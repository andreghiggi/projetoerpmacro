<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\CategoriaProduto;
use App\Models\Marca;
use App\Models\Empresa;
use App\Models\Nfce;
use App\Models\ConfigGeral;
use App\Models\Produto;
use App\Models\VendaSuspensa;
use App\Models\TefMultiPlusCard;
use App\Models\User;
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

        $item = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
        ->select('usuario_emissaos.*')
        ->where('usuario_emissaos.usuario_id', get_id_user())
        ->first();

        if($item != null){
            return;
        }
        
        $empresa = Empresa::findOrFail($empresa_id);
        if($empresa->ambiente == 1){
            $numero = $empresa->numero_ultima_nfce_producao;
        }else{
            $numero = $empresa->numero_ultima_nfce_homologacao;
        }
        
        if($numero){
            Nfce::where('estado', 'novo')
            ->where('empresa_id', $empresa_id)
            ->update(['numero' => $numero+1]);
        }
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

        $data = Nfce::where('empresa_id', request()->empresa_id)
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
        ->orderBy('created_at', 'desc')
        ->paginate(env("PAGINACAO"));
        $contigencia = $this->getContigencia(request()->empresa_id);

        $usuarios = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        return view('front_box.index', compact('data', 'contigencia', 'usuarios', 'adm'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $caixa = __isCaixaAberto();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)->get();

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

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();

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
        $isVendaSuspensa = 0;
        $title = 'Nova Venda - PDV';

        if(isset($request->venda_suspensa)){
            $item = VendaSuspensa::findOrfail($request->venda_suspensa);
            $isVendaSuspensa = 1;
            $title = 'Venda Suspensa';
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
        $local_id = $caixa->local_id;

        return view($view, compact('categorias', 'abertura', 
            'funcionarios', 'caixa', 'config', 'tiposPagamento', 'item', 'isVendaSuspensa', 'title', 
            'configTef', 'marcas', 'produtos', 'local_id'));
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
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)->get();

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

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
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

        $local_id = $caixa->local_id;

        return view($view, compact('categorias', 'abertura', 'funcionarios', 'item', 'cliente', 'funcionario', 
            'caixa', 'isVendaSuspensa', 'tiposPagamento', 'config', 'produtos', 'categorias', 'marcas', 'local_id'));
    }


    /**
     * Remove the specified resource from storage.
     */
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
            }

            $comissao = ComissaoVenda::where('empresa_id', $item->empresa_id)
            ->where('nfce_id', $item->id)->first();
            if($comissao){
                $comissao->delete();
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
        return redirect()->route('frontbox.index');
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

    public function imprimirNaoFiscal($id)
    {
        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresa($item);

        $config = Empresa::where('id', $item->empresa_id)
        ->first();

        $config = __objetoParaEmissao($config, $item->local_id);
        
        $usuario = UsuarioEmpresa::find(get_id_user());
        $cupom = new CupomNaoFiscal($item, $config, 0);

        $logo = null;
        if($config->logo && file_exists(public_path('/uploads/logos/') . $config->logo)){
            $logo = public_path('/uploads/logos/') . $config->logo;
        }
        $pdf = $cupom->render($logo);
        header("Content-Disposition: ; filename=CUPOM.pdf");
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
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
}
