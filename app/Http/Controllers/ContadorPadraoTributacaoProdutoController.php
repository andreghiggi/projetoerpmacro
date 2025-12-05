<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PadraoTributacaoProduto;
use App\Models\Produto;
use App\Models\Empresa;

class ContadorPadraoTributacaoProdutoController extends Controller
{
    public function index(Request $request){

        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        $data = PadraoTributacaoProduto::where('empresa_id', $empresaSelecionada)
        ->paginate(__itensPagina());

        return view('contador_padrao_tributacao.index', compact('data'));
    }

    public function create(){
        return view('contador_padrao_tributacao.create');
    }

    public function edit($id){
        $item = PadraoTributacaoProduto::findOrfail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        return view('contador_padrao_tributacao.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {

        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        if($request->padrao == 1){
            PadraoTributacaoProduto::where('empresa_id', $empresaSelecionada)
            ->update(['padrao' => 0]);
        }

        $item = PadraoTributacaoProduto::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        try {
            $request->merge(['empresa_id' => $empresaSelecionada]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Padrão atualizado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('contador-produto-tributacao.index');
    }

    public function destroy($id)
    {
        $item = PadraoTributacaoProduto::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        try {

            $item->delete();
            session()->flash("flash_success", "Padrão removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }


    public function store(Request $request)
    {
        $this->__validate($request);

        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        if($request->padrao == 1){
            PadraoTributacaoProduto::where('empresa_id', $empresaSelecionada)
            ->update(['padrao' => 0]);
        }
        try {
            $request->merge(['empresa_id' => $empresaSelecionada]);

            PadraoTributacaoProduto::create($request->all());
            session()->flash("flash_success", "Padrão cadastrado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('contador-produto-tributacao.index');
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

        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        $padroes = PadraoTributacaoProduto::where('empresa_id', $empresaSelecionada)
        ->get();

        $produtos = Produto::where('empresa_id', $empresaSelecionada)
        ->get();

        return view('contador_padrao_tributacao.alterar_produtos', compact('padroes', 'produtos'));
    }

    public function setTributacao(Request $request){

        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        $request->merge(['empresa_id' => $empresaSelecionada]);

        try{
            $cont = 0;
            for($i=0; $i<sizeof($request->produto_check); $i++){
                $produto = Produto::find($request->produto_check[$i]);
                if($produto != null){

                    $produto->fill($request->all())->save();
                    $cont++;
                }
            }
            session()->flash("flash_success", "Redefinada a tributação de $cont produtos!");

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('contador-produto-tributacao.index');
    }
}
