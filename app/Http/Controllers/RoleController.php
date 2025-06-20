<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Empresa;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{

    public function index(Request $request)
    {
        $empresa = $request->empresa;

        $data = Role::orderBy('id', 'desc')
        ->when($empresa, function ($q) use ($empresa) {
            return $q->where('empresa_id', $empresa);
        })
        ->when(!empty($request->descricao), function ($q) use ($request) {
            return $q->where('description', 'LIKE', "%$request->descricao%");
        })
        ->paginate(30);
        if($empresa){
            $empresa = Empresa::findOrFail($empresa);
        }else{
            $empresa = null;
        }

        return view('roles.index', compact('data', 'empresa'));
    }

    public function create(){
        $permissions = Permission::orderBy('description')->get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        Validator::make(
            $request->all(),
            $this->rules($request)
        )->validate();

        try{
            $request->merge([
                'type_user' => 2
            ]);
            $item = Role::create($request->except('permissions'));

            $item->permissions()->attach($request->permissions);
            session()->flash("flash_success", 'Registro criado com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('roles.index');
    }

    public function edit($id){
        $item = Role::findOrFail($id);
        if($item->name == 'gestor_plataforma'){
            session()->flash("flash_error", 'Não é permitido editar esse registro!');
            return redirect()->route('roles.index');
        }
        $permissions = Permission::orderBy('description')->get();

        return view('roles.edit', compact('item', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $item = Role::findOrFail($id);

        Validator::make(
            $request->all(),
            $this->rules($request, $item->getKey())
        )->validate();

        try{

            $item->fill($request->except(['permissions', 'empresa_id']))->save();
            $item->permissions()->sync($request->permissions);

            session()->flash("flash_success", 'Registro atualizado com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('roles.index');

    }

    public function destroy($id)
    {
        $item = Role::findOrFail($id);

        try {
            $item->delete();
            session()->flash("flash_success", 'Registro removido com sucesso.');
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('roles.index');
    }

    private function rules(Request $request, $primaryKey = null, bool $changeMessages = false)
    {
        $rules = [
            'name' => ['required', 'max:40'],
            'description' => ['required', 'max:40'],
            'permissions' => ['required']
        ];

        if (empty($primaryKey)) {
            $rules['name'][] = Rule::unique('roles');
        } else {
            $rules['name'][] = Rule::unique('roles')->ignore($primaryKey);
        }

        $messages = [];

        return !$changeMessages ? $rules : $messages;
    }

}
