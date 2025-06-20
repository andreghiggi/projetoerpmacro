<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cte;
use App\Models\Empresa;

class CteXmlController extends Controller
{
    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $data = [];
        if($start_date || $end_date){
            $data = Cte::where('empresa_id', request()->empresa_id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('created_at', '<=', $end_date);
            })
            ->where('estado', 'aprovado')
            ->get();

        }

        return view('cte.arquivos_xml', compact('data'));
    }

    public function download(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $empresa = Empresa::findOrFail($request->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
        
        $data = Cte::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('estado', 'aprovado')
        ->get();

        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-cte-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach($data as $item){
            if (file_exists(public_path('xml_cte/') . $item->chave . '.xml')) {
                $filename = public_path('xml_cte/') . $item->chave . '.xml';
                $zip->addFile($filename, $item->chave . '.xml');
            }
        }

        $zip->close();
        if (file_exists($zip_file)){
            return response()->download($zip_file, 'cte_'.$doc.'.zip');
        }else{
            session()->flash("flash_error", "Não foi possível gerar o arquivo");
            return redirect()->back();
        }
    }
}
