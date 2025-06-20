<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdemProducao;
use App\Models\ItemProducao;
use App\Models\ItemOrdemProducao;
use App\Models\Empresa;
use Dompdf\Dompdf;

class OrdemProducaoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:ordem_producao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:ordem_producao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:ordem_producao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:ordem_producao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        $data = OrdemProducao::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')
        ->paginate(env("PAGINACAO"));

        return view('ordem_producao.index', compact('data'));
    }

    public function create(Request $request){
        $data = ItemProducao::where('produtos.empresa_id', $request->empresa_id)
        ->select('item_producaos.*')
        ->join('produtos', 'produtos.id', '=', 'item_producaos.produto_id')
        ->where('item_producaos.status', 0)
        ->get();

        if(sizeof($data) == 0){
            session()->flash("flash_error", "Nenhum item para gerar a ordem de produção!");
            return redirect()->back();
        }

        // foreach($data as $i){
        //     foreach($i->itemNfe->itensDimensao as $itd){
        //         $i->quantidade = $itd->quantidade;
        //     }
        // }
        return view('ordem_producao.create', compact('data'));
    }

    public function edit(Request $request, $id){

        $item = OrdemProducao::findOrFail($id);
        $data = ItemProducao::where('produtos.empresa_id', $request->empresa_id)
        ->select('item_producaos.*')
        ->join('produtos', 'produtos.id', '=', 'item_producaos.produto_id')
        ->where('item_producaos.status', 0)
        ->get();

        return view('ordem_producao.edit', compact('data', 'item'));
    }

    public function show(Request $request, $id){
        $item = OrdemProducao::findOrFail($id);
        return view('ordem_producao.show', compact('item'));
    }

    public function store(Request $request){
        try{
            if(!$request->item_select){
                session()->flash("flash_error", "Selecione ao menos 1 produto!");
                return redirect()->back();
            }

            $lastItem = OrdemProducao::where('empresa_id', $request->empresa_id)
            ->orderBy('codigo_sequencial', 'desc')->first();
            $codigo_sequencial = 1;
            if($lastItem != null){
                $codigo_sequencial = $lastItem->codigo_sequencial+1;
            }
            $request->merge([
                'observacao' => $request->observacao ?? '',
                'codigo_sequencial' => $codigo_sequencial,
                'usuario_id' => get_id_user()
            ]);
            $ordem = OrdemProducao::create($request->all());

            for($i=0; $i<sizeof($request->item_select); $i++){
                $itemProducao = ItemProducao::findOrFail($request->item_select[$i]);
                $itemProducao->status = 1;
                $itemProducao->save();
                echo $request->qtd[$i]. "<br>";
                ItemOrdemProducao::create([
                    'ordem_producao_id' => $ordem->id,
                    'item_producao_id' => $itemProducao->id,
                    'produto_id' => $itemProducao->produto_id,
                    'quantidade' => $request->qtd[$i],
                    'status' => 0,
                    'observacao' => $request->observacao_item[$i]
                ]);
            }

            session()->flash("flash_success", "Ordem de Produção criada com sucesso");
            return redirect()->route('ordem-producao.index');

        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu Errado" . $e->getMessage());
            return redirect()->back();

        }
    }

    public function update(Request $request, $id){
        try{
            $item = OrdemProducao::findOrFail($id);

            $request->merge([
                'observacao' => $request->observacao ?? '',
            ]);
            $item->fill($request->all())->save();

            foreach($item->itens as $i){
                $itemProducao = $i->itemProducao;
                $itemProducao->status = 0;
                $itemProducao->save();
            }

            $item->itens()->delete();

            for($i=0; $i<sizeof($request->item_select); $i++){
                $itemProducao = ItemProducao::findOrFail($request->item_select[$i]);
                $itemProducao->status = 1;
                $itemProducao->save();
                ItemOrdemProducao::create([
                    'ordem_producao_id' => $item->id,
                    'item_producao_id' => $itemProducao->id,
                    'produto_id' => $itemProducao->produto_id,
                    'quantidade' => $request->qtd[$i],
                    'status' => 0,
                    'observacao' => $request->observacao_item[$i]
                ]);
            }

            session()->flash("flash_success", "Ordem de Produção atualizada com sucesso");
            return redirect()->route('ordem-producao.index');
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu Errado" . $e->getMessage());
            return redirect()->back();

        }
    }

    public function updateEstado(Request $request, $id){
        $item = OrdemProducao::findOrFail($id);
        $item->estado = $request->estado;
        $item->save();
        session()->flash("flash_success", "Estado alterado!");
        return redirect()->back();
    }

    public function alterarStatusItem($id){
        $item = ItemOrdemProducao::findOrFail($id);
        $item->status = !$item->status;
        $item->save();
        session()->flash("flash_success", "Status do item alterado!");
        return redirect()->back();
    }

    public function destroy($id)
    {
        $item = OrdemProducao::findOrFail($id);
        try {
            foreach($item->itens as $i){
                $itemProducao = $i->itemProducao;
                $itemProducao->status = 0;
                $itemProducao->save();
            }
            $item->itens()->delete();

            $item->delete();
            session()->flash("flash_success", "Ordem de produção removida");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->back();
    } 

    public function imprimirEtiquetas($id){
        $item = OrdemProducao::findOrFail($id);

        $empresa = Empresa::findOrFail(request()->empresa_id);
        // $p = view('ordem_producao.etiqueta', compact('item', 'empresa'));
        // $domPdf = new Dompdf(["enable_remote" => true]);
        // $domPdf->loadHtml($p);

        // $pdf = ob_get_clean();
        // $height = 0;
        // foreach($item->itens as $i){
        //     $height += $i->quantidade * 300;
        // }

        return view('ordem_producao.etiqueta', compact('item', 'empresa'));

        // $domPdf->set_paper(array(0,0,204,$height));
        // $domPdf->render();
        // $domPdf->stream("Etiqueta produção.pdf", array("Attachment" => false));
        // exit();
    }

    public function imprimir($id){
        $item = OrdemProducao::findOrFail($id);

        $config = Empresa::findOrFail(request()->empresa_id);
        $p = view('ordem_producao.imprimir', compact('item', 'config'));
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();
        
        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Ordem de produção.pdf", array("Attachment" => false));
        exit();
    }

}
