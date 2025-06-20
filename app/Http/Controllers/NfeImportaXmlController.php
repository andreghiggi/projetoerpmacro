<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Empresa;
use App\Models\EscritorioContabil;
use App\Utils\EmailUtil;

class NfeImportaXmlController extends Controller
{
    protected $emailUtil;
    public function __construct(EmailUtil $util){
        $this->emailUtil = $util;
    }

    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $data = [];
        if($start_date || $end_date){
            $data = Nfe::where('empresa_id', request()->empresa_id)->where('orcamento', 0)
            ->where('tpNF', 0)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('created_at', '<=', $end_date);
            })
            ->where('chave_importada', '!=', '')
            ->where('estado', 'novo')
            ->get();

        }

        $escritorio = EscritorioContabil::where('empresa_id', $request->empresa_id)
        ->first();
        return view('compras.arquivos_xml_importados', compact('data', 'escritorio'));
    }

    public function download(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $empresa = Empresa::findOrFail($request->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);

        $data = Nfe::where('empresa_id', request()->empresa_id)->where('orcamento', 0)
        ->where('tpNF', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('chave_importada', '!=', '')
        ->where('estado', 'novo')
        ->get();


        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $cont = 0;
        foreach($data as $item){
            if (file_exists(public_path('xml_entrada/') . $item->chave_importada . '.xml')) {
                $cont++;
                $filename = public_path('xml_entrada/') . $item->chave_importada . '.xml';
                $zip->addFile($filename, $item->chave_importada . '.xml');
            }
        }

        $zip->close();
        if (file_exists($zip_file)){
            return response()->download($zip_file, 'nfe_importada_'.$doc.'.zip');
        }else{
            session()->flash("flash_error", "Não foi possível gerar o arquivo");
            return redirect()->back();
        }
    }

    public function enviarContador(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $empresa = Empresa::findOrFail($request->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);

        $data = Nfe::where('empresa_id', request()->empresa_id)->where('orcamento', 0)
        ->where('tpNF', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('chave_importada', '!=', '')
        ->where('estado', 'novo')
        ->get();

        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach($data as $item){
            if (file_exists(public_path('xml_entrada/') . $item->chave_importada . '.xml')) {
                $filename = public_path('xml_entrada/') . $item->chave_importada . '.xml';
                $zip->addFile($filename, $item->chave . '.xml');
            }
        }

        $zip->close();
        if (file_exists($zip_file)){
            // return response()->download($zip_file, 'nfe_'.$doc.'.zip');
            $body = "Em anexo o arquivo ZIP de arquivos XML período: " . __data_pt($start_date, 0) . " até " . 
            __data_pt($end_date, 0);

            $retorno = $this->emailUtil->enviarXmlContadorZip($empresa->id, $zip_file, 'NFe Importada', $body);
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
