<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use App\Models\Funcionario;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:veiculos_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:veiculos_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:veiculos_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:veiculos_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Veiculo::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->placa), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('placa', 'LIKE', "%$request->placa%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());
        return view('veiculos.index', compact('data'));
    }

    
    public function create()
    {
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->get();
        return view('veiculos.create', compact('funcionarios'));
    }

    public function store(Request $request)
    {
        try{
            $request->merge([
                'rntrc' => $request->rntrc ?? 0,
                'taf' => $request->taf ?? 0,
                'renavam' => $request->renavam ?? '',
                'numero_registro_estadual' => $request->numero_registro_estadual ?? '',
                'tipo' => $request->tipo ?? '',
                'tipo_carroceria' => $request->tipo_carroceria ?? '',
                'tipo_rodado' => $request->tipo_rodado ?? '',
                'proprietario_ie' => $request->proprietario_ie ?? ''
            ]);
            Veiculo::create($request->all());
            session()->flash("flash_success", "Cadastrado com Sucesso");
        }catch(\Exception $e){
            session()->flash("flash_error", "Não foi possivel fazer o cadastro" . $e->getMessage());
        }
        return redirect()->route('veiculos.index');
    }   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Veiculo::findOrFail($id);
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->get();
        return view('veiculos.edit', compact('item', 'funcionarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        $item = Veiculo::findOrFail($id);
        try{
            $request->merge([
                'rntrc' => $request->rntrc ?? 0,
                'taf' => $request->taf ?? 0,
                'renavam' => $request->renavam ?? '',
                'numero_registro_estadual' => $request->numero_registro_estadual ?? '',
                'tipo' => $request->tipo ?? '',
                'tipo_carroceria' => $request->tipo_carroceria ?? '',
                'tipo_rodado' => $request->tipo_rodado ?? '',
                'proprietario_ie' => $request->proprietario_ie ?? ''
            ]);
            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Alerado com sucesso!');
        }catch(\Exception $e){
            session()->flash('flash_error', 'Não foi possível fazer a alteração' . $e->getMessage());
        }
        return redirect()->route('veiculos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $item = Veiculo::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('veiculos.index');
    }
}
