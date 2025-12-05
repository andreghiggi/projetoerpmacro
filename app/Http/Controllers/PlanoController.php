<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Segmento;
use App\Models\Empresa;
use App\Utils\UploadUtil;
use App\Utils\ModuloUtil;

class PlanoController extends Controller
{
    protected $util;
    protected $moduloUtil;

    public function __construct(UploadUtil $util, ModuloUtil $moduloUtil)
    {
        $this->util = $util;
        $this->moduloUtil = $moduloUtil;
    }

    public function index(Request $request)
    {
        $contador_id = $request->contador_id;
        $data = Plano::when(!empty($request->nome), function ($q) use ($request) {
            return $q->where(function ($quer) use ($request) {
                return $quer->where('nome', 'LIKE', "%$request->nome%");
            });
        })
        ->when($contador_id, function ($query) use ($contador_id) {
            return $query->where('contador_id', $contador_id);
        })
        ->paginate(__itensPagina());

        $contadores = Empresa::where('tipo_contador', 1)
        ->orderBy('nome')->get();

        return view('planos.index', compact('data', 'contadores'));
    }

    public function create()
    {
        $modulos = $this->moduloUtil->getModulos();
        $segmentos = Segmento::orderBy('nome', 'desc')
        ->get();
        return view('planos.create', compact('modulos', 'segmentos'));
    }

    public function edit($id)
    {
        $item = Plano::findOrFail($id);
        $modulos = $this->moduloUtil->getModulos();
        $item->modulos = json_decode($item->modulos);
        if($item->modulos == null){
            $item->modulos = []; 
        }

        $segmentos = Segmento::orderBy('nome', 'desc')
        ->get();
        return view('planos.edit', compact('item', 'modulos', 'segmentos'));
    }

    public function store(Request $request)
    {
        $this->_validate($request);

        if($request->auto_cadastro == 1){
            Plano::where('auto_cadastro', 1)->update(['auto_cadastro' => 0]);
        }
        try {
            $file_name = '';
            if ($request->hasFile('image')) {
                $file_name = $this->util->uploadImage($request, '/planos');
            }
            $request->merge([
                'descricao' => $request->descricao ?? '',
                'valor' => __convert_value_bd($request->valor),
                'valor_implantacao' => $request->valor_implantacao ? __convert_value_bd($request->valor_implantacao) : 0,
                'imagem' => $file_name,
            ]);

            if(!isset($request->modulos)){
                $request->merge([
                    'modulos' => '[]'
                ]);
            }else{
                $request->merge([
                    'modulos' => json_encode($request->modulos)
                ]);
            }
            Plano::create($request->all());
            session()->flash("flash_success", "Cadastro com Sucesso");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('planos.index');
    }

    public function update(Request $request, $id)
    {
        $this->_validate($request);
        if($request->auto_cadastro == 1){
            Plano::where('auto_cadastro', 1)->update(['auto_cadastro' => 0]);
        }
        $item = Plano::findOrfail($id);

        try {
            $file_name = $item->imagem;
            if ($request->hasFile('image')) {
                $this->util->unlinkImage($item, '/planos');
                $file_name = $this->util->uploadImage($request, '/planos');
            }
            $request->merge([
                'descricao' => $request->descricao ?? '',
                'valor' => __convert_value_bd($request->valor),
                'valor_implantacao' => $request->valor_implantacao ? __convert_value_bd($request->valor_implantacao) : 0,
                'imagem' => $file_name,
            ]);

            if(!isset($request->modulos)){
                $request->merge([
                    'modulos' => '[]'
                ]);
            }else{
                $request->merge([
                    'modulos' => json_encode($request->modulos)
                ]);
            }
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Cadastro Atualizado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('planos.index');
    }


    private function _validate(Request $request)
    {
        $rules = [
            'nome' => 'required|max:50'
        ];
        $messages = [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.max' => '50 caracteres maximos permitidos.'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function destroy($id)
    {
        $item = Plano::findOrFail($id);
        try {
            $item->delete();
            session()->flash('flash_success', 'Removido com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->back();
    }
}
