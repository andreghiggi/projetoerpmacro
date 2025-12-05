<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsuarioEmpresa;
use App\Models\Funcionario;
use App\Models\FuncionarioServico;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Ui\Presets\React;

class FuncionarioController extends Controller
{   

    public function __construct()
    {
        $this->middleware('permission:funcionario_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:funcionario_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:funcionario_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:funcionario_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Funcionario::where('empresa_id', request()->empresa_id)
        ->orderBy('created_at', 'desc')
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where(function ($quer) use ($request) {
                return $quer->where('nome', 'LIKE', "%$request->nome%");
            });
        })
        ->paginate(__itensPagina());
        return view('funcionario.index', compact('data'));
    }

    public function create()
    {
        $usuario = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();
        return view('funcionario.create', compact('usuario'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'comissao' => $request->comissao ? __convert_value_bd($request->comissao) : 0,
                'salario' => $request->salario ? __convert_value_bd($request->salario) : 0,
            ]);

            if($request->codigo){
                $funcionario = Funcionario::where('empresa_id', $request->empresa_id)
                ->where('codigo', $request->codigo)->first();
                if($funcionario != null){
                    session()->flash("flash_error", "Já existe um funcionário com esse código!");
                    return redirect()->back();
                }
            }
            __createLog($request->empresa_id, 'Funcionario', 'cadastrar', $request->nome);
            Funcionario::create($request->all());
            session()->flash("flash_success", "Cadastrado com Sucesso");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Funcionario', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possivel fazer o cadastro " . $e->getMessage());
        }
        return redirect()->route('funcionarios.index');
    }

    public function edit($id)
    {
        $item = Funcionario::findOrFail($id);
        $usuario = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();
        return view('funcionario.edit', compact('item', 'usuario'));
    }

    public function update(Request $request, $id)
    {
        $item = Funcionario::findOrFail($id);
        try {
            $request->merge([
                'comissao' => $request->comissao ? __convert_value_bd($request->comissao) : 0,
                'salario' => $request->salario ? __convert_value_bd($request->salario) : 0,
            ]);

            if($request->codigo){
                $funcionario = Funcionario::where('empresa_id', $request->empresa_id)
                ->where('codigo', $request->codigo)->first();
                if($funcionario != null && $item->codigo != $funcionario->codigo){
                    session()->flash("flash_error", "Já existe um funcionário com esse código!");
                    return redirect()->back();
                }
            }
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Funcionario', 'editar', $request->nome);
            session()->flash("flash_success", "Funcionário atualizado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Funcionario', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possivel fazer o cadastro " . $e->getMessage());
        }
        return redirect()->route('funcionarios.index');
    }

    public function destroy($id)
    {
        $item = Funcionario::findOrFail($id);
        try {
            
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Funcionario', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Removido com Sucesso");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Funcionario', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possivel deletar" . $e->getMessage());
        }
        return redirect()->route('funcionarios.index');
    }

    public function atribuir($id)
    {
        $item = Funcionario::findOrFail($id);

        $funcionarioServico = FuncionarioServico::where('empresa_id', request()->empresa_id)
        ->pluck('servico_id')->all();
        $servicos = Servico::whereNotIn('id', $funcionarioServico)
        ->where('empresa_id', request()->empresa_id)->get();
        $data = FuncionarioServico::where('funcionario_id', $item->id)->get();

        return view('funcionario.atribuir', compact('item', 'servicos', 'data'));
    }

    public function atribuirServico(Request $request)
    {
        try {
            $data = $request->except(['_token']);
            FuncionarioServico::updateOrCreate($data);
            session()->flash("flash_success", "Atribuído com Sucesso");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function deletarAtribuicao($id)
    {
        $item = FuncionarioServico::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Deletado atribuição com Sucesso");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }
}
