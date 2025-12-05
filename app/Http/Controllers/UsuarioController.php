<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Empresa;
use App\Models\UsuarioEmpresa;
use App\Models\UsuarioLocalizacao;
use App\Utils\UploadUtil;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:usuarios_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:usuarios_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:usuarios_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:usuarios_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->when(!empty($request->name), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('name', 'LIKE', "%$request->name%");
            });
        })
        ->paginate(__itensPagina());

        return view('usuarios.index', compact('data'));
    }

    public function create(Request $request)
    {
        $roles = Role::orderBy('name', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->get();
        $count = UsuarioEmpresa::where('empresa_id', request()->empresa_id)->count();
        $count++;
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $plano = $empresa->plano;

        if($count >= $plano->plano->maximo_usuarios){
            session()->flash("flash_warning", "Limite de usuários atingido!");
            return redirect()->back();
        }
        return view('usuarios.create', compact('roles'));
    }

    public function edit(Request $request, $id)
    {
        $item = User::findOrFail($id);

        if($item->empresa == null){
            abort(403);
        }

        $roles = Role::orderBy('name', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->get();
        $passwdHidden = 1;

        if(!__isMaster()){
            __validaObjetoEmpresa($item->empresa);
        }else{
            $passwdHidden = 0;
        }
        return view('usuarios.edit', compact('item', 'roles', 'passwdHidden'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {
            $file_name = '';
            if ($request->hasFile('image')) {
                $file_name = $this->util->uploadImage($request, '/usuarios');
            }
            $request->merge([
                'password' => Hash::make($request['password']),
                'imagem' => $file_name
            ]);
            $usuario = User::create($request->all());

            UsuarioEmpresa::create([
                'empresa_id' => $request->empresa_id,
                'usuario_id' => $usuario->id
            ]);

            $role = Role::findOrFail($request->role_id);
            $usuario->assignRole($role->name);

            if(isset($request->locais)){
                for($i=0; $i<sizeof($request->locais); $i++){
                    UsuarioLocalizacao::updateOrCreate([
                        'usuario_id' => $usuario->id,
                        'localizacao_id' => $request->locais[$i]
                    ]);
                }
            }
            __createLog($request->empresa_id, 'Usuário', 'cadastrar', $request->name);
            session()->flash("flash_success", "Usuário cadastrado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Usuário', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('usuarios.index');
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

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        try {
            $file_name = $usuario->imagem;

            if ($request->hasFile('image')) {
                $this->util->unlinkImage($usuario, '/usuarios');
                $file_name = $this->util->uploadImage($request, '/usuarios');
            }
            if ($request->password) {
                $request->merge([
                    'password' => Hash::make($request->password),
                    'imagem' => $file_name
                ]);
            } else {
                $request->merge([
                    'password' => $usuario->password,
                    'imagem' => $file_name
                ]);
            }

            if(__isMaster()){
                if ($request->nova_senha) {
                    $request->merge([
                        'password' => Hash::make($request->nova_senha)
                    ]);
                }
            }
            $usuario->fill($request->all())->save();

            $role = Role::findOrFail($request->role_id);
            $user_role = $usuario->roles->first();
            foreach($usuario->roles as $r){
                $usuario->removeRole($r->name);
            }
            $usuario->assignRole($role->name);

            if(isset($request->locais)){
                $usuario->locais()->delete();
                for($i=0; $i<sizeof($request->locais); $i++){
                    UsuarioLocalizacao::updateOrCreate([
                        'usuario_id' => $usuario->id,
                        'localizacao_id' => $request->locais[$i]
                    ]);
                }
            }
            session()->flash("flash_success", "Usuário alterado!");

            if($usuario->empresa){
                __createLog($request->empresa_id, 'Usuário', 'editar', $request->name);
            }else{
                return redirect()->route('usuarios.profile', [$usuario->id]);
            }
        } catch (\Exception $e) {
            if($usuario->empresa){
                __createLog($request->empresa_id, 'Usuário', 'erro', $e->getMessage());
            }
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('usuarios.index');
    }

    public function destroy($id)
    {
        $item = User::findOrFail($id);
        __validaObjetoEmpresa($item->empresa);

        try {

            $descricaoLog = $item->name;

            $item->empresa->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Usuário', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Usuário removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Usuário', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function profile($id)
    {
        $item = User::findOrFail($id);
        if($item->empresa == null){
            abort(403);
        }
        __validaObjetoEmpresa($item->empresa);
        return view('usuarios.profile', compact('item'));
    }

    public function show($id){
        if(!__isAdmin()){
            session()->flash("flash_error", "Acesso permitido somente para administradores");
            return redirect()->back();
        }
        $item = User::findOrFail($id);
        return view('usuarios.show', compact('item'));
    }

    public function alterSenha($id)
    {
        $item = User::findOrFail($id);
        return view('usuarios.alterar_senha', compact('item'));
    }

    public function updateSenha(Request $request, $id){
        $item = User::findOrFail($id);
        if($request->senha == $request->repita_senha){
            $item->password = Hash::make($request->senha);
            $item->save();
            session()->flash("flash_success", "Senha alterada com sucesso!");
            return redirect()->route('usuarios.index');
        }else{
            session()->flash("flash_error", "Senhas não coencidem!");
            return redirect()->back();
        }
    }

}
