<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Garantia;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;

class GarantiaController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:garantias_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:garantias_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:garantias_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:garantias_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $this->atualizarStatus();
        $produto_id = $request->produto_id;
        $cliente_id = $request->cliente_id;
        $status = $request->status;
        $data_venda = $request->data_venda;

        $data = Garantia::where('empresa_id', request()->empresa_id)
        ->when(!empty($produto_id), function ($q) use ($produto_id) {
            return $q->where('produto_id', $produto_id);
        })
        ->when(!empty($cliente_id), function ($q) use ($cliente_id) {
            return $q->where('cliente_id', $cliente_id);
        })
        ->when(!empty($status), function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->when(!empty($data_venda), function ($q) use ($data_venda) {
            return $q->whereDate('data_venda', $data_venda);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        $produto = null;
        $cliente = null;
        if($produto_id){
            $produto = Produto::findOrFail($produto_id);
        }

        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }

        return view('garantias.index', compact('data', 'produto', 'cliente'));
    }

    private function atualizarStatus()
    {
        DB::transaction(function () {
            Garantia::whereIn('status', ['em análise', 'registrada'])
            ->get()
            ->each(function ($garantia) {
                if (!$garantia->isValida()) {
                    $garantia->update(['status' => 'expirada']);
                }
            });
        });
    }

    public function create()
    {
        return view('garantias.create');
    }

    public function edit($id)
    {
        $item = Garantia::findOrFail($id);
        return view('garantias.edit', compact('item'));
    }

    public function imprimir($id)
    {
        $garantia = Garantia::findOrFail($id);
        $config = Empresa::where('id', $garantia->empresa_id)->first();

        $p = view('garantias.imprimir', compact('config', 'garantia'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        header("Content-Disposition: ; filename=Pedido.pdf");
        $domPdf->stream("Garantia #$garantia->id.pdf", array("Attachment" => false));

    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'valor_reparo' => __convert_value_bd($request->valor_reparo),
                'observacao' => $request->observacao ?? '',
                'descricao_problema' => $request->descricao_problema ?? '',
                'usuario_id' => \Auth::user()->id,
            ]);
            Garantia::create($request->all());
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('garantias.index');
    }

    public function update(Request $request, $id)
    {
        $item = Garantia::findOrFail($id);
        try {
            $request->merge([
                'valor_reparo' => __convert_value_bd($request->valor_reparo),
                'observacao' => $request->observacao ?? '',
                'descricao_problema' => $request->descricao_problema ?? '',
                'usuario_id' => \Auth::user()->id,
            ]);
            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Atualizado com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('garantias.index');
    }

    public function destroy($id)
    {
        $item = Garantia::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash('flash_success', 'Removido com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_warning', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function modal($id){
        $item = Garantia::findOrFail($id);
        return view('garantias.partials.modal_body', compact('item'))->render();
    }
    
}
