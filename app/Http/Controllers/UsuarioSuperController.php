<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Role;
use App\Models\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioSuperController extends Controller
{
    public function index(Request $request)
    {
        $data = User::when(!empty($request->name), function ($q) use ($request) {
            return $q->where('name', 'LIKE', "%$request->name%");
        })
        ->select('users.*')
        ->when(!empty($request->email), function ($q) use ($request) {
            return $q->where('email', 'LIKE', "%$request->email%");
        })
        ->when(!empty($request->tipo_contador), function ($q) use ($request) {
            return $q->where('tipo_contador', $request->tipo_contador);
        })
        ->when(!empty($request->empresa), function ($q) use ($request) {
            return $q->where('usuario_empresas.empresa_id', $request->empresa)
            ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'users.id');
        })
        ->paginate(__itensPagina());

        $empresa = null;
        if($request->empresa){
            $empresa = Empresa::findOrFail($request->empresa);
        }
        return view('usuarios_super.index', compact('data', 'empresa'));
    }

    public function create()
    {

        $empresas = Empresa::all();
        return view('usuarios_super.create', compact('empresas'));
    }

    public function edit($id)
    {
        $item = User::findOrFail($id);

        $empresas = Empresa::all();

        $roles = [];
        if($item->empresa){
            $roles = Role::orderBy('name', 'desc')
            ->where('empresa_id', $item->empresa->empresa_id)
            ->get();
        }
        return view('usuarios_super.edit', compact('item', 'empresas', 'roles'));
    }

    public function destroy($id){
        $item = User::findOrFail($id);
        try {
            $item->acessos()->delete();
            $item->locais()->delete();
            if($item->empresa){
                $item->empresa->delete();
            }

            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        try {

            if ($request->password) {
                $request->merge([
                    'password' => Hash::make($request->password),
                ]);
            }else{
                $request->merge([
                    'password' => $usuario->password,
                ]);
            }

            if($request->empresa){
                UsuarioEmpresa::create([
                    'empresa_id' => $request->empresa,
                    'usuario_id' => $id
                ]);
            }
            $usuario->fill($request->all())->save();

            $role = Role::findOrFail($request->role_id);
            foreach($usuario->roles as $r){
                $usuario->removeRole($r->name);
            }
            $usuario->assignRole($role->name);
            session()->flash("flash_success", "Usuário alterado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('usuario-super.index');
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {
            // dd($request->all());

            $usuario = User::create([
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'name' => $request->name,
                'suporte' => $request->suporte,
            ]);


            if($request->empresa){
                UsuarioEmpresa::create([
                    'empresa_id' => $request->empresa,
                    'usuario_id' => $usuario->id
                ]);
            }

            session()->flash("flash_success", "Usuário criado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('usuario-super.index');
    }

    private function __validate(Request $request)
    {
        $rules = [
            'email' => 'unique:users',
        ];

        $messages = [
            'email.unique' => 'Este email já esta em uso!',
        ];
        $this->validate($request, $rules, $messages);
    }
}
