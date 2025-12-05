<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PadraoTributacaoProduto;
use App\Models\Produto;

class PadraoTributacaoProdutoController extends Controller
{
    public function index(){
        $data = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)
        ->paginate(__itensPagina());

        return view('padrao_tributacao.index', compact('data'));
    }

    public function create(){
        return view('padrao_tributacao.create');
    }

    public function edit($id){
        $item = PadraoTributacaoProduto::findOrfail($id);
        __validaObjetoEmpresa($item);
        return view('padrao_tributacao.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {

        if($request->padrao == 1){
            PadraoTributacaoProduto::where('empresa_id', $request->empresa_id)
            ->update(['padrao' => 0]);
        }

        $item = PadraoTributacaoProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Padrão de Tributação', 'editar', $request->descricao);
            session()->flash("flash_success", "Padrão atualizado!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Padrão de Tributação', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('produtopadrao-tributacao.index');
    }

    public function destroy($id)
    {
        $item = PadraoTributacaoProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->descricao;
            $item->delete();
            __createLog(request()->empresa_id, 'Padrão de Tributação', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Padrão removido!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Padrão de Tributação', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = PadraoTributacaoProduto::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->descricao;
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Padrão de Tributação', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Padrão de Tributação', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->back();
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();

    }

    public function store(Request $request)
    {
        // dd($request);
        $this->__validate($request);
        if($request->padrao == 1){
            PadraoTributacaoProduto::where('empresa_id', $request->empresa_id)
            ->update(['padrao' => 0]);
        }
        try {

            PadraoTributacaoProduto::create($request->all());
            __createLog($request->empresa_id, 'Padrão de Tributação', 'cadastrar', $request->descricao);
            session()->flash("flash_success", "Padrão cadastrado!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Padrão de Tributação', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('produtopadrao-tributacao.index');
    }

    private function __validate(Request $request)
    {
        $rules = [
            'descricao' => 'required',
            // 'ncm' => 'required',
            'perc_icms' => 'required',
            'perc_pis' => 'required',
            'perc_cofins' => 'required',
            'perc_ipi' => 'required',
            'cst_csosn' => 'required',
            'cst_pis' => 'required',
            'cst_cofins' => 'required',
            'cst_ipi' => 'required',
        ];

        $messages = [
            'descricao.required' => 'Campo Obrigatório',
            'codigo_barras.required' => 'Campo Obrigatório',
            'ncm.required' => 'Campo Obrigatório',
            'cest.required' => 'Campo Obrigatório',
            'unidade.required' => 'Campo Obrigatório',
            'perc_icms.required' => 'Campo Obrigatório',
            'perc_pis.required' => 'Campo Obrigatório',
            'perc_cofins.required' => 'Campo Obrigatório',
            'perc_ipi.required' => 'Campo Obrigatório',
            'cst_csosn.required' => 'Campo Obrigatório',
            'cst_pis.required' => 'Campo Obrigatório',
            'cst_cofins.required' => 'Campo Obrigatório',
            'cst_ipi.required' => 'Campo Obrigatório',
            'valor_unitario.required' => 'Campo Obrigatório',
        ];
        $this->validate($request, $rules, $messages);
    }

    public function alterarProdutos(Request $request){
        $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)
        ->get();

        $produtos = Produto::where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->get();

        return view('padrao_tributacao.alterar_produtos', compact('padroes', 'produtos'));
    }

    public function setTributacao(Request $request){
        try{
            $cont = 0;
            for($i=0; $i<sizeof($request->produto_check); $i++){
                $produto = Produto::find($request->produto_check[$i]);
                if($produto != null){
                    $produto->padrao_id = $request->padrao_id;
                    $produto->fill($request->all())->save();
                    $cont++;
                }
            }
            session()->flash("flash_success", "Redefinada a tributação de $cont produtos!");

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('produtopadrao-tributacao.index');
    }
}
