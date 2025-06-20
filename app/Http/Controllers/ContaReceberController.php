<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Cliente;
use App\Models\ContaReceber;
use App\Models\Frete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\ContaEmpresaUtil;
use App\Models\ItemContaEmpresa;
use App\Utils\UploadUtil;

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
        $status = $request->status;
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
        })
        ->paginate(env("PAGINACAO"));

        $cliente = null;
        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }
        return view('conta-receber.index', compact('data', 'cliente'));
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

        return view('conta-receber.create', compact('item', 'diferenca'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {
            $file_name = '';
            if ($request->hasFile('file')) {
                $file_name = $this->uploadUtil->uploadFile($request->file, '/financeiro');
            }

            $request->merge([
                'valor_integral' => __convert_value_bd($request->valor_integral),
                'valor_recebido' => $request->valor_recebido ? __convert_value_bd($request->valor_recebido) : 0,
                'arquivo' => $file_name
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
                    $data = [
                        'venda_id' => null,
                        'data_vencimento' => $data,
                        'data_pagamento' => $data,
                        'valor_integral' => $valor,
                        'valor_recebido' => $request->status ? $valor : 0,
                        'referencia' => $request->referencia,
                        'categoria_id' => $request->categoria_id,
                        'status' => $request->status,
                        'empresa_id' => $request->empresa_id,
                        'cliente_id' => $request->cliente_id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'local_id' => $request->local_id
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

        return view('conta-receber.edit', compact('item'));
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
        return redirect()->route('conta-receber.index');
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
        for($i=0; $i<sizeof($request->item_recebe_paga); $i++){
            $item = ContaReceber::findOrFail($request->item_recebe_paga[$i]);
            $item->status = 1;
            $item->valor_recebido = $item->valor_integral;
            $item->data_recebimento = date('Y-m-d');

            $item->save();
            $recebidos++;
        }

        session()->flash("flash_success", "Total de itens recebidos: $recebidos");
        return redirect()->back();
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
            $item->save();

            $valorMenor = $item->valor_recebido < $item->valor_integral;

            if(isset($request->conta_empresa_id)){

                $data = [
                    'conta_id' => $request->conta_empresa_id,
                    'descricao' => "Recebimento da conta " . $item->id,
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'valor' => $item->valor_recebido,
                    'tipo' => 'entrada'
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);
            }

            if($valorMenor){
                $diferenca = $item->valor_integral - $item->valor_recebido;

                $item->valor_integral = $item->valor_recebido;
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
}
