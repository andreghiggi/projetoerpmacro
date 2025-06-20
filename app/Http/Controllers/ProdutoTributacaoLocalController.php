<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdutoTributacaoLocal;
use App\Models\Produto;
use App\Models\Localizacao;
use App\Models\Empresa;

class ProdutoTributacaoLocalController extends Controller
{
    public function index(Request $request, $id){
        $item = Produto::findOrFail($id);
        $locais = Localizacao::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->get();

        $empresa = Empresa::findOrFail(request()->empresa_id);

        $listaCTSCSOSN = Produto::listaCSTCSOSN();

        return view('produtos.tributacao_por_local', compact('item', 'locais', 'listaCTSCSOSN'));
    }

    public function update(Request $request, $id){
        $item = Produto::findOrFail($id);
        try{
            for($i=0; $i<sizeof($request->local_id); $i++){
                $produtoTributacaoLocal = ProdutoTributacaoLocal::where('produto_id', $item->id)
                ->where('local_id', $request->local_id[$i])->first();
                $data = [
                    'produto_id' => $item->id,
                    'local_id' => $request->local_id[$i],
                    'ncm' => $request->ncm[$i] ? $request->ncm[$i] : null,
                    'perc_icms' => $request->perc_icms[$i] ? $request->perc_icms[$i] : null,
                    'perc_pis' => $request->perc_pis[$i] ? $request->perc_pis[$i] : null,
                    'perc_cofins' => $request->perc_cofins[$i] ? $request->perc_cofins[$i] : null,
                    'perc_ipi' => $request->perc_ipi[$i] ? $request->perc_ipi[$i] : null,
                    'cest' => $request->cest[$i] ? $request->cest[$i] : null,
                    'origem' => $request->origem[$i] ? $request->origem[$i] : null,
                    'cst_csosn' => $request->cst_csosn[$i] ? $request->cst_csosn[$i] : null,
                    'cst_pis' => $request->cst_pis[$i] ? $request->cst_pis[$i] : null,
                    'cst_cofins' => $request->cst_cofins[$i] ? $request->cst_cofins[$i] : null,
                    'cst_ipi' => $request->cst_ipi[$i] ? $request->cst_ipi[$i] : null,
                    'valor_unitario' => $request->valor_unitario[$i] ? __convert_value_bd($request->valor_unitario[$i]) : null,
                    'cfop_estadual' => $request->cfop_estadual[$i] ? $request->cfop_estadual[$i] : null,
                    'cfop_outro_estado' => $request->cfop_estadual[$i] ? $request->cfop_outro_estado[$i] : null
                ];

                if($i == 0){
                    $item->update($data);
                }
                if($produtoTributacaoLocal == null){
                    ProdutoTributacaoLocal::create($data);
                }else{
                    $produtoTributacaoLocal->update($data);
                }
            }
            session()->flash("flash_success", 'Dados atualizados!');

        }catch(\Exception $e){
            session()->flash("flash_error", $e->getMessage());
        }
        return redirect()->route('produtos.index');

    }
}
