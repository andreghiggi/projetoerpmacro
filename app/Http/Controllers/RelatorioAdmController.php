<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\AcessoLog;
use App\Models\PlanoEmpresa;
use Dompdf\Dompdf;
use NFePHP\Common\Certificate;

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
        })->get();


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

}
