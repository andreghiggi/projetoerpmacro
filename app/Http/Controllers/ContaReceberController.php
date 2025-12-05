<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Cliente;
use App\Models\ContaReceber;
use App\Models\CategoriaConta;
use App\Models\Frete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\ContaEmpresaUtil;
use App\Models\ItemContaEmpresa;
use App\Utils\UploadUtil;
use Dompdf\Dompdf;
use App\Exports\ContaReceberExport;
use Maatwebsite\Excel\Facades\Excel;

class ContaReceberController extends Controller
{
    protected $util;
    protected $uploadUtil;

    public function __construct(ContaEmpresaUtil $util, UploadUtil $uploadUtil){
        $this->util = $util;
        $this->uploadUtil = $uploadUtil;

        $this->middleware('permission:conta_receber_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:conta_receber_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:conta_receber_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:conta_receber_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $cliente_id = $request->cliente_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $filtro_data = $request->filtro_data;
        $status = $request->status;
        $categoria_conta_id = $request->categoria_conta_id;
        $ordem = $request->ordem;
        $reserva_id = $request->reserva_id;

        if($filtro_data == 'data_recebimento'){
            $status = 1;
        }

        $local_id = $request->get('local_id');


        $data = ContaReceber::where('empresa_id', request()->empresa_id)
        ->select('conta_recebers.*')
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('conta_recebers.cliente_id', $cliente_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date, $filtro_data) {
            return $query->whereDate('conta_recebers.'.$filtro_data, '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date, $filtro_data) {
            return $query->whereDate('conta_recebers.'.$filtro_data, '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('conta_recebers.local_id', $local_id);
        })
        ->when($categoria_conta_id, function ($query) use ($categoria_conta_id) {
            return $query->where('conta_recebers.categoria_conta_id', $categoria_conta_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('conta_recebers.local_id', $locais);
        })
        ->when($status !== null, function ($query) use ($status) {
            if($status != -1){
                return $query->where('conta_recebers.status', $status);
            }else{
                return $query->where('conta_recebers.status', 0)
                ->whereDate('conta_recebers.data_vencimento', '<', date('Y-m-d'));
            }
        })
        ->when($ordem != '', function ($query) use ($ordem) {
            return $query->orderBy('conta_recebers.data_vencimento', 'asc');
        })
        ->when($ordem == '', function ($query) use ($ordem) {
            return $query->orderBy('conta_recebers.created_at', 'asc');
        })
        ->when($reserva_id, function ($query) use ($reserva_id) {
            return $query->join('fatura_reservas', 'fatura_reservas.conta_receber_id', '=', 'conta_recebers.id')
            ->where('fatura_reservas.reserva_id', $reserva_id);
        })
        ->paginate(__itensPagina());

        $cliente = null;
        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }

        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'receber')->get();

        return view('conta-receber.index', compact('data', 'cliente', 'categorias'));
    }

    public function exportExcel(Request $request){
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $cliente_id = $request->cliente_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $categoria_conta_id = $request->categoria_conta_id;
        $ordem = $request->ordem;
        $local_id = $request->get('local_id');

        $data = ContaReceber::where('empresa_id', request()->empresa_id)
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data_vencimento', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when($categoria_conta_id, function ($query) use ($categoria_conta_id) {
            return $query->where('categoria_conta_id', $categoria_conta_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($status != '', function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when($ordem != '', function ($query) use ($ordem) {
            return $query->orderBy('data_vencimento', 'asc');
        })
        ->when($ordem == '', function ($query) use ($ordem) {
            return $query->orderBy('created_at', 'asc');
        })->get();

        $file = new ContaReceberExport($data);
        return Excel::download($file, 'contas_receber.xlsx');
    }

    public function create(Request $request)
    {

        $item = null;
        $diferenca = null;
        if($request->id){
            $item = ContaReceber::findOrFail($request->id);
            $item->valor_integral = $request->diferenca;
        }

        if($request->diferenca){
            $diferenca = $request->diferenca;
        }

        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'receber')->get();

        return view('conta-receber.create', compact('item', 'diferenca', 'categorias'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {
            $file_name = '';
            if ($request->hasFile('file')) {
                $file_name = $this->uploadUtil->uploadFile($request->file, '/financeiro');
            }

            $referencia = "";
            if ($request->dt_recorrencia) {
                $referencia = "Parcela 1 de " . sizeof($request->dt_recorrencia)+1;
            }
            $descricao = $request->descricao;
            $request->merge([
                'valor_integral' => __convert_value_bd($request->valor_integral),
                'valor_original' => __convert_value_bd($request->valor_integral),
                'valor_recebido' => $request->valor_recebido ? __convert_value_bd($request->valor_recebido) : 0,
                'arquivo' => $file_name,
                'descricao' => $descricao . " " . $referencia
            ]);
            $conta = ContaReceber::create($request->all());
            $descricaoLog = "Vencimento: " . __data_pt($request->data_vencimento, 0) . " R$ " . __moeda($request->valor_integral);
            __createLog($request->empresa_id, 'Conta a Receber', 'cadastrar', $descricaoLog);
            if(isset($request->frete_id)){
                $frete = Frete::findOrFail($request->frete_id);
                $frete->conta_receber_id = $conta->id;
                $frete->save();
            }

            if ($request->dt_recorrencia) {
                for ($i = 0; $i < sizeof($request->dt_recorrencia); $i++) {
                    $data = $request->dt_recorrencia[$i];
                    $valor = __convert_value_bd($request->valor_recorrencia[$i]);
                    $referencia = "Parcela ".($i+2)." de " . sizeof($request->dt_recorrencia)+1;
                    $data = [
                        'venda_id' => null,
                        'data_vencimento' => $data,
                        // 'data_recebimento' => $data,
                        'valor_integral' => $valor,
                        'valor_recebido' => $request->status ? $valor : 0,
                        'descricao' => $descricao . " " . $referencia,
                        'categoria_conta_id' => $request->categoria_conta_id,
                        'status' => $request->status,
                        'empresa_id' => $request->empresa_id,
                        'cliente_id' => $request->cliente_id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'local_id' => $request->local_id,
                        'observacao' => $request->observacao,
                        'observacao2' => $request->observacao2,
                        'observacao3' => $request->observacao3,
                    ];
                    $conta = ContaReceber::create($data);
                    $descricaoLog = "Vencimento: " . __data_pt($request->dt_recorrencia[$i], 0) . " R$ " . __moeda($valor);
                    __createLog($request->empresa_id, 'Conta a Receber', 'cadastrar', $descricaoLog);

                }
            }
            session()->flash("flash_success", "Conta a receber cadastrada!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta a Receber', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }

        if(isset($request->redirect)){
            return redirect($request->redirect);
        }
        return redirect()->route('conta-receber.index');
    }

    public function edit($id)
    {
        $item = ContaReceber::findOrFail($id);
        __validaObjetoEmpresa($item);
        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'receber')->get();
        return view('conta-receber.edit', compact('item', 'categorias'));
    }

    public function show($id)
    {
        $item = ContaReceber::findOrFail($id);
        __validaObjetoEmpresa($item);
        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'receber')->get();
        return view('conta-receber.show', compact('item', 'categorias'));
    }

    public function estornar($id)
    {
        $item = ContaReceber::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('conta-receber.estornar', compact('item'));
    }

    public function estornarUpdate(Request $request, $id){
        $item = ContaReceber::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->status = 0;
            $item->motivo_estorno = $request->motivo_estorno;
            $item->save();

            session()->flash("flash_success", "Conta a receber estornada!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('conta-receber.index');
    }

    public function update(Request $request, $id)
    {
        $item = ContaReceber::findOrFail($id);
        __validaObjetoEmpresa($item);

        try {
            $file_name = $item->arquivo;
            if ($request->hasFile('file')) {
                $this->uploadUtil->unlinkImage($item, '/financeiro');
                $file_name = $this->uploadUtil->uploadFile($request->file, '/financeiro');
            }
            $request->merge([
                'valor_integral' => __convert_value_bd($request->valor_integral),
                'valor_recebido' => __convert_value_bd($request->valor_recebido) ? __convert_value_bd($request->valor_recebido) : 0,
                'arquivo' => $file_name
            ]);
            $item->fill($request->all())->save();
            $descricaoLog = "Vencimento: " . __data_pt($item->data_vencimento) . " R$ " . __moeda($item->valor_integral);
            __createLog($request->empresa_id, 'Conta a Receber', 'editar', $descricaoLog);
            session()->flash("flash_success", "Conta a receber atualizada!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta a Receber', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('conta-receber.index');
    }

    public function downloadFile($id){
        $item = ContaReceber::findOrFail($id);
        if (file_exists(public_path('uploads/financeiro/') . $item->arquivo)) {
            return response()->download(public_path('uploads/financeiro/') . $item->arquivo);
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    private function __validate(Request $request)
    {
        $rules = [
            'cliente_id' => 'required',
            'valor_integral' => 'required',
            'data_vencimento' => 'required',
            'status' => 'required',
            'tipo_pagamento' => 'required'
        ];
        $messages = [
            'cliente_id.required' => 'Campo obrigatório',
            'valor_integral.required' => 'Campo obrigatório',
            'data_vencimento.required' => 'Campo obrigatório',
            'status.required' => 'Campo obrigatório',
            'tipo_pagamento.required' => 'Campo obrigatório'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function destroy($id)
    {
        $item = ContaReceber::findOrFail($id);
        __validaObjetoEmpresa($item);
        
        try {
            $descricaoLog = "Vencimento: " . __data_pt($item->data_vencimento, 0) . " R$ " . __moeda($item->valor_integral);
            $item->delete();
            __createLog(request()->empresa_id, 'Conta a Receber', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Conta removida!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Conta a Receber', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        $recebidas = 0;
        // dd($request->all());
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = ContaReceber::findOrFail($request->item_delete[$i]);
            if($item->boleto){
                session()->flash("flash_error", 'Conta a receber selecionada com boleto vinculado!');
                return redirect()->back();
            }

            if(!$item->status){
                try {
                    $descricaoLog = "Vencimento: " . __data_pt($item->data_vencimento, 0) . " R$ " . __moeda($item->valor_integral);
                    $item->delete();
                    $removidos++;
                    __createLog(request()->empresa_id, 'Conta a Receber', 'excluir', $descricaoLog);
                } catch (\Exception $e) {
                    __createLog(request()->empresa_id, 'Conta a Receber', 'erro', $e->getMessage());
                    session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                    return redirect()->back();
                }
            }else{
                $recebidas++;
            }
        }

        session()->flash("flash_success", "Total de contas removidas: $removidos");
        if($recebidas > 0){
            session()->flash("flash_warning", "Total de contas não removidas: $recebidas");
        }
        return redirect()->back();
    }

    public function receberSelecionados(Request $request)
    {
        $recebidos = 0;
        $data = [];
        for($i=0; $i<sizeof($request->item_recebe_paga); $i++){
            $item = ContaReceber::findOrFail($request->item_recebe_paga[$i]);
            $data[] = $item;
        }

        return view('conta-receber.receive_select', compact('data'));
    }

    public function pay($id)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $item = ContaReceber::findOrFail($id);
        if($item->status){
            session()->flash("flash_warning", "Esta conta já esta recebida!");
            return redirect()->route('conta-receber.index');
        }
        return view('conta-receber.pay', compact('item'));
    }

    public function payPut(Request $request, $id)
    {
        $usuario = Auth::user()->id;
        $caixa = Caixa::where('usuario_id', $usuario)->where('status', 1)->first();
        $item = ContaReceber::findOrFail($id);

        try {
            $item->valor_recebido = __convert_value_bd($request->valor_pago);
            $item->status = true;
            $item->data_recebimento = $request->data_recebimento;
            $item->tipo_pagamento = $request->tipo_pagamento;
            $item->caixa_id = $caixa->id;

            $valorMenor = $item->valor_recebido < $item->valor_integral;

            if(isset($request->conta_empresa_id)){
                $item->conta_empresa_id = $request->conta_empresa_id;

                $nDoc = '';
                $descricao = "Recebimento de conta";
                if($item->nfe){

                    if($item->descricao){
                        $descricao .= " " . $item->descricao . " ";
                    }
                    $descricao .= " - valor integral R$" . __moeda($item->valor_integral) . " referente a venda Nº " . $item->nfe->numero_sequencial;
                    if($item->nfe->estado == 'aprovado'){
                        $descricao .= ", emitida em " . __data_pt($item->nfe->data_emissao);
                        $nDoc = $item->nfe->numero;
                    }

                    $descricao .= " VALOR TOTAL VENDA R$ " . __moeda($item->nfe->total);
                }

                $data = [
                    'conta_id' => $request->conta_empresa_id,
                    'descricao' => $descricao,
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'valor' => $item->valor_recebido,
                    'tipo' => 'entrada',
                    'cliente_id' => $item->cliente_id,
                    'numero_documento' => $nDoc,
                    'conta_pagar_id' => $item->id,
                    'categoria_id' => $item->categoria_conta_id
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);
            }

            $item->save();

            if($valorMenor){
                $diferenca = $item->valor_integral - $item->valor_recebido;

                // $item->valor_integral = $item->valor_recebido;
                $item->recebimento_parcial = 1;
                $item->save();
                
                session()->flash("flash_warning", "Conta recebida com valor parcial!");

                return redirect()->route('conta-receber.create', ['diferenca=' . $diferenca . '&id=' . $item->id]);
            }
            session()->flash("flash_success", "Conta recebida!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('conta-receber.index');
    }

    public function receiveSelect(Request $request){
        for ($i = 0; $i < sizeof($request->conta_id); $i++) {
            $item = ContaReceber::findOrFail($request->conta_id[$i]);

            $item->valor_recebido = __convert_value_bd($request->valor_recebido[$i]);
            $item->status = 1;
            $item->data_recebimento = $request->data_recebimento[$i];
            $item->tipo_pagamento = $request->tipo_pagamento[$i];

            if(isset($request->conta_empresa_id[$i])){
                $item->conta_empresa_id = $request->conta_empresa_id[$i];

                $nDoc = '';
                $descricao = "Recebimento da conta";
                if($item->nfe){

                    if($item->descricao){
                        $descricao .= " " . $item->descricao . " ";
                    }
                    $descricao .= " - valor integral R$" . __moeda($item->valor_integral) . " referente a venda Nº " . $item->nfe->numero_sequencial;
                    if($item->nfe->estado == 'aprovado'){
                        $descricao .= ", emitida em " . __data_pt($item->nfe->data_emissao);
                        $nDoc = $item->nfe->numero;
                    }

                    $descricao .= " VALOR TOTAL VENDA R$ " . __moeda($item->nfe->total);
                }
                $data = [
                    'conta_id' => $request->conta_empresa_id[$i],
                    'descricao' => $descricao,
                    'tipo_pagamento' => $request->tipo_pagamento[$i],
                    'valor' => __convert_value_bd($request->valor_recebido[$i]),
                    'tipo' => 'entrada',
                    'cliente_id' => $item->cliente_id,
                    'numero_documento' => $nDoc,
                    'conta_receber_id' => $item->id,
                    'categoria_id' => $item->categoria_conta_id
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);
            }
            $item->save();

            session()->flash("flash_success", "Contas recebidas!");
        }
        return redirect()->route('conta-receber.index');
    }

    public function imprimirComprovante($id){
        $item = ContaReceber::findOrFail($id);
        __validaObjetoEmpresa($item);

        $p = view('conta-receber.imprimir_comprovante', compact('item'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper([0,0,204, 204]);
        $domPdf->render();
        header("Content-Disposition: ; filename=Pedido.pdf");
        $domPdf->stream("Comprovante.pdf", array("Attachment" => false));

    }
}
