<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\CategoriaProduto;
use App\Models\Empresa;
use App\Models\FaturaPreVenda;
use App\Models\Funcionario;
use App\Models\ItemPreVenda;
use App\Models\NaturezaOperacao;
use App\Models\PreVenda;
use App\Models\Produto;
use App\Models\Nfce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use StringBackedEnum;
use Svg\Tag\Rect;
use Illuminate\Support\Str;
use NFePHP\DA\NFe\CupomNaoFiscal;
use Dompdf\Dompdf;
use App\Models\ConfigGeral;

class PreVendaController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:pre_venda_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:pre_venda_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pre_venda_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:pre_venda_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $status = $request->get('status');
        $local_id = $request->get('local_id');

        $data = PreVenda::where('empresa_id', request()->empresa_id)
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            if ($status == -1) {
                return $query->where('status', '!=', 1);
            } else {
                return $query->where('status', $status);
            }
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());
        return view('pre_venda.index', compact('data'));
    }


    public function create()
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $abertura = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        }
        $caixa = __isCaixaAberto();

        $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
        $config = Empresa::findOrFail(request()->empresa_id);

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($config->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }
        
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

        $config = ConfigGeral::where('empresa_id', $config->id)->first();

        return view('pre_venda.create', compact('abertura', 'categorias', 'funcionarios', 'naturezas', 'caixa', 
            'tiposPagamento', 'config'));
    }

    public function edit($id)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $item = PreVenda::findOrFail($id);
        __validaObjetoEmpresa($item);

        $abertura = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        }
        $caixa = __isCaixaAberto();

        $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
        $config = Empresa::findOrFail(request()->empresa_id);

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($config->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }
        
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

        $config = ConfigGeral::where('empresa_id', $config->id)->first();

        return view('pre_venda.edit', compact('abertura', 'categorias', 'funcionarios', 'naturezas', 'caixa', 
            'tiposPagamento', 'config', 'item'));
    }

    public function store(Request $request)
    {
        if(!$request->produto_id){
            session()->flash("flash_error", "Inclua ao menos 1 item na pré venda");
            return redirect()->back();
        }
        try {
            // $valor_total = $this->somaItens($request);

            $natureza = NaturezaOperacao::where('empresa_id', request()->empresa_id)->first();
            $caixa = __isCaixaAberto();
            $request->merge([
                'cliente_id' => $request->cliente_id,
                'bandeira_cartao' => $request->bandeira_cartao ?? '',
                'cnpj_cartao' => $request->cnpj_cartao ?? '',
                'cAut_cartao' => $request->cAut_cartao ?? '',
                'descricao_pag_outros' => $request->descricao_pag_outros ?? '',
                'rascunho' => $request->rascunho ?? 0,
                'usuario_id' => get_id_user(),
                'observacao' => $request->observacao ?? '',
                'qtd_volumes' => $request->qtd_volumes ?? 0,
                'peso_liquido' => $request->peso_liquido ?? 0,
                'funcionario_id' => $request->funcionario_id ?? null,
                'peso_bruto' => $request->peso_bruto ?? 0,
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'valor_total' => __convert_value_bd($request->valor_total),
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'natureza_id' => $natureza->id,
                'forma_pagamento' => '',
                'tipo_pagamento' => $request->tipo_pagamento_row ? '99' : $request->tipo_pagamento,
                'nome' => $request->nome,
                'cpf' => $request->cpf ?? '',
                'local_id' => $caixa->local_id,
                'codigo' => Str::random(8)
            ]);
            
            $preVenda = PreVenda::create($request->all());
            
            for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                $product = Produto::findOrFail($request->produto_id[$i]);
                $cfop = 0;
                $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;
                ItemPreVenda::create([
                    'pre_venda_id' => $preVenda->id,
                    'produto_id' => (int)$request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade[$i]),
                    'valor' => __convert_value_bd($request->valor_unitario[$i]),
                    'cfop' => $cfop,
                    'observacao' => $request->observacao ?? '',
                    'variacao_id' => $variacao_id,
                ]);
            }

            if ($request->tipo_pagamento_row) {
                for ($i = 0; $i < sizeof($request->tipo_pagamento_row); $i++) {
                    FaturaPreVenda::create([
                        'valor_parcela' => __convert_value_bd($request->valor_integral_row[$i]),
                        'tipo_pagamento' => $request->tipo_pagamento_row[$i],
                        'pre_venda_id' => $preVenda->id,
                        'vencimento' => $request->data_vencimento_row[$i]
                    ]);
                }
            } else {
                FaturaPreVenda::create([
                    'valor_parcela' => __convert_value_bd($request->valor_total),
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'pre_venda_id' => $preVenda->id,
                    'vencimento' => $request->data_vencimento
                ]);
            }
            session()->flash("flash_success", "Pré venda realizada com sucesso!");
        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            session()->flash("flash_error", "Algo deu errado por aqui: " . $e->getMessage());
        }
        return redirect()->back()->with(['codigo' => $preVenda->codigo]);
    }

    public function update(Request $request, $id)
    {
        if(!$request->produto_id){
            session()->flash("flash_error", "Inclua ao menos 1 item na pré venda");
            return redirect()->back();
        }

        $item = PreVenda::findOrFail($id);
        try {
            // $valor_total = $this->somaItens($request);

            $data = [
                'cliente_id' => $request->cliente_id,
                'bandeira_cartao' => $request->bandeira_cartao ?? '',
                'cnpj_cartao' => $request->cnpj_cartao ?? '',
                'cAut_cartao' => $request->cAut_cartao ?? '',
                'descricao_pag_outros' => $request->descricao_pag_outros ?? '',
                'rascunho' => $request->rascunho ?? 0,
                'observacao' => $request->observacao ?? '',
                'qtd_volumes' => $request->qtd_volumes ?? 0,
                'peso_liquido' => $request->peso_liquido ?? 0,
                'funcionario_id' => $request->funcionario_id ?? null,
                'peso_bruto' => $request->peso_bruto ?? 0,
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'valor_total' => __convert_value_bd($request->valor_total),
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'tipo_pagamento' => $request->tipo_pagamento_row ? '99' : $request->tipo_pagamento,
                'nome' => $request->nome,
                'cpf' => $request->cpf ?? '',
            ];
            
            $item->fill($data)->save();
            
            $item->itens()->delete();
            for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                $product = Produto::findOrFail($request->produto_id[$i]);
                $cfop = 0;
                $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;
                ItemPreVenda::create([
                    'pre_venda_id' => $item->id,
                    'produto_id' => (int)$request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade[$i]),
                    'valor' => __convert_value_bd($request->valor_unitario[$i]),
                    'cfop' => $cfop,
                    'observacao' => $request->observacao ?? '',
                    'variacao_id' => $variacao_id,
                ]);
            }

            $item->fatura()->delete();
            if ($request->tipo_pagamento_row) {
                for ($i = 0; $i < sizeof($request->tipo_pagamento_row); $i++) {
                    FaturaPreVenda::create([
                        'valor_parcela' => __convert_value_bd($request->valor_integral_row[$i]),
                        'tipo_pagamento' => $request->tipo_pagamento_row[$i],
                        'pre_venda_id' => $item->id,
                        'vencimento' => $request->data_vencimento_row[$i]
                    ]);
                }
            } else {
                FaturaPreVenda::create([
                    'valor_parcela' => __convert_value_bd($request->valor_total),
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'pre_venda_id' => $item->id,
                    'vencimento' => $request->data_vencimento
                ]);
            }
            session()->flash("flash_success", "Pré venda atualizada com sucesso!");
        } catch (\Exception $e) {
            echo $e->getMessage() . '<br>' . $e->getLine();
            die;
            session()->flash("flash_error", "Algo deu errado por aqui: " . $e->getMessage());
        }
        return redirect()->route('pre-venda.index');
    }

    // public function imprimir($codigo){
    //     $item = PreVenda::where('codigo', $codigo)
    //     ->where('empresa_id', request()->empresa_id)
    //     ->first();


    //     $config = Empresa::where('id', $item->empresa_id)
    //     ->first();
    //     $cupom = new CupomNaoFiscal($item, $config, 1);
    //     $pdf = $cupom->render();
    //     return response($pdf)
    //     ->header('Content-Type', 'application/pdf');
    // }

    public function imprimir($codigo){

        $item = PreVenda::where('codigo', $codigo)
        ->where('empresa_id', request()->empresa_id)
        ->first();

        $config = Empresa::where('id', $item->empresa_id)
        ->first();

        $preVenda = 1;

        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();
        
        $p = view('front_box.cupom_nao_fiscal', compact('config', 'item', 'preVenda', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $height = 300;

        $height += sizeof($item->itens)*10;
        $domPdf->setPaper([0,0,244,$height]);
        $pdf = $domPdf->render();


        $domPdf->stream("Pré Venda $codigo.pdf", array("Attachment" => false));
    }


    private function somaItens($request)
    {
        $valor_total = 0;
        for ($i = 0; $i < sizeof($request->produto_id); $i++) {
            $valor_total += __convert_value_bd($request->subtotal_item[$i]);
        }
        return $valor_total;
    }

    public function destroy($id)
    {
        $item = PreVenda::findOrFail($id);
        try {
            $item->itens()->delete();
            // $item->nfce->delete();

            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('pre-venda.index');
    }
}
