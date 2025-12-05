<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServico;
use App\Utils\UploadUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaServicoController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:categoria_servico_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categoria_servico_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categoria_servico_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:categoria_servico_delete', ['only' => ['destroy']]);
    }

    private function insertHash(){
        $data = CategoriaServico::where('empresa_id', request()->empresa_id)
        ->where('hash_delivery', null)->get();
        foreach($data as $c){
            $c->hash_delivery = Str::random(50);
            $c->save();
        }
    }

    public function index(Request $request)
    {
        $this->insertHash();
        
        $data = CategoriaServico::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('categoria_servico.index', compact('data'));
    }

    public function create()
    {
        return view('categoria_servico.create');
    }

    public function edit($id)
    {
        $item = CategoriaServico::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('categoria_servico.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $file_name = '';
            if ($request->hasFile('image')) {
                $file_name = $this->util->uploadImage($request, '/categoriaServico');
            }

            if ($request->marketplace) {
                $request->merge([
                    'hash_delivery' => Str::random(50),
                ]);
            }

            $request->merge([
                'imagem' => $file_name
            ]);
            CategoriaServico::create($request->all());
            __createLog($request->empresa_id, 'Categoria de Serviço', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Categoria de Serviço', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('categoria-servico.index');
    }

    public function update(Request $request, $id)
    {
        $item = CategoriaServico::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $file_name = $item->imagem;

            if ($request->hasFile('image')) {
                $this->util->unlinkImage($item, '/categoriaServico');
                $file_name = $this->util->uploadImage($request, '/categoriaServico');
            }

            if ($request->marketplace) {
                $request->merge([
                    'hash_delivery' => Str::random(50),
                ]);
            }

            $request->merge([
                'imagem' => $file_name
            ]);
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Categoria de Serviço', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Categoria de Serviço', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('categoria-servico.index');
    }

    public function destroy($id)
    {
        $item = CategoriaServico::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Categoria de Serviço', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Categoria removida com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Categoria de Serviço', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar: ' . $e->getMessage());
        }
        return redirect()->route('categoria-servico.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = CategoriaServico::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->nome;
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Categoria de Serviço', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Categoria de Serviço', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->back();
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();
    }
}
