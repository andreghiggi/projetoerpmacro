<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\UsuarioEmpresa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RepresentanteController extends Controller
{
    public function index(Request $request)
    {
        $data = Empresa::when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->when(!empty($request->cpf_cnpj), function ($q) use ($request) {
            return $q->where('cpf_cnpj', 'LIKE', "%$request->cpf_cnpj%");
        })
        ->where('tipo_representante', 1)
        ->paginate(__itensPagina());

        return view('representantes.index', compact('data'));
    }

    public function create()
    {
        return view('representantes.create');
    }

    public function edit($id)
    {
        $item = Empresa::findOrFail($id);

        return view('representantes.edit', compact('item'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {

            DB::transaction(function () use ($request) {

                $email = $request->email;
                $request->merge([
                    'email' => $request->email_empresa,
                    'tipo_contador' => $request->tipo_contador,
                    'tipo_representante' => 1
                ]);

                $empresa = Empresa::create($request->all());

                if ($request->usuario) {

                    $usuario = User::create([
                        'name' => $request->usuario ?? null,
                        'email' => $email ?? null,
                        'password' => Hash::make($request['password']) ?? '',
                        'remember_token' => Hash::make($request['remember_token']) ?? '',
                        'tipo_contador' => 1
                    ]);

                    UsuarioEmpresa::create([
                        'empresa_id' => $empresa->id,
                        'usuario_id' => $usuario->id ?? null
                    ]);
                }
                return true;
            });
            session()->flash("flash_success", "Representante cadastrado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('representantes.index');
    }

    public function update(Request $request, $id)
    {
        $item = Empresa::findOrFail($id);

        try {
            $request->merge([
                'percentual_comissao' => __convert_value_bd($request->percentual_comissao)
            ]);
            
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Representante atualizado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('representantes.index');
    }

    private function __validate(Request $request)
    {
        $rules = [
            'nome' => 'required',
            'cpf_cnpj' => 'required',
            'ie' => 'required',
            'celular' => 'required',
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade_id' => 'required',
            'email' => 'unique:users',
        ];
        $messages = [
            'nome.required' => 'Campo Obrigatório',
            'cpf_cnpj.required' => 'Campo Obrigatório',
            'ie.required' => 'Campo Obrigatório',
            'email.required' => 'Campo Obrigatório',
            'celular.required' => 'Campo Obrigatório',
            'csc.required' => 'Campo Obrigatório',
            'csc_id.required' => 'Campo Obrigatório',
            'cep.required' => 'Campo Obrigatório',
            'rua.required' => 'Campo Obrigatório',
            'numero.required' => 'Campo Obrigatório',
            'bairro.required' => 'Campo Obrigatório',
            'cidade_id.required' => 'Campo Obrigatório',
            'numero_ultima_nfe_producao.required' => 'Campo Obrigatório',
            'numero_ultima_nfe_homologacao.required' => 'Campo Obrigatório',
            'numero_serie_nfe.required' => 'Campo Obrigatório',
            'numero_ultima_nfce_producao.required' => 'Campo Obrigatório',
            'numero_ultima_nfce_homologacao.required' => 'Campo Obrigatório',
            'numero_serie_nfce.required' => 'Campo Obrigatório',
            'email.unique' => 'Já existe um usuário com este email',
        ];
        $this->validate($request, $rules, $messages);
    }

    public function destroy($id)
    {

        $item = Empresa::findOrFail($id);
        foreach($item->usuarios as $u){
            $u->usuario->acessos()->delete();
        }
        $item->usuarios()->delete();
        // $item->user()->delete();
        $item->plano()->delete();
        \App\Models\Role::where('empresa_id', $id)->delete();
        RepresentanteEmpresa::where('contador_id', $id)->delete();
        try {
            $item->delete();
            session()->flash("flash_success", "Contador removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function show($id)
    {
        $item = Empresa::findOrFail($id);
        return view('contadores.show', compact('item'));
    }
}
