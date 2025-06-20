<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Empresa;
use NFePHP\DA\NFe\Danfe;

class ContadorAdminNFeController extends Controller
{
    public function __construct(){
        if (!is_dir(public_path('zips'))) {
            mkdir(public_path('zips'), 0777, true);
        }
    }

    public function nfe(Request $request){
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $tpNF = $request->get('tpNF');
        $estado = $request->get('estado');

        $data = Nfe::where('empresa_id', $empresaSelecionada)->where('orcamento', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($tpNF != "", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(env("PAGINACAO"));

        $contXml = $this->preparaXmls($start_date, $end_date, $tpNF, $empresaSelecionada, $estado);
        return view('contador.nfe', compact('data', 'contXml'));
    }

    private function preparaXmls($start_date, $end_date, $tpNF, $empresaSelecionada, $estado){

        $data = Nfe::where('empresa_id', $empresaSelecionada)->where('orcamento', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($tpNF != "", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->where('estado', $estado)
        ->get();

        $dir = 'xml_nfe/';
        if($estado == 'cancelado'){
            $dir = 'xml_nfe_cancelada/';
        }

        $cont = 0;
        foreach($data as $item){
            if (file_exists(public_path($dir) . $item->chave . '.xml')) {
                $cont++;
            }
        }
        return $cont;

    }

    public function downloadNFe($id){
        $item = Nfe::findOrFail($id);
        $dir = 'xml_nfe/';
        if($item->estado == 'cancelado'){
            $dir = 'xml_nfe_cancelada/';
        }
        if (file_exists(public_path($dir) . $item->chave . '.xml')) {
            return response()->download(public_path($dir) . $item->chave . '.xml');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function danfe($id){
        $item = Nfe::findOrFail($id);

        if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfe/') . $item->chave . '.xml');

            $danfe = new Danfe($xml);
            $pdf = $danfe->render();
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function downloadZip(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $tpNF = $request->get('tpNF');
        $estado = $request->get('estado');

        $contador = Empresa::findOrFail(request()->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $contador->cpf_cnpj);
        $empresaSelecionada = $contador->empresa_selecionada;
        $data = Nfe::where('empresa_id', $empresaSelecionada)->where('orcamento', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($tpNF != "", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->where('estado', $estado)
        ->get();
        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $dir = 'xml_nfe/';
        if($estado == 'cancelado'){
            $dir = 'xml_nfe_cancelada/';
        }

        foreach($data as $item){
            if (file_exists(public_path($dir) . $item->chave . '.xml')) {
                $filename = public_path($dir) . $item->chave . '.xml';

                $zip->addFile($filename, $item->chave . '.xml');
            }
        }
        $zip->close();
        if (file_exists($zip_file)){
            return response()->download($zip_file, 'nfe_'.$doc.'.zip');
        }else{
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }
}
