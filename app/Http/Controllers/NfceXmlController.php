<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\Empresa;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Utils\EmailUtil;
use App\Models\EscritorioContabil;

class NfceXmlController extends Controller
{

    protected $emailUtil;
    public function __construct(EmailUtil $util){
        $this->emailUtil = $util;
    }

    public function index(Request $request){
        if (!is_dir(public_path('xml_filtros'))) {
            mkdir(public_path('xml_filtros'), 0777, true);
        }
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $data = [];
        if($start_date || $end_date){
            $data = Nfce::where('empresa_id', request()->empresa_id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_emissao', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('created_at', '<=', $end_date);
            })
            ->when($estado, function ($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->when($local_id, function ($query) use ($local_id) {
                return $query->where('local_id', $local_id);
            })
            ->when(!$local_id, function ($query) use ($locais) {
                return $query->whereIn('local_id', $locais);
            })
            ->get();
        }

        $escritorio = EscritorioContabil::where('empresa_id', $request->empresa_id)
        ->first();
        
        return view('nfce.arquivos_xml', compact('data', 'escritorio'));
    }

    public function download(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $empresa = Empresa::findOrFail($request->empresa_id);
        if($local_id){
            $empresa = __objetoParaEmissao($empresa, $local_id);
        }

        $doc = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
        
        $data = Nfce::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($estado, function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $path = 'xml_nfce/';
        if($estado == 'cancelado'){
            $path = 'xml_nfce_cancelada/';
        }
        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-nfce-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $dataPrint = [];
        foreach($data as $item){
            if (file_exists(public_path($path) . $item->chave . '.xml')) {
                $filename = public_path($path) . $item->chave . '.xml';
                $zip->addFile($filename, $item->chave . '.xml');
                array_push($dataPrint, $item);
            }
        }

        $p = view('nfce/xml_print', compact('empresa', 'dataPrint', 'start_date', 'end_date'));

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $domPdf = new Dompdf($options);

        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        // $domPdf->stream("Somatório de vendas.pdf", array("Attachment" => false));
        // die;
        file_put_contents(public_path('xml_filtros/') .'registros.pdf', $domPdf->output());
        $zip->addFile(public_path('xml_filtros/') .'registros.pdf', 'registros.pdf');

        $zip->close();
        if (file_exists($zip_file)){
            return response()->download($zip_file, 'nfce_'.$doc.'.zip');
        }else{
            session()->flash("flash_error", "Não foi possível gerar o arquivo");
            return redirect()->back();
        }
    }

    public function enviarContador(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $empresa = Empresa::findOrFail($request->empresa_id);
        if($local_id){
            $empresa = __objetoParaEmissao($empresa, $local_id);
        }

        $doc = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
        
        $data = Nfce::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($estado, function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $path = 'xml_nfce/';
        if($estado == 'cancelado'){
            $path = 'xml_nfce_cancelada/';
        }
        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-nfce-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $dataPrint = [];
        foreach($data as $item){
            if (file_exists(public_path($path) . $item->chave . '.xml')) {
                $filename = public_path($path) . $item->chave . '.xml';
                $zip->addFile($filename, $item->chave . '.xml');
                array_push($dataPrint, $item);
            }
        }

        $p = view('nfce/xml_print', compact('empresa', 'dataPrint', 'start_date', 'end_date'));

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $domPdf = new Dompdf($options);

        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        // $domPdf->stream("Somatório de vendas.pdf", array("Attachment" => false));
        // die;
        file_put_contents(public_path('xml_filtros/') .'registros.pdf', $domPdf->output());
        $zip->addFile(public_path('xml_filtros/') .'registros.pdf', 'registros.pdf');

        $zip->close();
        if (file_exists($zip_file)){
            // return response()->download($zip_file, 'nfe_'.$doc.'.zip');
            $body = "Em anexo o arquivo ZIP de arquivos XML período: " . __data_pt($start_date, 0) . " até " . 
            __data_pt($end_date, 0);

            $retorno = $this->emailUtil->enviarXmlContadorZip($empresa->id, $zip_file, 'NFCe', $body);
            if($retorno == 1){
                session()->flash("flash_success", "Email enviado!");
            }else{
                session()->flash("flash_error", "Não foi possível enviar o email!");
            }
        }else{
            session()->flash("flash_error", "Não foi possível gerar o arquivo para envio!");
        }
        return redirect()->back();
    }
}
