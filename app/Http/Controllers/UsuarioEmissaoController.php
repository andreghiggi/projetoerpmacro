<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioEmissao;
use App\Models\User;

class UsuarioEmissaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:config_fiscal_usuario_view', ['only' => ['create', 'store']]);
        $this->middleware('permission:config_fiscal_usuario_create', ['only' => ['edit', 'update']]);
        $this->middleware('permission:config_fiscal_usuario_edit', ['only' => ['show', 'index']]);
        $this->middleware('permission:config_fiscal_usuario_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {   

        $data = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
        ->select('usuario_emissaos.*')
        ->when(!empty($request->usuario_id), function ($q) use ($request) {
            return $q->where('usuario_emissaos.usuario_id', $request->usuario_id);
        })
        ->paginate(__itensPagina());

        $usuario = User::find($request->usuario_id);
        return view('configuracao_usuario_emissao.index', compact('data', 'usuario'));
    }

    public function create()
    {
        return view('configuracao_usuario_emissao.create');
    }

    public function edit($id)
    {
        $item = UsuarioEmissao::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('configuracao_usuario_emissao.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            $item = UsuarioEmissao::where('usuario_id', $request->usuario_id)
            ->first();

            if($item != null){
                session()->flash('flash_warning', 'Já existe uma configuração para esse usuário!');
                session()->flash('flash_audio_error', 1);
                return redirect()->back();
            }

            UsuarioEmissao::create($request->all());
            __createLog($request->empresa_id, 'Configuração Usuário Emissão', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Configuração Usuário Emissão', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('config-fiscal-usuario.index');
    }

    public function update(Request $request, $id)
    {
        $item = UsuarioEmissao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $config = UsuarioEmissao::where('usuario_id', $request->usuario_id)
            ->first();

            if($config != null && $config->usuario_id != $item->usuario_id){
                session()->flash('flash_warning', 'Já existe uma configuração para esse usuário!');
                session()->flash('flash_audio_error', 1);
                return redirect()->back();
            }

            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Configuração Usuário Emissão', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Configuração Usuário Emissão', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('config-fiscal-usuario.index');
    }

    public function destroy($id)
    {
        $item = UsuarioEmissao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Configuração Usuário Emissão', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Configuração Usuário Emissão', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }
}
