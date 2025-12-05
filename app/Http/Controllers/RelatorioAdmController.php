<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\AcessoLog;
use App\Models\PlanoEmpresa;
use Dompdf\Dompdf;
use NFePHP\Common\Certificate;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Fornecedor;

class RelatorioAdmController extends Controller
{
    public function index()
    {
        return view('relatorios_adm.index');
    }

    public function empresas(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;

        $data = Empresa::
        when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($status !== null, function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->get();

        $p = view('relatorios_adm.empresas', compact('data'))
        ->with('title', 'Relatório de Empresas');

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Empresas.pdf", array("Attachment" => false));
    }

    public function historicoAcesso(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $empresa = $request->empresa;

        $data = AcessoLog::
        when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('acesso_logs.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('acesso_logs.created_at', '<=', $end_date);
        })
        ->when($empresa, function ($query) use ($empresa) {
            return $query->where('usuario_empresas.empresa_id', $empresa)
            ->join('usuario_empresas', 'acesso_logs.usuario_id', '=', 'usuario_empresas.usuario_id');
        })
        ->select('acesso_logs.*')
        ->get();

        $p = view('relatorios_adm.historico_acesso', compact('data'))
        ->with('title', 'Relatório de Histórico de Acesso');

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Histórico de Acesso.pdf", array("Attachment" => false));
    }

    public function planos(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = PlanoEmpresa::
        when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_expiracao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data_expiracao', '<=', $end_date);
        })
        ->get();

        $p = view('relatorios_adm.planos', compact('data'))
        ->with('title', 'Relatório de Planos');

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Planos.pdf", array("Attachment" => false));
    }

    public function certificados(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if(!$start_date || !$end_date){
            session()->flash("flash_warning", "Informe a data inicial e final!");
            return redirect()->back();
        }

        $empresas = Empresa::all();

        $data = [];
        $dataHoje = date('Y-m-d');

        foreach($empresas as $e){
            if($e->arquivo){
                try{
                    $infoCertificado = Certificate::readPfx($e->arquivo, $e->senha);
                    $publicKey = $infoCertificado->publicKey;

                    $e->vencimento = $publicKey->validTo->format('Y-m-d');
                    $e->vencido = strtotime($dataHoje) > strtotime($e->vencimento);

                    
                    if((strtotime($e->vencimento) > strtotime($start_date)) && (strtotime($e->vencimento) < strtotime($end_date))){
                        array_push($data, $e);
                    }

                }catch(\Exception $e){
                    echo $e->getMessage();
                }
            }
        }

        usort($data, function($a, $b){
            return strtotime($a->vencimento) > strtotime($b->vencimento) ? 1 : 0;
        });

        $p = view('relatorios_adm.certificados', compact('data'))
        ->with('title', 'Certificados');

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Certificados.pdf", array("Attachment" => false));
    }

    public function resumoOperacional(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;

        $empresas = Empresa::
        when($status !== null, function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->orderBy('nome')
        // ->limit(1)
        ->get();

        $data = [];
        foreach($empresas as $e){
            $contaAcessos = AcessoLog::
            when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('acesso_logs.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                return $query->whereDate('acesso_logs.created_at', '<=', $end_date);
            })
            ->where('usuario_empresas.empresa_id', $e->id)
            ->join('usuario_empresas', 'acesso_logs.usuario_id', '=', 'usuario_empresas.usuario_id')
            ->select('acesso_logs.*')
            ->count();

            $vendas = Nfe::where('empresa_id', $e->id)
            ->where('tpNF', 1)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                return $query->whereDate('created_at', '<=', $end_date);
            })
            ->selectRaw('COUNT(*) as total_vendas, SUM(total) as soma_vendas')
            ->first();

            $somaVendas = $vendas->soma_vendas ?? 0;
            $contaVendas = $vendas->total_vendas ?? 0;

            $vendas = Nfce::
            where('empresa_id', $e->id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                return $query->whereDate('created_at', '<=', $end_date);
            })
            ->selectRaw('COUNT(*) as total_vendas, SUM(total) as soma_vendas')
            ->first();

            $somaVendas += $vendas->soma_vendas ?? 0;
            $contaVendas += $vendas->total_vendas ?? 0;

            $compras = Nfe::where('empresa_id', $e->id)
            ->where('tpNF', 0)
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->selectRaw('COUNT(*) as total_compras, SUM(total) as soma_compras')
            ->first();

            $contasRecebidas = ContaReceber::
            where('empresa_id', $e->id)
            ->where('status', 1)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_vencimento', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                return $query->whereDate('data_vencimento', '<=', $end_date);
            })
            ->selectRaw('COUNT(*) as total_contas, SUM(valor_recebido) as soma_contas')
            ->first();

            $contasReceber = ContaReceber::
            where('empresa_id', $e->id)
            ->where('status', 0)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_vencimento', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                return $query->whereDate('data_vencimento', '<=', $end_date);
            })
            ->selectRaw('COUNT(*) as total_contas, SUM(valor_integral) as soma_contas')
            ->first();


            $contasPagas = ContaPagar::
            where('empresa_id', $e->id)
            ->where('status', 1)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_vencimento', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                return $query->whereDate('data_vencimento', '<=', $end_date);
            })
            ->selectRaw('COUNT(*) as total_contas, SUM(valor_pago) as soma_contas')
            ->first();

            $contasPagar = ContaPagar::
            where('empresa_id', $e->id)
            ->where('status', 0)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_vencimento', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                return $query->whereDate('data_vencimento', '<=', $end_date);
            })
            ->selectRaw('COUNT(*) as total_contas, SUM(valor_integral) as soma_contas')
            ->first();

            $data[] = (object)[
                'empresa' => $e->nome,
                'cpf_cnpj' => $e->cpf_cnpj,
                'endereco' => $e->endereco,
                'celular' => $e->celular,
                'contador_acessos' => $contaAcessos,
                'contador_vendas' => $contaVendas,
                'soma_vendas' => $somaVendas,
                'contador_compras' => $compras->total_compras ?? 0,
                'soma_compras' => (float)$compras->soma_compras ?? 0,

                'contador_conta_recebidas' => $contasRecebidas->total_contas ?? 0,
                'soma_conta_recebidas' => (float)$contasRecebidas->soma_contas ?? 0,

                'contador_conta_receber' => $contasReceber->total_contas ?? 0,
                'soma_conta_receber' => (float)$contasReceber->soma_contas ?? 0,

                'contador_conta_pagas' => $contasPagas->total_contas ?? 0,
                'soma_conta_pagas' => (float)$contasPagas->soma_contas ?? 0,

                'contador_conta_pagar' => $contasPagar->total_contas ?? 0,
                'soma_conta_pagar' => (float)$contasPagar->soma_contas ?? 0,
            ];
        }

        $p = view('relatorios_adm.resumo_operacional', compact('data', 'start_date', 'end_date'))
        ->with('title', 'Resumo Operacional');

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Resumo Operacional.pdf", array("Attachment" => false));
    }

}
