<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CrmAnotacao;
use App\Models\CrmAnotacaoNota;
use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Funcionario;
use App\Models\Empresa;
use Dompdf\Dompdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RelatorioCrmExport;

class CrmController extends Controller
{
    public function index(Request $request){

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $fornecedor_id = $request->get('fornecedor_id');
        $funcionario_id = $request->get('funcionario_id');
        $status = $request->get('status');

        $data = CrmAnotacao::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($fornecedor_id), function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->when(!empty($funcionario_id), function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->paginate(__itensPagina());

        $cliente = null;
        $fornecedor = null;
        $funcionario = null;

        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }

        if($fornecedor_id){
            $fornecedor = Fornecedor::findOrFail($fornecedor_id);
        }

        if($funcionario_id){
            $funcionario = Funcionario::findOrFail($funcionario_id);
        }

        return view('crm.index', compact('data', 'cliente', 'fornecedor', 'funcionario'));
    }

    public function create(){
        return view('crm.create');
    }

    public function store(Request $request){
        try{

            CrmAnotacao::create($request->all());
            session()->flash('flash_success', 'Cadastrado com sucesso.');
            return redirect()->route('crm.index');

        }catch(\Exception $e){
            session()->flash('flash_error', 'Não foi possível concluir o cadastro: ' . $e->getMessage());
            return redirect()->back();

        }
    }

    public function edit($id){
        $item = CrmAnotacao::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('crm.edit', compact('item'));
    }

    public function show($id){
        $item = CrmAnotacao::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('crm.show', compact('item'));
    }

    public function update(Request $request, $id){
        $item = CrmAnotacao::findOrFail($id);
        try{

            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Atualizado com sucesso.');
            return redirect()->route('crm.index');

        }catch(\Exception $e){
            session()->flash('flash_error', 'Não foi possível alterar o cadastro: ' . $e->getMessage());
            return redirect()->back();

        }
    }

    public function destroy($id)
    {
        $item = CrmAnotacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        
        try {
            $item->delete();
            session()->flash("flash_success", "Registro removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function storeNota(Request $request, $id){
        $item = CrmAnotacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            CrmAnotacaoNota::create([
                'nota' => $request->nota,
                'crm_anotacao_id' => $id
            ]);
            session()->flash("flash_success", "Nota salva!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();

    }

    public function destroyNota($id)
    {
        $item = CrmAnotacaoNota::findOrFail($id);
        
        try {
            $item->delete();
            session()->flash("flash_success", "Registro removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function print(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $fornecedor_id = $request->get('fornecedor_id');
        $funcionario_id = $request->get('funcionario_id');
        $status = $request->get('status');
        $excel = $request->excel;

        $data = CrmAnotacao::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($fornecedor_id), function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->when(!empty($funcionario_id), function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->get();

        $config = Empresa::where('id', $request->empresa_id)->first();
        if($excel == -1){
            $p = view('crm.print', compact('config', 'data'));

            $domPdf = new Dompdf(["enable_remote" => true]);
            $domPdf->loadHtml($p);
            $pdf = ob_get_clean();
            $domPdf->setPaper("A4", "landscape");
            $domPdf->render();
            header("Content-Disposition: ; filename=CRM relatório.pdf");
            $domPdf->stream("CRM relatório.pdf", array("Attachment" => false));
        }else{
            $relatorioEx = new RelatorioCrmExport($data);
            return Excel::download($relatorioEx, 'crm.xlsx');
        }
    }

}
