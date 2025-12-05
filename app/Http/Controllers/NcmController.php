<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProdutoImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ncm;

class NcmController extends Controller
{
    public function index(Request $request){
        $this->validaNcm();
        $data = Ncm::
        when(!empty($request->descricao), function ($q) use ($request) {
            return $q->where('descricao', 'LIKE', "%$request->descricao%");
        })
        ->paginate(50);
        return view('ncm.index', compact('data'));
    }

    public function create()
    {
        return view('ncm.create');
    }

    public function edit($id)
    {
        $item = Ncm::findOrFail($id);

        return view('ncm.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            $request->merge([
                'descricao' => "$request->codigo - $request->descricao"
            ]);

            Ncm::create($request->all());
            session()->flash("flash_success", "NCM criado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('ncm.index');
    }

    public function update(Request $request, $id)
    {
        $item = Ncm::findOrFail($id);
        try {

            $item->fill($request->all())->save();
            session()->flash("flash_success", "NCM alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());

        }
        return redirect()->route('ncm.index');
    }

    public function destroy($id)
    {
        $item = Ncm::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "NCM removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('ncm.index');
    }

    public function refresh(){
        return view('ncm.upload');
    }

    private function validaNcm(){
        if(!file_exists(public_path('tabela_ncm.xlsx'))){
            abort("403", "Arquivo tabela_ncm.xlsx não encontrado!");
        }
        $data = Ncm::count();
        if($data == 0){

            $file = file_get_contents(public_path('tabela_ncm.xlsx'));

            $rows = Excel::toArray(new ProdutoImport, public_path('tabela_ncm.xlsx'));

            foreach($rows[0] as $key => $line){

                if(isset($line[0])){
                    $codigo = trim($line[0]);
                    $descricao = trim($line[1]);

                    if($key > 4){
                        $descricao = str_replace("--", "", $descricao);
                        $descricao = str_replace("-", "", $descricao);

                        Ncm::create([
                            'codigo' => $codigo,
                            'descricao' => "$codigo - $descricao"
                        ]);
                    }
                }
            }
        }
    }

    public function import(Request $request){
        if ($request->hasFile('file')) {

            $arquivo = $request->hasFile('file');
            $file = $request->file;
            Ncm::query()->delete();

            $rows = Excel::toArray(new ProdutoImport, $file);
            $isNcm = 0;
            foreach($rows[0] as $key => $line){

                if(isset($line[0])){
                    $codigo = trim($line[0]);
                    $descricao = trim($line[1]);
                    if(is_numeric($codigo) && $isNcm == 0){
                        $isNcm = 1;
                    }
                    if($isNcm){
                        $descricao = str_replace("--", "", $descricao);
                        $descricao = str_replace("-", "", $descricao);

                        Ncm::create([
                            'codigo' => $codigo,
                            'descricao' => "$codigo - $descricao"
                        ]);
                    }
                    
                }
            }
            session()->flash("flash_success", "Registro atualizado!");
            return redirect()->route('ncm.index');
        } else {
            session()->flash('flash_error', 'Arquivo inválido!');
            return redirect()->back();
        }
    }
}
