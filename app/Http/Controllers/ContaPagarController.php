<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Cliente;
use App\Models\ContaPagar;
use App\Models\Fornecedor;
use App\Models\ItemContaEmpresa;
use App\Models\ManutencaoVeiculo;
use App\Models\DespesaFrete;
use App\Models\CategoriaConta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\ContaEmpresaUtil;
use App\Utils\UploadUtil;
use App\Exports\ContaPagarExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $filtro_data = $request->filtro_data;
        $categoria_conta_id = $request->categoria_conta_id;
        $local_id = $request->get('local_id');

        if($filtro_data == 'data_pagamento'){
            $status = 1;
        }

        $data = ContaPagar::where('empresa_id', request()->empresa_id)
        ->when(!empty($fornecedor_id), function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date, $filtro_data) {
            return $query->whereDate($filtro_data, '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date, $filtro_data) {
            return $query->whereDate($filtro_data, '<=', $end_date);
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
        ->when($status !== null, function ($query) use ($status) {
            if($status != -1){
                return $query->where('status', $status);
            }else{
                return $query->where('status', 0)
                ->whereDate('data_vencimento', '<', date('Y-m-d'));
            }
        })
        ->when($ordem != '', function ($query) use ($ordem, $filtro_data) {
            return $query->orderBy($filtro_data, 'asc');
        })
        ->when($ordem == '', function ($query) use ($ordem) {
            return $query->orderBy('created_at', 'asc');
        })
        ->paginate(__itensPagina());

        $fornecedor = null;
        if($fornecedor_id){
            $fornecedor = Fornecedor::findOrFail($fornecedor_id);
        }

        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'pagar')->get();

        return view('conta-pagar.index', compact('data', 'fornecedor', 'categorias'));
    }

    public function exportExcel(Request $request){
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $fornecedor_id = $request->fornecedor_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $ordem = $request->ordem;
        $categoria_conta_id = $request->categoria_conta_id;
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

        $file = new ContaPagarExport($data);
        return Excel::download($file, 'contas_pagar.xlsx');
    }

    public function create()
    {
        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)->get();

        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'pagar')->get();

        return view('conta-pagar.create', compact('fornecedores', 'categorias'));
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
                'valor_pago' => $request->valor_pago ? __convert_value_bd($request->valor_pago) : 0,
                'arquivo' => $file_name,
                'descricao' => $descricao . " " . $referencia
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
                    $referencia = "Parcela ".($i+2)." de " . sizeof($request->dt_recorrencia)+1;

                    $data = [
                        'venda_id' => null,
                        'data_vencimento' => $data,
                        'data_pagamento' => $data,
                        'valor_integral' => $valor,
                        'valor_pago' => $request->status ? $valor : 0,
                        'descricao' => $descricao . " " . $referencia,
                        'categoria_conta_id' => $request->categoria_conta_id,
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
        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'pagar')->get();
        return view('conta-pagar.edit', compact('item', 'fornecedores', 'categorias'));
    }

    public function show($id)
    {
        $item = ContaPagar::findOrFail($id);
        __validaObjetoEmpresa($item);

        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)->get();
        $categorias = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->where('tipo', 'pagar')->get();
        return view('conta-pagar.show', compact('item', 'fornecedores', 'categorias'));
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
        return redirect()->back();
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

        $data = [];
        $total = 0;
        for($i=0; $i<sizeof($request->item_recebe_paga); $i++){
            $item = ContaPagar::findOrFail($request->item_recebe_paga[$i]);
            $data[] = $item;
            $total += $item->valor_integral;
        }
        return view('conta-pagar.pay_select', compact('data', 'total'));
    }

    public function paySelect(Request $request){
        for ($i = 0; $i < sizeof($request->conta_id); $i++) {
            $item = ContaPagar::findOrFail($request->conta_id[$i]);

            $item->valor_pago = __convert_value_bd($request->valor_pago[$i]);
            $item->status = 1;
            $item->data_pagamento = $request->data_pagamento[$i];
            $item->tipo_pagamento = $request->tipo_pagamento[$i];

            if(isset($request->conta_empresa_id[$i])){
                $item->conta_empresa_id = $request->conta_empresa_id[$i];

                $nDoc = '';
                $descricao = "Pagamento da conta";
                if($item->nfe){

                    if($item->descricao){
                        $descricao .= " " . $item->descricao . " ";
                    }
                    $descricao .= " - valor integral R$" . __moeda($item->valor_integral) . " referente a compra Nº " . $item->nfe->numero_sequencial;
                    if($item->nfe->estado == 'aprovado'){
                        $descricao .= ", emitida em " . __data_pt($item->nfe->data_emissao);
                        $nDoc = $item->nfe->numero;
                    }

                    $descricao .= " VALOR TOTAL COMPRA R$ " . __moeda($item->nfe->total);
                }
                $data = [
                    'conta_id' => $request->conta_empresa_id[$i],
                    'descricao' => $descricao,
                    'tipo_pagamento' => $request->tipo_pagamento[$i],
                    'valor' => __convert_value_bd($request->valor_pago[$i]),
                    'tipo' => 'saida',
                    'fornecedor_id' => $item->fornecedor_id,
                    'numero_documento' => $nDoc,
                    'conta_pagar_id' => $item->id,
                    'categoria_id' => $item->categoria_conta_id
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);
            }
            $item->save();

            session()->flash("flash_success", "Contas pagas!");
        }
        return redirect()->route('conta-pagar.index');
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
            $vPago = __convert_value_bd($request->valor_pago);
            if($item->valor_integral > $vPago){
                $item->desconto = $item->valor_integral - $vPago;
            }
            if($item->valor_integral < $vPago){
                $item->acrescimo = $vPago - $item->valor_integral;
            }
            
            $item->valor_pago = $vPago;
            $item->status = true;
            $item->data_pagamento = $request->data_pagamento;
            $item->tipo_pagamento = $request->tipo_pagamento;
            $item->caixa_id = $caixa->id;
            $item->save();

            if(isset($request->conta_empresa_id)){
                $item->conta_empresa_id = $request->conta_empresa_id;

                $nDoc = '';
                $descricao = "Pagamento de conta";
                if($item->nfe){
                    if($item->descricao){
                        $descricao .= " " . $item->descricao . " ";
                    }
                    $descricao .= " - valor integral da parcela R$" . __moeda($item->valor_integral) . " referente a compra Nº " . $item->nfe->numero_sequencial;
                    if($item->nfe->estado == 'aprovado'){
                        $descricao .= ", emitida em " . __data_pt($item->nfe->data_emissao);
                        $nDoc = $item->nfe->numero;
                    }

                    $descricao .= " VALOR TOTAL COMPRA R$ " . __moeda($item->nfe->total);
                }
                $data = [
                    'conta_id' => $request->conta_empresa_id,
                    'descricao' => $descricao,
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'valor' => $item->valor_pago,
                    'tipo' => 'saida',
                    'fornecedor_id' => $item->fornecedor_id,
                    'numero_documento' => $nDoc,
                    'conta_pagar_id' => $item->id,
                    'categoria_id' => $item->categoria_conta_id
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);
            }
            $item->save();
            
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
