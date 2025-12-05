<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormatoArmacaoOtica;
use App\Utils\UploadUtil;

class FormatoArmacaoOticaController extends Controller
{

    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:formato_armacao_view', ['only' => ['create', 'store']]);
        $this->middleware('permission:formato_armacao_create', ['only' => ['edit', 'update']]);
        $this->middleware('permission:formato_armacao_edit', ['only' => ['show', 'index']]);
        $this->middleware('permission:formato_armacao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {   
        $data = FormatoArmacaoOtica::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('formato_armacao.index', compact('data'));
    }

    public function create()
    {
        return view('formato_armacao.create');
    }

    public function edit($id)
    {
        $item = FormatoArmacaoOtica::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('formato_armacao.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $file_name = '';
            if ($request->hasFile('image')) {
                $file_name = $this->util->uploadImage($request, '/formato_armacao');
            }

            $request->merge([
                'imagem' => $file_name
            ]);
            FormatoArmacaoOtica::create($request->all());
            __createLog($request->empresa_id, 'Formato de Armação', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Formato de Armação', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('formato-armacao.index');
    }

    public function update(Request $request, $id)
    {
        $item = FormatoArmacaoOtica::findOrFail($id);
        __validaObjetoEmpresa($item);

        $file_name = $item->imagem;

        if ($request->hasFile('image')) {
            $this->util->unlinkImage($item, '/formato_armacao');
            $file_name = $this->util->uploadImage($request, '/formato_armacao');
        }

        $request->merge([
            'imagem' => $file_name
        ]);
        try {

            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Formato de Armação', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Formato de Armação', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('formato-armacao.index');
    }

    public function destroy($id)
    {
        $item = FormatoArmacaoOtica::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;

            $this->util->unlinkImage($item, '/formato_armacao');
            $item->delete();
            __createLog(request()->empresa_id, 'Formato de Armação', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Formato de Armação', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }
}
