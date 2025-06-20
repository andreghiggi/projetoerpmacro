<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Empresa;
use App\Utils\EmpresaUtil;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ControleAcessoController extends Controller
{

    protected $empresaUtil;

    public function __construct(EmpresaUtil $empresaUtil)
    {
        $this->empresaUtil = $empresaUtil;
        $this->middleware('permission:controle_acesso_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:controle_acesso_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:controle_acesso_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:controle_acesso_delete', ['only' => ['destroy']]);
    }

    private function validaPermissoes($empresa_id){
        $empresa = Empresa::findOrFail($empresa_id);

        if(sizeof($empresa->roles) == 0){
            // se não tiver adiciona os padrões
            $this->empresaUtil->defaultPermissions($empresa_id);
        }
        foreach($empresa->usuarios as $u){

            $user = $u->usuario;
            if(sizeof($user->roles) == 0){
                $user->assignRole($empresa->roles[0]->name);
            }
        }

    }

    public function index(Request $request)
    {
        $this->validaPermissoes($request->empresa_id);
        $data = Role::orderBy('id', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->when(!empty($request->descricao), function ($q) use ($request) {
            return $q->where('description', 'LIKE', "%$request->descricao%");
        })
        ->paginate(30);

        return view('controle_acesso.index', compact('data'));
    }

    public function create(){

        $permissions = $this->empresaUtil->getPermissions(request()->empresa_id);
        return view('controle_acesso.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'type_user' => 2,
            'name' => $request->name."#".$request->empresa_id
        ]);

        $this->__validate($request);
        try{

            $item = Role::create($request->except('permissions'));

            $item->permissions()->attach($request->permissions);
            session()->flash("flash_success", 'Registro criado com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('controle-acesso.index');
    }

    public function edit($id){

        $permissions = $this->empresaUtil->getPermissions(request()->empresa_id);
        $item = Role::findOrFail($id);

        return view('controle_acesso.edit', compact('permissions', 'item'));
    }

    public function update(Request $request, $id)
    {
        $item = Role::findOrFail($id);

        // $this->__validate($request);

        try{
            $item->fill($request->except('permissions'))->save();
            $item->permissions()->sync($request->permissions);
            session()->flash("flash_success", 'Registro atualizado com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('controle-acesso.index');

    }

    private function __validate(Request $request)
    {
        $rules = [
            'name' => \Illuminate\Validation\Rule::unique('roles')->ignore($request->id),
        ];

        $messages = [
            'name.unique' => 'Nome já existe, defina outro nome',
        ];
        $this->validate($request, $rules, $messages);
    }

    public function destroy($id)
    {
        $item = Role::findOrFail($id);
        __validaObjetoEmpresa($item);

        try {
            $item->delete();
            session()->flash("flash_success", 'Registro removido com sucesso.');
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }
}
