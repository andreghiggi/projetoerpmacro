<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{

    public function index(Request $request){
        $data = Permission::orderBy('description')
        ->when(!empty($request->descricao), function ($q) use ($request) {
            return $q->where('description', 'LIKE', "%$request->descricao%");
        })
        ->paginate(30);

        return view('permissions.index', compact('data'));
    }

    public function create(){
        return view('permissions.create');
    }

    public function store(Request $request){
        Validator::make(
            $request->all(),
            $this->rules($request)
        )->validate();

        try{
            Permission::create($request->all());
            session()->flash("flash_success", 'Registro criado com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('permissions.index');
    }

    public function edit($id){
        $item = Permission::findOrFail($id);

        return view('permissions.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Permission::findOrFail($id);

        Validator::make(
            $request->all(),
            $this->rules($request, $item->getKey())
        )->validate();

        try{

            $item->fill($request->all())->save();
            session()->flash("flash_success", 'Registro atualizado com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('permissions.index');
    }

    public function destroy($id)
    {
        $item = Permission::findOrFail($id);

        try {
            $item->delete();
            session()->flash("flash_success", 'Registro removido com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('permissions.index');

    }

    private function rules(Request $request, $primaryKey = null, bool $changeMessages = false)
    {
        $rules = [
            'name' => ['required', 'max:50'],
            'description' => ['required', 'max:100']
        ];

        if (empty($primaryKey)) {
            $rules['name'][] = Rule::unique('permissions');
        } else {
            $rules['name'][] = Rule::unique('permissions')->ignore($primaryKey);
        }

        $messages = [];

        return !$changeMessages ? $rules : $messages;
    }

    public function updateAll(){
        $permissions = Permission::defaultPermissions();
        foreach ($permissions as $permission) {
            $p = Permission::where('name', $permission['name'])->first();
            if($p == null){
                Permission::create(
                    [
                        'name' => $permission['name'],
                        'description' => $permission['description'],
                    ]
                );
            }
        }
        session()->flash("flash_success", 'Permissões atualizadas.');
        return redirect()->route('permissions.index');
    }
}
