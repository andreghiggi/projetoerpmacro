<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Cliente;
use App\Models\ContaPagar;
use App\Models\Fornecedor;
use App\Models\ItemContaEmpresa;
use App\Models\ManutencaoVeiculo;
use App\Models\DespesaFrete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\ContaEmpresaUtil;
use App\Utils\UploadUtil;

class ContaPagarController extends Controller
{

    protected $util;
    protected $uploadUtil;
    public function __construct(ContaEmpresaUtil $util, UploadUtil $uploadUtil){
        $this->util = $util;
        $this->uploadUtil = $uploadUtil;
        $this->middleware('permission:conta_pagar_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:conta_pagar_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:conta_pagar_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:conta_pagar_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $fornecedor_id = $request->fornecedor_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $ordem = $request->ordem;
        $local_id = $request->get('local_id');

        $data = ContaPagar::where('empresa_id', request()->empresa_id)
        ->when(!empty($fornecedor_id), function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
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

        $fornecedor = null;
        if($fornecedor_id){
            $fornecedor = Cliente::findOrFail($fornecedor_id);
        }
        return view('conta-pagar.index', compact('data', 'fornecedor'));
    }

    public function create()
    {
        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)->get();
        return view('conta-pagar.create', compact('fornecedores'));
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
                'valor_pago' => $request->valor_pago ? __convert_value_bd($request->valor_pago) : 0,
                'arquivo' => $file_name
            ]);

            $conta = ContaPagar::create($request->all());
            $descricaoLog = "Vencimento: " . __data_pt($request->data_vencimento, 0) . " R$ " . __moeda($request->valor_integral);
            __createLog($request->empresa_id, 'Conta a Pegar', 'cadastrar', $descricaoLog);
            if(isset($request->manutencao_id)){
                $manutencao = ManutencaoVeiculo::findOrFail($request->manutencao_id);
                $manutencao->conta_pagar_id = $conta->id;
                $manutencao->save();
            }

            if(isset($request->despesa_id)){
                $despesa = DespesaFrete::findOrFail($request->despesa_id);
                $despesa->conta_pagar_id = $conta->id;
                $despesa->save();
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
                        'valor_pago' => $request->status ? $valor : 0,
                        'referencia' => $request->referencia,
                        'categoria_id' => $request->categoria_id,
                        'status' => $request->status,
                        'empresa_id' => $request->empresa_id,
                        'fornecedor_id' => $request->fornecedor_id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'local_id' => $conta->local_id
                    ];

                    ContaPagar::create($data);

                    $descricaoLog = "Vencimento: " . __data_pt($request->dt_recorrencia[$i], 0) . " R$ " . __moeda($valor);
                    __createLog($request->empresa_id, 'Conta a Pagar', 'cadastrar', $descricaoLog);
                }
            }
            session()->flash("flash_success", "Conta a Pagar cadastrada!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta a Pagar', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        if(isset($request->redirect)){
            return redirect($request->redirect);
        }
        return redirect()->route('conta-pagar.index');
    }

    public function edit($id)
    {
        $item = ContaPagar::findOrFail($id);
        __validaObjetoEmpresa($item);

        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)->get();

        return view('conta-pagar.edit', compact('item', 'fornecedores'));
    }

    public function update(Request $request, $id)
    {
        $item = ContaPagar::findOrFail($id);
        __validaObjetoEmpresa($item);
        
        try {
            $file_name = $item->arquivo;
            if ($request->hasFile('file')) {
                $this->uploadUtil->unlinkImage($item, '/financeiro');
                $file_name = $this->uploadUtil->uploadFile($request->file, '/financeiro');
            }
            $request->merge([
                'valor_integral' => __convert_value_bd($request->valor_integral),
                'valor_pago' => __convert_value_bd($request->valor_pago) ? __convert_value_bd($request->valor_pago) : 0,
                'arquivo' => $file_name
            ]);
            $item->fill($request->all())->save();

            $descricaoLog = "Vencimento: " . __data_pt($item->data_vencimento) . " R$ " . __moeda($item->valor_integral);
            __createLog($request->empresa_id, 'Conta a Pagar', 'editar', $descricaoLog);
            session()->flash("flash_success", "Conta a pagar atualizada!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            __createLog($request->empresa_id, 'Conta a Pagar', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('conta-pagar.index');
    }

    public function downloadFile($id){
        $item = ContaPagar::findOrFail($id);
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
            'fornecedor_id' => 'required',
            'valor_integral' => 'required',
            'data_vencimento' => 'required',
            'status' => 'required',
            'tipo_pagamento' => 'required'
        ];
        $messages = [
            'fornecedor_id.required' => 'Campo obrigatório',
            'valor_integral.required' => 'Campo obrigatório',
            'data_vencimento.required' => 'Campo obrigatório',
            'status.required' => 'Campo obrigatório',
            'tipo_pagamento.required' => 'Campo obrigatório'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function destroy($id)
    {
        $item = ContaPagar::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = "Vencimento: " . __data_pt($item->data_vencimento, 0) . " R$ " . __moeda($item->valor_integral);
            $item->delete();
            __createLog(request()->empresa_id, 'Conta a Pagar', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Conta removida!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Conta a Pagar', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('conta-pagar.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = ContaPagar::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = "Vencimento: " . __data_pt($item->data_vencimento, 0) . " R$ " . __moeda($item->valor_integral);
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Conta a Pagar', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Conta a Pagar', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->back();
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos");
        return redirect()->back();
    }

    public function pagarSelecionados(Request $request)
    {
        $pagos = 0;
        for($i=0; $i<sizeof($request->item_recebe_paga); $i++){
            $item = ContaPagar::findOrFail($request->item_recebe_paga[$i]);
            $item->status = 1;
            $item->valor_pago = $item->valor_integral;
            $item->data_pagamento = date('Y-m-d');

            $item->save();
            $pagos++;
        }

        session()->flash("flash_success", "Total de itens pagos: $pagos");
        return redirect()->back();
    }

    public function pay($id)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $item = ContaPagar::findOrFail($id);

        if($item->status){
            session()->flash("flash_warning", "Esta conta já esta paga!");
            return redirect()->route('conta-pagar.index');
        }
        return view('conta-pagar.pay', compact('item'));
    }

    public function payPut(Request $request, $id)
    {
        $usuario = Auth::user()->id;
        $caixa = Caixa::where('usuario_id', $usuario)->where('status', 1)->first();
        $item = ContaPagar::findOrFail($id);

        try {
            $item->valor_pago = __convert_value_bd($request->valor_pago);
            $item->status = true;
            $item->data_pagamento = $request->data_pagamento;
            $item->tipo_pagamento = $request->tipo_pagamento;
            $item->caixa_id = $caixa->id;
            $item->save();

            if(isset($request->conta_empresa_id)){

                $data = [
                    'conta_id' => $request->conta_empresa_id,
                    'descricao' => "Pagamento da conta " . $item->id,
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'valor' => $item->valor_pago,
                    'tipo' => 'saida'
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);
            }
            session()->flash("flash_success", "Conta paga!");
        } catch (\Exception $e) {
            // echo $e->getLine();
            // die;
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('conta-pagar.index');
    }

    public function estornar($id)
    {
        $item = ContaPagar::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('conta-pagar.estornar', compact('item'));
    }

    public function estornarUpdate(Request $request, $id){
        $item = ContaPagar::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->status = 0;
            $item->motivo_estorno = $request->motivo_estorno;
            $item->save();

            session()->flash("flash_success", "Conta a pagar estornada!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('conta-pagar.index');
    }
}
