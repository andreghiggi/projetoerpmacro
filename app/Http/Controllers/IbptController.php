<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ibpt;
use App\Models\ItemIbpt;

class IbptController extends Controller
{
    public function index(Request $request){
        $data = Ibpt::orderBy('uf', 'desc')->get();

        return view('ibpt.index', compact('data'));
    }

    public function create(){
        return view('ibpt.create');
    }

    public function store(Request $request){
        try{
            if ($request->hasFile('file')){

                $file = $request->file;
                $handle = fopen($file, "r");
                $row = 0;
                $linhas = [];

                $item = Ibpt::where('uf', $request->uf)->first();
                if($item != null){
                    session()->flash('flash_error', 'UF já cadastrada!');
                    return redirect()->back();
                }

                $ibpt = Ibpt::create(
                    [
                        'uf' => $request->uf,
                        'versao' => $request->versao,
                    ]
                );
                $cont = 0;
                while ($line = fgetcsv($handle, 1000, ";")) {
                    if ($row++ == 0) {
                        continue;
                    }

                    $data = [
                        'ibpt_id' => $ibpt->id,
                        'codigo' => $line[0],
                        'descricao' => $line[3],
                        'nacional_federal' => $line[4],
                        'importado_federal' => $line[5],
                        'estadual' => $line[6],
                        'municipal' => $line[7] 
                    ];
                    $cont++;

                    $item = ItemIbpt::create($data);
                    // print_r($data);
                    // echo "<br>";

                    // if($cont == 20)die;
                }
            }
            session()->flash('flash_success', 'Tabela importada!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('ibpt.index');
    }

    public function show($id){
        $item = Ibpt::findOrFail($id);
        $data = ItemIbpt::where('ibpt_id', $id)
        ->paginate(30);
        return view('ibpt.show', compact('data', 'item'));
    }

    public function destroy($id)
    {
        $item = Ibpt::findOrFail($id);
        try {
            $item->itens()->delete();
            $item->delete();
            session()->flash('flash_success', 'Removido com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_warning', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('ibpt.index');
    }
}
